<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'api_key_id',
        'app_key',
        'name',
        'description',
        'webhook_url',
        'is_active',
        'messages_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (empty($application->app_key)) {
                $application->app_key = self::generateAppKey();
            }
        });
    }

    /**
     * Generate a unique App Key
     */
    public static function generateAppKey(): string
    {
        return 'app_' . Str::random(24);
    }

    /**
     * Get masked App Key for display
     */
    public function getMaskedAppKeyAttribute(): string
    {
        return substr($this->app_key, 0, 12) . '••••••••••••';
    }

    /**
     * Increment message count
     */
    public function incrementMessageCount(): void
    {
        $this->increment('messages_count');
        $this->update(['last_used_at' => now()]);
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'application_device')
            ->withTimestamps();
    }
}
