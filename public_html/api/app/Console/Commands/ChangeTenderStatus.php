<?php

namespace App\Console\Commands;

use App\Models\Tender;
use Illuminate\Console\Command;

class ChangeTenderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-tender-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change tender status when bid is ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenders = Tender::where('bid_submission_end_date', '<', now())
            ->where('status', 'active')->get();

        $tenders->each(fn ($tender) => $tender->update(['status' => 'inactive']));

        $this->info("Tenders updated successfully");
    }
}
