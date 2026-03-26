<?php

namespace App\Jobs;

use App\Models\MediaFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to clean up expired media files.
 * 
 * This job runs on the 'low' priority queue as it's not time-sensitive.
 * It deletes files that have passed their retention period.
 */
class CleanupMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Maximum files to process per batch.
     */
    protected int $batchSize = 100;

    public function __construct()
    {
        // Run on low priority queue
        $this->onQueue('low');
    }

    public function handle(): void
    {
        Log::info('Starting media cleanup job');
        
        $deletedCount = 0;
        $failedCount = 0;
        $totalSizeFreed = 0;

        // Get files pending deletion
        $files = MediaFile::pendingDeletion()
            ->limit($this->batchSize)
            ->get();

        if ($files->isEmpty()) {
            Log::info('No media files pending deletion');
            return;
        }

        foreach ($files as $file) {
            try {
                $size = $file->size_bytes;
                
                if ($file->deleteFile()) {
                    $deletedCount++;
                    $totalSizeFreed += $size;
                    
                    Log::debug('Deleted media file', [
                        'file_id' => $file->id,
                        'path' => $file->path,
                        'size' => $file->getHumanSize(),
                    ]);
                } else {
                    $failedCount++;
                    Log::warning('Failed to delete media file', [
                        'file_id' => $file->id,
                        'path' => $file->path,
                    ]);
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Error deleting media file', [
                    'file_id' => $file->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log summary
        Log::info('Media cleanup completed', [
            'deleted' => $deletedCount,
            'failed' => $failedCount,
            'size_freed' => $this->formatBytes($totalSizeFreed),
        ]);

        // If there are more files, dispatch another job
        $remainingCount = MediaFile::pendingDeletion()->count();
        if ($remainingCount > 0) {
            self::dispatch()->delay(now()->addSeconds(30));
            Log::info("Scheduling next cleanup batch, {$remainingCount} files remaining");
        }
    }

    /**
     * Format bytes to human readable string.
     */
    protected function formatBytes(int $bytes): string
    {
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
