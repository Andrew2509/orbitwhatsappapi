<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceUsageLimit extends Model
{
    protected $fillable = [
        'device_id',
        'date',
        'messages_sent',
        'messages_limit',
        'warning_threshold',
        'is_blocked',
        'cooldown_until',
    ];

    protected $casts = [
        'date' => 'date',
        'is_blocked' => 'boolean',
        'cooldown_until' => 'datetime',
    ];

    /**
     * Get the device that owns this usage limit.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get today's usage record for a device, create if not exists.
     */
    public static function getOrCreateToday(int $deviceId): self
    {
        return self::firstOrCreate(
            [
                'device_id' => $deviceId,
                'date' => now()->toDateString(),
            ],
            [
                'messages_sent' => 0,
                'messages_limit' => config('whatsapp.daily_message_limit', 200),
                'warning_threshold' => config('whatsapp.warning_threshold', 80),
            ]
        );
    }

    /**
     * Check if the device can send more messages today.
     */
    public function canSend(): bool
    {
        if ($this->is_blocked) {
            return false;
        }

        if ($this->cooldown_until && now()->lt($this->cooldown_until)) {
            return false;
        }

        return $this->messages_sent < $this->messages_limit;
    }

    /**
     * Get remaining messages for today.
     */
    public function getRemainingMessages(): int
    {
        return max(0, $this->messages_limit - $this->messages_sent);
    }

    /**
     * Get usage percentage.
     */
    public function getUsagePercentage(): float
    {
        if ($this->messages_limit === 0) {
            return 100;
        }
        return round(($this->messages_sent / $this->messages_limit) * 100, 1);
    }

    /**
     * Check if usage is at warning level.
     */
    public function isAtWarningLevel(): bool
    {
        return $this->getUsagePercentage() >= $this->warning_threshold;
    }

    /**
     * Increment the messages sent counter.
     */
    public function incrementSent(int $count = 1): void
    {
        $this->increment('messages_sent', $count);
        
        // Auto-block if limit reached
        if ($this->messages_sent >= $this->messages_limit) {
            $this->update(['is_blocked' => true]);
        }
    }

    /**
     * Set cooldown period for the device.
     */
    public function setCooldown(int $minutes = 60): void
    {
        $this->update([
            'cooldown_until' => now()->addMinutes($minutes),
        ]);
    }
}
