<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'session_data',
        'status',
        'qr_code',
        'last_connected_at',
        'messages_sent',
        'messages_received',
    ];

    protected $casts = [
        'session_data' => 'array',
        'last_connected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function autoReplies(): HasMany
    {
        return $this->hasMany(AutoReply::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_device')
            ->withTimestamps();
    }

    /**
     * Get all usage limits for this device.
     */
    public function usageLimits(): HasMany
    {
        return $this->hasMany(DeviceUsageLimit::class);
    }

    /**
     * Get today's usage limit for this device.
     */
    public function todayUsage(): HasOne
    {
        return $this->hasOne(DeviceUsageLimit::class)
            ->where('date', now()->toDateString());
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    /**
     * Check if device can send message (connected + not at daily limit).
     */
    public function canSendMessage(): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $usage = $this->getTodayUsage();
        return $usage->canSend();
    }

    /**
     * Get or create today's usage record.
     */
    public function getTodayUsage(): DeviceUsageLimit
    {
        return DeviceUsageLimit::getOrCreateToday($this->id);
    }

    /**
     * Increment daily usage counter.
     */
    public function incrementDailyUsage(int $count = 1): void
    {
        $usage = $this->getTodayUsage();
        $usage->incrementSent($count);
    }

    /**
     * Get remaining messages for today.
     */
    public function getRemainingMessagesToday(): int
    {
        return $this->getTodayUsage()->getRemainingMessages();
    }

    /**
     * Check if device is at warning level for daily usage.
     */
    public function isAtWarningLevel(): bool
    {
        return $this->getTodayUsage()->isAtWarningLevel();
    }
}
