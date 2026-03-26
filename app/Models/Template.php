<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'is_system',
        'name',
        'category',
        'content',
        'variables',
        'buttons',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'variables' => 'array',
        'buttons' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId)->where('is_system', false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function render(array $data = []): string
    {
        $content = $this->content;
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        return $content;
    }
}
