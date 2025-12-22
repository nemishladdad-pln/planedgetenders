<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('app:remove-duplicate-work-bids')->everyMinute()->runInBackground();
        $schedule->command('app:change-tender-status')->daily()->runInBackground();
        $schedule->command('app:upload-contractor-to-google-sheet')->everyMinute()->runInBackground();
        $schedule->command('app:remove-old-activities')->daily()->runInBackground();
        $schedule->command('app:send-september-email-to-contractor')->yearlyOn(9, 1)->runInBackground();
        //$schedule->command('app:update-revision-percentage')->everyMinute()->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
