<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-old-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all 1 month old activities.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ActivityLog::where('created_at', '<=', now()->subDays(10))->delete();
    }
}
