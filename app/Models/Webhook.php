<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'secret',
        'events',
        'is_active',
        'max_retries',
        'last_triggered_at',
        'failure_count',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class)->latest();
    }

    public function subscribesTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    public function markTriggered(): void
    {
        $this->update([
            'last_triggered_at' => now(),
            'failure_count' => 0,
        ]);
    }

    public function incrementFailure(): void
    {
        $this->increment('failure_count');
        
        // Auto-disable after 10 consecutive failures
        if ($this->failure_count >= 10) {
            $this->update(['is_active' => false]);
        }
    }

    public function getSecretKeyAttribute(): string
    {
        return $this->attributes['secret'] ?? '';
    }
}
