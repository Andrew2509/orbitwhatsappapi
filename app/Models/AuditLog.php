<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by auditable model.
     */
    public function scopeForModel($query, string $modelClass, ?int $modelId = null)
    {
        $query->where('auditable_type', $modelClass);
        
        if ($modelId) {
            $query->where('auditable_id', $modelId);
        }

        return $query;
    }

    /**
     * Get formatted action name for display.
     */
    public function getFormattedActionAttribute(): string
    {
        $actions = [
            'webhook.created' => 'Webhook dibuat',
            'webhook.updated' => 'Webhook diubah',
            'webhook.deleted' => 'Webhook dihapus',
            'api_key.created' => 'API Key dibuat',
            'api_key.deleted' => 'API Key dihapus',
            'api_key.regenerated' => 'API Key di-regenerate',
            'device.connected' => 'Device terhubung',
            'device.disconnected' => 'Device terputus',
            'device.deleted' => 'Device dihapus',
            'user.login' => 'Login',
            'user.logout' => 'Logout',
            'user.password_changed' => 'Password diubah',
            'admin.user_plan_changed' => 'Paket user diubah oleh admin',
        ];

        return $actions[$this->action] ?? $this->action;
    }
}
