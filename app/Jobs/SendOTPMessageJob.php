<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Contact;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Dedicated job for OTP messages with highest priority.
 * 
 * OTP messages always go to 'high' queue and have:
 * - Fewer retry attempts (2 vs 3)
 * - Shorter timeout (30s vs 60s)
 * - Skip daily limit check (OTP must always go through)
 */
class SendOTPMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;
    public int $backoff = 5;

    public function __construct(
        public int $deviceId,
        public string $phoneNumber,
        public string $otpMessage,
        public ?string $senderName = null
    ) {
        // OTP always goes to high priority queue
        $this->onQueue('high');
    }

    public function handle(WhatsAppService $whatsApp): void
    {
        $device = Device::find($this->deviceId);
        
        if (!$device || $device->status !== 'connected') {
            Log::error('OTP send failed: Device not connected', [
                'device_id' => $this->deviceId,
                'phone' => $this->phoneNumber,
            ]);
            throw new \Exception('Device not connected');
        }

        try {
            $result = $whatsApp->sendMessage(
                $this->deviceId,
                $this->phoneNumber,
                $this->otpMessage,
                'text',
                null
            );

            if ($result['success']) {
                // Create message record for tracking
                $contact = Contact::firstOrCreate(
                    ['phone_number' => $this->phoneNumber],
                    ['name' => $this->senderName ?? 'Unknown']
                );

                Message::create([
                    'device_id' => $this->deviceId,
                    'contact_id' => $contact->id,
                    'direction' => 'outbound',
                    'type' => 'text',
                    'content' => '[OTP] ' . substr($this->otpMessage, 0, 50) . '...',
                    'status' => 'sent',
                    'external_id' => $result['messageId'],
                    'sent_at' => now(),
                ]);

                // Increment counters (OTP still counts towards daily limit for tracking)
                $device->increment('messages_sent');
                $device->incrementDailyUsage();

                Log::info('OTP sent successfully', [
                    'device_id' => $this->deviceId,
                    'phone' => $this->phoneNumber,
                    'message_id' => $result['messageId'],
                ]);
            } else {
                throw new \Exception($result['error'] ?? 'Failed to send OTP');
            }
        } catch (\Exception $e) {
            Log::error('OTP send failed', [
                'device_id' => $this->deviceId,
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            throw $e; // Always retry OTP
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('OTP job completely failed after all retries', [
            'device_id' => $this->deviceId,
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
        ]);
    }
}
