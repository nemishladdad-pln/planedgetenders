<?php

namespace App\Console\Commands;

use App\Http\Resources\ContractorResource;
use App\Models\Contractor;
use App\Services\Google\GoogleSheetService;
use Illuminate\Console\Command;
use Revolution\Google\Sheets\Facades\Sheets;
use Carbon\Carbon;
class UploadContractorToGoogleSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-contractor-to-google-sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The details filled in the Registration form should be uploaded on google sheets to maintain the records';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        /** prepare the data in array **/

        $data = [
            [
                'CUID',
                'NAME',
                'EMAIL',
                'MOBILE',
                'COMPANY NAME',
                'EST YEAR',
                'DIRECTOR NAME',
                'ADDRESS',
                'DIRECTOR DOB',
                'DIRECTOR ADDRESS',
                'COMPANY MOBILE NO.',
                "GRADE",
                'MATERIAL WORK TYPE',
                "PAN NO",
                "GST NO",
            ],
        ];

        $contractors = ContractorResource::collection(Contractor::get());

        $i = 1;
        if (!empty($contractors)) {
            foreach ($contractors as $contractor) {
                $data[$i++] = [
                    $contractor->cUID,
                    $contractor->name,
                    $contractor->email,
                    $contractor->mobile_no,
                    $contractor->company_name,
                    $contractor->est_year,
                    $contractor->director_name,
                    $contractor->address,
                    $contractor->director_dob,
                    $contractor->director_address,
                    $contractor->company_landline_no,
                    $contractor->grade ? $contractor->grade: 'NA',
                    $contractor->material_work_type->name,
                    $contractor->pan_no,
                    $contractor->gstin_no,
                ];
            }
        }

        (new GoogleSheetService ())->writeSheet($data);
    }
}
