<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoReply extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'keyword',
        'match_type',
        'reply_type',
        'template_id',
        'reply_value',
        'is_active',
        'priority',
        'triggered_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function matches(string $message): bool
    {
        return match ($this->match_type) {
            'exact' => strtolower($message) === strtolower($this->keyword),
            'contains' => str_contains(strtolower($message), strtolower($this->keyword)),
            'starts_with' => str_starts_with(strtolower($message), strtolower($this->keyword)),
            'regex' => (bool) preg_match($this->keyword, $message),
            default => false,
        };
    }

    /**
     * Get the actual reply content based on reply_type.
     * If template, fetch content from the linked template.
     * If text, return the reply_value directly.
     *
     * @param array $variables Optional variables to replace in template
     * @return string|null
     */
    public function getReplyContent(array $variables = []): ?string
    {
        if ($this->reply_type === 'template' && $this->template) {
            return $this->template->render($variables);
        }

        return $this->reply_value;
    }

    public function incrementTrigger(): void
    {
        $this->increment('triggered_count');
    }
}
