<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'template_id',
        'name',
        'message_type',
        'custom_message',
        'media_path',
        'status',
        'recipients',
        'scheduled_at',
        'delay_min',
        'delay_max',
        'batch_size',
        'batch_delay',
        'rotation_strategy',
        'current_device_index',
        'current_batch',
        'total_recipients',
        'sent_count',
        'failed_count',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function campaignRecipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    /**
     * Get all devices assigned to this campaign (for multi-device rotation).
     */
    public function campaignDevices(): HasMany
    {
        return $this->hasMany(CampaignDevice::class);
    }

    /**
     * Get devices through the pivot table.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'campaign_devices')
            ->withPivot(['messages_sent', 'priority', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Check if campaign has multiple devices configured.
     */
    public function hasMultipleDevices(): bool
    {
        return $this->campaignDevices()->count() > 1;
    }

    /**
     * Get total devices assigned to this campaign.
     */
    public function getDeviceCount(): int
    {
        return $this->campaignDevices()->count();
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->total_recipients === 0) return 0;
        return round((($this->sent_count + $this->failed_count) / $this->total_recipients) * 100, 1);
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Get the message content based on message_type.
     */
    public function getMessageContent(): ?string
    {
        if ($this->message_type === 'template' && $this->template) {
            return $this->template->content;
        }
        return $this->custom_message;
    }

    /**
     * Process spintax in message content.
     * Example: "{Hello|Hi|Hey} there!" becomes one of "Hello there!", "Hi there!", or "Hey there!"
     */
    public static function processSpintax(string $text): string
    {
        return preg_replace_callback('/\{([^{}]+)\}/', function ($matches) {
            $options = explode('|', $matches[1]);
            return $options[array_rand($options)];
        }, $text);
    }

    /**
     * Get random delay between min and max.
     */
    public function getRandomDelay(): int
    {
        return rand($this->delay_min, $this->delay_max);
    }

    /**
     * Check if we need a batch pause (every batch_size messages).
     */
    public function needsBatchPause(int $messageIndex): bool
    {
        return $this->batch_size > 0 && $messageIndex > 0 && ($messageIndex % $this->batch_size) === 0;
    }
}
