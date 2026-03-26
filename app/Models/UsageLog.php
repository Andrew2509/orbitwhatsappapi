<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'messages_sent',
        'api_calls',
        'devices_connected',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForMonth($query, int $month = null, int $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public static function getOrCreateForToday(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'date' => now()->toDateString()],
            ['messages_sent' => 0, 'api_calls' => 0, 'devices_connected' => 0]
        );
    }

    public function incrementMessages(int $count = 1): void
    {
        $this->increment('messages_sent', $count);
    }

    public function incrementApiCalls(int $count = 1): void
    {
        $this->increment('api_calls', $count);
    }

    public function updateDevicesCount(int $count): void
    {
        $this->update(['devices_connected' => $count]);
    }
}
