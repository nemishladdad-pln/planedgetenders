<?php

namespace App\Console\Commands;

use App\Http\Resources\ContractorResource;
use App\Mail\UpdateProfileMail;
use App\Models\Contractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSeptemberEmailToContractor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-september-email-to-contractor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email to all the contractors to update their profile.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractors = ContractorResource::collection(Contractor::get());

        $contractors->each(fn($contractor) => $this->sendUpdateProfileEmail($contractor));

        $this->info('Updated profile email sent successfully');
    }

    private function sendUpdateProfileEmail($contractor)
    {
        $emailData = [
            'name' => $contractor->name,
            'email' => $contractor->email,
        ];

        Mail::to($contractor->email)->send(new UpdateProfileMail($emailData));
    }
}
