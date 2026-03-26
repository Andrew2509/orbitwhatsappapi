<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    protected $fillable = [
        'user_id',
        'message_id',
        'path',
        'disk',
        'mime_type',
        'size_bytes',
        'delete_after',
        'is_deleted',
        'deleted_at',
    ];

    protected $casts = [
        'delete_after' => 'datetime',
        'deleted_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the user who uploaded this file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the message this file is attached to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Scope to get files pending deletion.
     */
    public function scopePendingDeletion($query)
    {
        return $query->where('is_deleted', false)
            ->whereNotNull('delete_after')
            ->where('delete_after', '<=', now());
    }

    /**
     * Scope to get files for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create a new media file record.
     */
    public static function createFromUpload(
        int $userId,
        string $path,
        string $disk = 'local',
        ?string $mimeType = null,
        int $sizeBytes = 0,
        ?int $messageId = null
    ): self {
        $retentionHours = config('whatsapp.media_retention_hours', 24);
        
        return self::create([
            'user_id' => $userId,
            'message_id' => $messageId,
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'delete_after' => now()->addHours($retentionHours),
        ]);
    }

    /**
     * Get the full URL of the file.
     */
    public function getUrl(): ?string
    {
        if ($this->is_deleted) {
            return null;
        }
        
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Delete the actual file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->is_deleted) {
            return true;
        }

        try {
            $deleted = Storage::disk($this->disk)->delete($this->path);
            
            if ($deleted) {
                $this->update([
                    'is_deleted' => true,
                    'deleted_at' => now(),
                ]);
            }
            
            return $deleted;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanSize(): string
    {
        $bytes = $this->size_bytes;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }
}
