<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CleanupMediaJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the media cleanup job to run hourly.
 * This removes expired media files based on retention settings.
 */
Schedule::job(new CleanupMediaJob)->hourly();

/**
 * Schedule daily limit reset at midnight.
 * This command resets device daily usage counters.
 */
Schedule::command('whatsapp:reset-daily-limits')->dailyAt('00:00');

/**
 * Schedule warmup advancement check daily at 6 AM.
 * This advances devices to the next warmup day if they completed their target.
 */
Schedule::command('whatsapp:advance-warmups')->dailyAt('06:00');
