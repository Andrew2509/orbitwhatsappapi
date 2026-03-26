<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceWarmup extends Model
{
    protected $fillable = [
        'device_id',
        'warmup_day',
        'daily_target',
        'current_progress',
        'is_warmup_complete',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'is_warmup_complete' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Warmup schedule: message limits per day.
     */
    public const WARMUP_SCHEDULE = [
        1 => ['limit' => 20, 'description' => 'Kirim 15-20 pesan ke kontak yang sudah kenal'],
        2 => ['limit' => 40, 'description' => 'Tingkatkan menjadi 30-40 pesan, mulai variasi waktu'],
        3 => ['limit' => 60, 'description' => 'Kirim 50-60 pesan, gunakan delay random'],
        4 => ['limit' => 80, 'description' => 'Kirim 70-80 pesan, sertakan media sesekali'],
        5 => ['limit' => 120, 'description' => 'Kirim 100-120 pesan, pastikan ada balasan masuk'],
        6 => ['limit' => 160, 'description' => 'Kirim 140-160 pesan dengan konten bervariasi'],
        7 => ['limit' => 200, 'description' => 'Nomor siap untuk penggunaan normal (200 pesan/hari)'],
    ];

    /**
     * Get the device this warmup belongs to.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Start warmup for a device.
     */
    public static function startForDevice(int $deviceId): self
    {
        return self::updateOrCreate(
            ['device_id' => $deviceId],
            [
                'warmup_day' => 1,
                'daily_target' => self::WARMUP_SCHEDULE[1]['limit'],
                'current_progress' => 0,
                'is_warmup_complete' => false,
                'started_at' => now(),
            ]
        );
    }

    /**
     * Advance to the next warmup day.
     */
    public function advanceDay(): void
    {
        $nextDay = $this->warmup_day + 1;
        
        if ($nextDay > 7) {
            // Warmup complete!
            $this->update([
                'is_warmup_complete' => true,
                'completed_at' => now(),
            ]);
        } else {
            $this->update([
                'warmup_day' => $nextDay,
                'daily_target' => self::WARMUP_SCHEDULE[$nextDay]['limit'],
                'current_progress' => 0,
            ]);
        }
    }

    /**
     * Increment progress for today.
     */
    public function incrementProgress(int $count = 1): void
    {
        $this->increment('current_progress', $count);
    }

    /**
     * Get remaining messages for today.
     */
    public function getRemainingForToday(): int
    {
        return max(0, $this->daily_target - $this->current_progress);
    }

    /**
     * Get progress percentage for current day.
     */
    public function getDayProgressPercent(): float
    {
        if ($this->daily_target === 0) {
            return 100;
        }
        return round(($this->current_progress / $this->daily_target) * 100, 1);
    }

    /**
     * Get overall warmup progress percentage.
     */
    public function getOverallProgressPercent(): float
    {
        if ($this->is_warmup_complete) {
            return 100;
        }
        return round((($this->warmup_day - 1) / 7) * 100, 1);
    }

    /**
     * Get description for current warmup day.
     */
    public function getCurrentDayDescription(): string
    {
        return self::WARMUP_SCHEDULE[$this->warmup_day]['description'] ?? 'Lanjutkan penggunaan normal';
    }

    /**
     * Get warmup tips for display.
     */
    public static function getWarmupTips(): array
    {
        return [
            '✅ Mulai dengan mengirim pesan ke kontak yang sudah kenal',
            '✅ Jangan langsung blast ke banyak nomor baru',
            '✅ Pastikan ada balasan masuk dari penerima',
            '✅ Gunakan delay random antara pesan (30-60 detik)',
            '✅ Variasikan isi pesan, jangan copy-paste yang sama',
            '✅ Hindari link di 3 hari pertama',
            '❌ Jangan kirim pesan late night (00:00 - 06:00)',
            '❌ Jangan kirim gambar/video yang sama berulang-ulang',
        ];
    }

    /**
     * Check if device is still in warmup period.
     */
    public function isInWarmup(): bool
    {
        return !$this->is_warmup_complete;
    }

    /**
     * Should advance to next day? (Check if current day is complete)
     */
    public function shouldAdvance(): bool
    {
        return $this->current_progress >= $this->daily_target;
    }
}
