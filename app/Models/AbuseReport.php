<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbuseReport extends Model
{
    protected $fillable = [
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'reported_phone',
        'reason',
        'description',
        'evidence',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'ip_address',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Available report reasons.
     */
    public const REASONS = [
        'spam' => 'Spam / Pesan Berulang',
        'scam' => 'Penipuan',
        'harassment' => 'Pelecehan',
        'gambling' => 'Judi Online',
        'adult' => 'Konten Dewasa',
        'other' => 'Lainnya',
    ];

    /**
     * Status labels for display.
     */
    public const STATUS_LABELS = [
        'pending' => 'Menunggu Review',
        'investigating' => 'Sedang Investigasi',
        'resolved' => 'Telah Diselesaikan',
        'dismissed' => 'Ditolak',
    ];

    /**
     * Get the admin who resolved this report.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope to get pending reports.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get unresolved reports.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'investigating']);
    }

    /**
     * Mark report as investigating.
     */
    public function markAsInvestigating(): void
    {
        $this->update(['status' => 'investigating']);
    }

    /**
     * Resolve the report.
     */
    public function resolve(int $adminId, string $notes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $adminId,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Dismiss the report.
     */
    public function dismiss(int $adminId, string $reason): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolved_by' => $adminId,
            'resolution_notes' => $reason,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get count of reports for a specific phone number.
     */
    public static function countForPhone(string $phone): int
    {
        return self::where('reported_phone', $phone)->count();
    }

    /**
     * Check if phone has been reported multiple times (indicates bad actor).
     */
    public static function isFlagged(string $phone, int $threshold = 3): bool
    {
        return self::countForPhone($phone) >= $threshold;
    }
}
