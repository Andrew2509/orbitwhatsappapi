<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\DeviceRotationService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 3600; // 1 hour max

    public function __construct(
        public Campaign $campaign
    ) {}

    public function handle(WhatsAppService $whatsApp, DeviceRotationService $rotation): void
    {
        $campaign = $this->campaign->fresh();
        
        // Check if campaign is still running
        if ($campaign->status !== 'running') {
            Log::info("Campaign {$campaign->id} is not running, skipping.");
            return;
        }

        // Check for available devices (multi-device or single device fallback)
        $hasMultiDevices = $campaign->campaignDevices()->exists();
        
        if (!$hasMultiDevices) {
            // Fallback to single device mode (legacy)
            $this->processSingleDevice($campaign, $whatsApp);
            return;
        }

        // Multi-device mode with rotation
        $this->processMultiDevice($campaign, $whatsApp, $rotation);
    }

    /**
     * Process campaign with a single device (legacy mode).
     */
    protected function processSingleDevice(Campaign $campaign, WhatsAppService $whatsApp): void
    {
        $device = $campaign->device;
        if (!$device || $device->status !== 'connected') {
            $campaign->update(['status' => 'paused']);
            Log::error("Campaign {$campaign->id}: Device not connected.");
            return;
        }

        // Check device daily limit
        if (!$device->canSendMessage()) {
            $campaign->update(['status' => 'paused']);
            Log::warning("Campaign {$campaign->id}: Device {$device->id} reached daily limit.");
            return;
        }

        $this->sendMessages($campaign, $device, $whatsApp, null);
    }

    /**
     * Process campaign with multiple devices and rotation.
     */
    protected function processMultiDevice(Campaign $campaign, WhatsAppService $whatsApp, DeviceRotationService $rotation): void
    {
        // Check if all devices are at limit
        if ($rotation->allDevicesAtLimit($campaign)) {
            $campaign->update(['status' => 'paused']);
            Log::warning("Campaign {$campaign->id}: All devices reached daily limit. Pausing campaign.");
            return;
        }

        $this->sendMessages($campaign, null, $whatsApp, $rotation);
    }

    /**
     * Send messages to recipients.
     */
    protected function sendMessages(Campaign $campaign, $device, WhatsAppService $whatsApp, ?DeviceRotationService $rotation): void
    {
        // Get pending recipients
        $recipients = $campaign->campaignRecipients()
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        if ($recipients->isEmpty()) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            Log::info("Campaign {$campaign->id} completed.");
            return;
        }

        $messageTemplate = $campaign->getMessageContent();
        if (!$messageTemplate) {
            $campaign->update(['status' => 'failed']);
            Log::error("Campaign {$campaign->id}: No message content.");
            return;
        }

        $processedCount = 0;
        $batchCount = 0;
        $consecutiveFailures = 0;
        $maxConsecutiveFailures = 5;

        foreach ($recipients as $recipient) {
            // Refresh campaign status (check for pause/cancel)
            $campaign->refresh();
            if ($campaign->status !== 'running') {
                Log::info("Campaign {$campaign->id} stopped mid-process.");
                return;
            }

            // Get device (rotation or single)
            $currentDevice = $rotation 
                ? $rotation->getNextDevice($campaign) 
                : $device;

            // No device available (all at limit)
            if (!$currentDevice) {
                $campaign->update(['status' => 'paused']);
                Log::warning("Campaign {$campaign->id}: No devices available. Pausing.");
                return;
            }

            // Check if we need a batch pause
            if ($campaign->needsBatchPause($processedCount)) {
                $batchCount++;
                $campaign->update(['current_batch' => $batchCount]);
                Log::info("Campaign {$campaign->id}: Batch pause for {$campaign->batch_delay} seconds.");
                sleep($campaign->batch_delay);
                
                // Re-check status after batch pause
                $campaign->refresh();
                if ($campaign->status !== 'running') {
                    return;
                }
            }

            try {
                // Mark as queued
                $recipient->markAsQueued();

                // Process spintax and variables
                $message = Campaign::processSpintax($messageTemplate);
                
                // Replace variables if available
                if ($recipient->variables) {
                    foreach ($recipient->variables as $key => $value) {
                        $message = str_replace("{{" . $key . "}}", $value, $message);
                    }
                }
                $message = str_replace("{{nama}}", $recipient->name ?? '', $message);
                $message = str_replace("{{phone}}", $recipient->phone, $message);

                // Send message
                $result = $whatsApp->sendMessage(
                    $currentDevice->id,
                    $recipient->phone,
                    $message,
                    $campaign->media_path ? 'image' : 'text',
                    $campaign->media_path
                );

                if ($result['success']) {
                    $recipient->markAsSent($result['messageId'] ?? 'sent');
                    $campaign->increment('sent_count');
                    
                    // Record send for rotation tracking
                    if ($rotation) {
                        $rotation->recordSend($campaign, $currentDevice);
                    } else {
                        // Single device: increment daily usage
                        $currentDevice->incrementDailyUsage();
                    }
                    
                    $consecutiveFailures = 0;
                    Log::info("Campaign {$campaign->id}: Sent to {$recipient->phone} via Device {$currentDevice->id}");
                } else {
                    throw new \Exception($result['error'] ?? 'Send failed');
                }

            } catch (\Exception $e) {
                $recipient->markAsFailed($e->getMessage());
                $campaign->increment('failed_count');
                $consecutiveFailures++;
                
                Log::error("Campaign {$campaign->id}: Failed to {$recipient->phone} - {$e->getMessage()}");

                // If too many consecutive failures, deactivate device
                if ($rotation && $consecutiveFailures >= $maxConsecutiveFailures) {
                    $rotation->deactivateDevice($campaign, $currentDevice, 'Too many consecutive failures');
                    $consecutiveFailures = 0;
                }
            }

            $processedCount++;

            // Random delay between messages
            $delay = $campaign->getRandomDelay();
            if ($delay > 0) {
                sleep($delay);
            }
        }

        // Check if all done
        $pendingCount = $campaign->campaignRecipients()->where('status', 'pending')->count();
        if ($pendingCount === 0) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            Log::info("Campaign {$campaign->id} completed successfully!");
        }
    }
}

