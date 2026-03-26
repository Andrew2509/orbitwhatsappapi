<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class BlacklistedWord extends Model
{
    protected $fillable = [
        'word',
        'category',
        'severity',
        'reason',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Available categories for blacklisted words.
     */
    public const CATEGORIES = [
        'scam' => 'Penipuan',
        'gambling' => 'Judi Online',
        'adult' => 'Konten Dewasa',
        'drugs' => 'Narkoba',
        'violence' => 'Kekerasan',
        'spam' => 'Spam',
        'general' => 'Umum',
    ];

    /**
     * Get the user who created this word.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get only active words.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all active blacklisted words with caching.
     */
    public static function getAllActive(): array
    {
        return Cache::remember('blacklisted_words_active', 3600, function () {
            return self::active()
                ->select('word', 'category', 'severity')
                ->get()
                ->toArray();
        });
    }

    /**
     * Clear the cache when words are modified.
     */
    public static function clearCache(): void
    {
        Cache::forget('blacklisted_words_active');
    }

    /**
     * Boot method to clear cache on model events.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
