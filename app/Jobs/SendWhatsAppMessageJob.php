<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Device;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    /**
     * Priority levels for queue routing.
     */
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_DEFAULT = 'default';
    public const PRIORITY_LOW = 'low';

    public function __construct(
        public Message $message,
        public string $priority = self::PRIORITY_DEFAULT
    ) {
        // Auto-route to appropriate queue based on priority
        $this->onQueue($priority);
    }

    /**
     * Create a high-priority job (for OTP/single API sends).
     */
    public static function highPriority(Message $message): self
    {
        return new self($message, self::PRIORITY_HIGH);
    }

    /**
     * Create a low-priority job (for broadcast/bulk sends).
     */
    public static function lowPriority(Message $message): self
    {
        return new self($message, self::PRIORITY_LOW);
    }

    public function handle(WhatsAppService $whatsApp): void
    {
        $device = $this->message->device;
        
        // Check device connection
        if (!$device || $device->status !== 'connected') {
            $this->message->update([
                'status' => 'failed',
                'error_message' => 'Device not connected'
            ]);
            return;
        }

        // Check daily limit (Anti-Ban feature)
        if (!$device->canSendMessage()) {
            $usage = $device->getTodayUsage();
            
            if ($usage->is_blocked) {
                $this->message->update([
                    'status' => 'failed',
                    'error_message' => 'Daily limit reached. Device blocked until tomorrow.'
                ]);
                Log::warning('Message blocked: Daily limit reached', [
                    'device_id' => $device->id,
                    'messages_sent' => $usage->messages_sent,
                    'limit' => $usage->messages_limit,
                ]);
                return;
            }
            
            if ($usage->cooldown_until && now()->lt($usage->cooldown_until)) {
                // Re-queue to try again after cooldown
                $remainingSeconds = now()->diffInSeconds($usage->cooldown_until);
                $this->release($remainingSeconds);
                return;
            }
        }

        try {
            $phone = $this->message->contact?->phone_number;
            if (!$phone) {
                throw new \Exception('No phone number');
            }

            $result = $whatsApp->sendMessage(
                $device->id,
                $phone,
                $this->message->content,
                $this->message->type,
                $this->message->media_url
            );

            if ($result['success']) {
                $this->message->update([
                    'status' => 'sent',
                    'external_id' => $result['messageId'],
                    'sent_at' => now(),
                ]);
                
                // Increment both counters (legacy + new daily limit)
                $device->increment('messages_sent');
                $device->incrementDailyUsage();
                
                Log::info('Message sent successfully', [
                    'message_id' => $this->message->id,
                    'external_id' => $result['messageId'],
                    'priority' => $this->priority,
                    'remaining_today' => $device->getRemainingMessagesToday(),
                ]);
            } else {
                throw new \Exception($result['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            Log::error('Message send failed', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
                'priority' => $this->priority,
            ]);
            
            if ($this->attempts() >= $this->tries) {
                $this->message->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            } else {
                throw $e; // Retry
            }
        }
    }

    /**
     * Get the queue priority for display.
     */
    public function getPriority(): string
    {
        return $this->priority;
    }
}
