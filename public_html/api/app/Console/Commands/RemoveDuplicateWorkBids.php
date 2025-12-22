<?php

namespace App\Console\Commands;

use App\Models\ContractorTender;
use App\Models\ContractorTenderMaterialWork;
use App\Models\ContractorTenderRevision;
use App\Models\TenderMaterialWork;
use Illuminate\Console\Command;

class RemoveDuplicateWorkBids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicate-work-bids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all the contractor tender and update revision percentage accordingly.
        $contractorTenders = ContractorTender::orderBy('id', 'desc')->get();

        foreach ($contractorTenders as $contractorTender) {
            $latestRevisionOfContractor = ContractorTenderRevision::where('contractor_tender_id', $contractorTender->id)->latest()->first();
            $tenderMaterialWorks = TenderMaterialWork::where('tender_id', $contractorTender->tender_id)->get();

            foreach ($tenderMaterialWorks as $tenderMaterial) {
                $contractorTenderMaterialWorks = ContractorTenderMaterialWork::where('contractor_tender_id', $contractorTender->id)
                                                                                ->where('tender_material_work_id', $tenderMaterial->id)
                                                                                ->orderBy('id', 'asc')
                                                                                ->get();

                if (count($contractorTenderMaterialWorks) > $latestRevisionOfContractor->revision) {
                    $ids = $contractorTenderMaterialWorks->pluck('id')->toArray();
                    $idTs = $contractorTenderMaterialWorks->pluck('id', 'revision')->toArray();

                    // Ids to remove.
                    ContractorTenderMaterialWork::destroy(
                        array_diff($contractorTenderMaterialWorks->pluck('id')->toArray(),
                                    $contractorTenderMaterialWorks->pluck('id', 'revision')->toArray()));
                }
            }
        }
    }
}
