<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Service for logging sensitive actions in the system.
 * 
 * Usage:
 * AuditService::log('webhook.updated', $webhook, $oldValues);
 * AuditService::log('api_key.created', $apiKey);
 */
class AuditService
{
    /**
     * Log an action to the audit trail.
     *
     * @param string $action Action identifier (e.g., 'webhook.updated')
     * @param Model $model The model being audited
     * @param array|null $oldValues Previous values (for updates)
     * @param string|null $description Optional human-readable description
     */
    public static function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?string $description = null
    ): AuditLog {
        $user = Auth::user();
        $request = Request::instance();

        // Calculate new values (current model attributes)
        $newValues = $model->getAttributes();

        // For deletes, old values are the current values
        if (str_contains($action, 'deleted')) {
            $oldValues = $newValues;
            $newValues = null;
        }

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'description' => $description,
        ]);
    }

    /**
     * Log a simple action without model comparison.
     */
    public static function logAction(
        string $action,
        ?Model $model = null,
        ?array $data = null,
        ?string $description = null
    ): AuditLog {
        $user = Auth::user();
        $request = Request::instance();

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => $model ? get_class($model) : 'System',
            'auditable_id' => $model?->getKey() ?? 0,
            'old_values' => null,
            'new_values' => $data,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'description' => $description,
        ]);
    }

    /**
     * Log user login.
     */
    public static function logLogin(): void
    {
        if (Auth::check()) {
            self::logAction('user.login', Auth::user(), null, 'User logged in');
        }
    }

    /**
     * Log user logout.
     */
    public static function logLogout(): void
    {
        if (Auth::check()) {
            self::logAction('user.logout', Auth::user(), null, 'User logged out');
        }
    }

    /**
     * Get recent audit logs for a user.
     */
    public static function getRecentForUser(int $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific model.
     */
    public static function getForModel(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->getKey())
            ->orderByDesc('created_at')
            ->get();
    }
}
