<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'name',
        'push_name',
        'email',
        'avatar_url',
        'labels',
        'notes',
        'is_blocked',
        'last_message_at',
    ];

    protected $casts = [
        'labels' => 'array',
        'is_blocked' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // Accessors

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->push_name ?? $this->phone_number;
    }
}
