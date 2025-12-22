<?php

namespace App\Listeners;

use App\Events\TenderCreated;
use App\Models\Contractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenderCreatedMail;
use App\Mail\TenderPasswordMail;
use App\Models\Tender;

class SendContractorTenderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param TenderCreated $event
     * @return void
     */
    public function handle(TenderCreated $event)
    {
        $tender = $event->tender;

        $contractors = Contractor::where('material_work_type_id', $tender->material_work_type_id)->get();
        if (!empty($contractors->toArray())) {
            foreach ($contractors as $contractor) {
                $emailData = [
                    'tender' => $tender,
                    'email' => $contractor->email,
                ];
                // Send mail to contractors
                Mail::to($contractor->email)->send(new TenderCreatedMail($emailData));
            }

            // Send mail to organization about the secret tender password
            $tenderInfo = Tender::findOrFail($tender->id);
            $emailData = [
                'tender' => $tenderInfo,
                'email' => $contractor->email,
            ];
            Mail::to($tenderInfo->project->organization->email)->send(new TenderPasswordMail($emailData));
        }
        // Send email to contractor
        // Example:
        // Mail::to($tender->contractor->email)->send(new TenderCreatedNotification($tender));
    }
}
