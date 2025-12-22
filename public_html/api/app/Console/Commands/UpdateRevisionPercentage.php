<?php

namespace App\Console\Commands;

use App\Models\ContractorTender;
use App\Models\ContractorTenderMaterialWork;
use App\Models\ContractorTenderRevision;
use App\Models\TenderMaterialWork;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRevisionPercentage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-revision-percentage';

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
        // First we need to calculate every tender material works total.
        $tenderMaterialWorks = TenderMaterialWork::orderBy('id', 'desc')->get();
        //Log::info('Tender Material Works --'.$tenderMaterialWorks);
        if ($tenderMaterialWorks) {
            foreach ($tenderMaterialWorks as $tenderMaterialWork) {
                $total = (int)$tenderMaterialWork->rate * (int)$tenderMaterialWork->quantity;


                TenderMaterialWork::where('id', $tenderMaterialWork->id)->update(['total' => round($total, 2)]);
                // Now we must find all the contractor tender material works and update all the required fields.
                $contractorTenderMaterialWorks = ContractorTenderMaterialWork::where('tender_material_work_id', $tenderMaterialWork->id)
                                                                                ->get();
                if ($contractorTenderMaterialWorks) {
                    foreach ($contractorTenderMaterialWorks as $contractorTenderMaterialWork) {
                        $contractorTotal = (int)$contractorTenderMaterialWork->rate * (int)$tenderMaterialWork->quantity;
                        ContractorTenderMaterialWork::where('id', $contractorTenderMaterialWork->id)
                                                    ->update([
                                                        'total' => round($contractorTotal, 2),
                                                        'quantity' => $tenderMaterialWork->quantity,

                                                    ]);
                    }
                }
            }
        }

        // Fetch all the contractor tender and update revision percentage accordingly.
        $contractorTenders = ContractorTender::get();

        foreach ($contractorTenders as $contractorTender) {
            $contractorTenderRevisions = ContractorTenderRevision::where('contractor_tender_id', $contractorTender->id)->get();
            if ($contractorTenderRevisions) {
                foreach ($contractorTenderRevisions as $contractorTenderRevision) {
                    $this->_calculatePercentageDiffRevision($contractorTender, $contractorTenderRevision->revision);
                }
            }
        }
    }

    private function _calculatePercentageDiffRevision($contractorTender, $revision)
    {
        // Material work rates total
        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect(); //important as the existing connection if any would be in strict mode
        $materialWorks = DB::table('tender_material_works')
                                ->select('tender_id', 'material_work_type_id', DB::raw('ROUND(SUM(`total`), 2) AS rate_total'))
                                ->whereRaw('tender_id = '.$contractorTender->tender_id)
                                ->groupBy('material_work_type_id')
                                ->orderBy('material_work_type_id', 'asc')
                                ->get();
        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();


        $contractorRateAdd = $materialRateAdd = 0;
        foreach ($materialWorks as $materialWork) {
            // Contractor rate added.
            $contractorRateTotal = $contractorTender->contractor_tender_material_works()
                                //->where('tender_material_work_id', $materialWork->material_work_type_id)
                                ->where('revision', $revision)
                                ->sum('total');

            // Now calculate percentage difference.
            // Formula to calculate percentage difference is as below:
            /**
             * (Contractor Rate total - Tender rate total) / ( ((Contractor Rate total + Tender rate total) / 2) ) * 100
             */

            if ((int)$contractorRateTotal > (int)$materialWork->rate_total * 2) {
                $percentageDifference = 100;
            } else if ((int)$contractorRateTotal > (int)$materialWork->rate_total) {
                $percentageDifference = ((int)$contractorRateTotal - (int)$materialWork->rate_total) / (((int)$contractorRateTotal + (int)$materialWork->rate_total) / 2) * 100;
            } else {
                $percentageDifference = 0;
            }


            // Log::info('Contractor id--'.$contractorTender->contractor_id);
            // Log::info('Material--'.$materialWork->rate_total);
            // Log::info('Contractor Total--'.$contractorRateTotal);
            // Log::info('Percentage Difference--'.$percentageDifference);

            $contractorTender->contractor_tender_work_revisions()
                            ->where('material_work_type_id', $materialWork->material_work_type_id)
                            ->where('revision', $revision)
                            ->update(['percentage_difference' => round($percentageDifference, 2)]);

            $materialRateAdd += $materialWork->rate_total;
            $contractorRateAdd += $contractorRateTotal;
        }

        if ((int)$contractorRateAdd > (int)$materialRateAdd * 2) {
            $versionPercentageDifference = 100;
        } else if ((int)$contractorRateAdd > (int)$materialRateAdd) {
            $versionPercentageDifference = ((int)$contractorRateAdd - (int)$materialRateAdd) / (((int)$contractorRateAdd + (int)$materialRateAdd) / 2) * 100;
        } else {
            $versionPercentageDifference = 0;
        }

        $contractorTender->contractor_tender_revisions()->where('revision', $revision)->update(
            [
                'percentage_difference' => round($versionPercentageDifference, 2)
            ]);
        return $contractorTender;
    }
}
