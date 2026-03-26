<?php

namespace App\Console\Commands;

use App\Models\DeviceUsageLimit;
use Illuminate\Console\Command;

class ResetDailyLimitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:reset-daily-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily message limits for all devices (run at midnight)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Resetting daily limits...');

        // We don't need to explicitly reset - new records are created for each day
        // But we can clean up old records to keep the table size manageable
        $deletedCount = DeviceUsageLimit::where('date', '<', now()->subDays(30)->toDateString())->delete();

        $this->info("Cleaned up {$deletedCount} old usage records (older than 30 days).");
        $this->info('Daily limits have been reset for a new day.');

        return Command::SUCCESS;
    }
}
