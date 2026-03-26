<?php

namespace App\Console\Commands;

use App\Models\DeviceWarmup;
use Illuminate\Console\Command;

class AdvanceWarmupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:advance-warmups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advance device warmups to the next day if target was met';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking device warmups...');

        $warmups = DeviceWarmup::where('is_warmup_complete', false)->get();

        $advancedCount = 0;
        $completedCount = 0;

        foreach ($warmups as $warmup) {
            if ($warmup->shouldAdvance()) {
                $wasDay = $warmup->warmup_day;
                $warmup->advanceDay();
                
                if ($warmup->is_warmup_complete) {
                    $completedCount++;
                    $this->line("Device #{$warmup->device_id}: Warmup completed! 🎉");
                } else {
                    $advancedCount++;
                    $this->line("Device #{$warmup->device_id}: Moved from day {$wasDay} to day {$warmup->warmup_day}");
                }
            } else {
                $remaining = $warmup->getRemainingForToday();
                $this->line("Device #{$warmup->device_id}: Day {$warmup->warmup_day} - {$remaining} messages remaining");
            }
        }

        $this->info("Advanced {$advancedCount} device(s), {$completedCount} completed warmup.");

        return Command::SUCCESS;
    }
}
