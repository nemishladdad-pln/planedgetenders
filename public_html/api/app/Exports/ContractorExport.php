<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Http\Resources\ContractorResource;
use App\Models\Contractor;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;

class ContractorExport implements WithMapping,
                                    Responsable,
                                    WithColumnWidths,
                                    ShouldAutoSize,
                                    WithHeadings,
                                    WithEvents,
                                    FromCollection,
                                    WithStyles

{

    use Exportable;

    /**
     * @var bool
     */
    private $isDemo;
    private $selectedItem;


    public function __construct(array $request, $item = null)
    {
        if ($request['demo']) {
            $this->isDemo = true;
        }
        if ($item) {
            $this->selectedItem = $item;
        }
    }


    /**
    * @return
    */
    public function collection()
    {
        return ContractorResource::collection(Contractor::all()->sortByDesc('id'));
    }


    public function map($contractor): array
    {
        $contractorArr = ContractorResource::make(Contractor::findOrFail($contractor->id))->resolve();

        $panNumber = $gstNumber = $companyAddress = $directorAddressProof = null;

        if ($contractor->contractor_documents->where('document_type_id', 1)) {
            $panResource = $contractor->contractor_documents->where('document_type_id', 1);

            if (isset($panResource[0]['value'])) {
                $panNumber = $panResource[0]['value'];
            }
        }
        if ($contractor->contractor_documents->where('document_type_id', 2)) {
            $gstResource = $contractor->contractor_documents->where('document_type_id', 2);

            if (isset($gstResource[0]['value'])) {
                $gstNumber = $gstResource[0]['value'];
            }
            else if (isset($gstResource[1]['value']))
            {
                $gstNumber = $gstResource[1]['value'];
            }
            else if (isset($gstResource[2]['value']))
            {
                $gstNumber = $gstResource[2]['value'];
            }
        }
        $materials = array();
        if (!empty($contractor->material)) {
            foreach ($contractor->material as $material) {
                $materials[$material->id] = $material->id;
            }
        }

        $approvedStatus = "primary:Pending";
        if ($contractor->avp_by != null) {
            $approvedStatus = 'success:Approved';
        }

        $completedWorks = $ongoingWorks = $contactPerson = $directorProprietor =
        $technicalStaff = $equipments = $documents = $qualityCertificates = $turnOvers = "";
        if ($contractorArr['completed_works']) {
            $i = 0;
            $c = '';
            foreach ($contractorArr['completed_works'] as $completed_works) {
                $c .= $i + 1 . '-Completed work' ;
                $c .= 'Title: '.$completed_works->title_description . PHP_EOL;
                $c .= 'Scope Work: '.$completed_works->scope_work . PHP_EOL;
                $c .= 'Location: '.$completed_works->location . PHP_EOL;
                $c .= 'Tendered Cost: '.$completed_works->tendered_cost . PHP_EOL;
                $c .= 'Stage of Work: '.$completed_works->stage_work . PHP_EOL;
                $c .= 'Award Date: '.$completed_works->award_date . PHP_EOL;
                $c .= 'Planned Completion Date: '.$completed_works->planned_completion_date . PHP_EOL;
                $c .= 'Actual Completion Date: '.$completed_works->actual_completion_date . PHP_EOL;

                if (sizeof($contractorArr['completed_works']) > $i+1) {
                    $c .= PHP_EOL;
                }
                // $arr = [
                //     'title_description' => 	$completed_works->title_description . "/n",
                //     'scope_work' => 	$completed_works->scope_work . "/n",
                //     'location' => 	$completed_works->location . "/n",
                //     'tendered_cost' => 	$completed_works->tendered_cost . "/n",
                //     'actual_cost' => 	$completed_works->actual_cost . "/n",
                //     'stage_work' => 	$completed_works->stage_work . "/n",
                //     'award_date' => 	$completed_works->award_date . "/n",
                //     'planned_completion_date' => 	$completed_works->planned_completion_date . "/n",
                //     'actual_completion_date' => 	$completed_works->actual_completion_date . "/n",
                //     'client_contact_person_name' => 	$completed_works->client_contact_person_name . "/n",
                //     'client_contact_person_address' => 	$completed_works->client_contact_person_address . "/n",
                //     'architects_name' => 	$completed_works->architects_name . "/n",
                //     'architects_address' => 	$completed_works->architects_address . "/n",
                //     'architects_tel_no' => 	$completed_works->architects_tel_no . "/n",
                //     'other_consultants_name' => 	$completed_works->other_consultants_name . "/n",
                //     'other_consultants_address' => 	$completed_works->other_consultants_address . "/n",
                //     'other_consultants_tel_no' => 	$completed_works->other_consultants_tel_no . "/n",
                //     'responsible_staff' => 	$completed_works->responsible_staff . "/n",
                // ];
                // $completedWorks = implode('"\n"', $arr);
                $i++;
            }
            $completedWorks = wordwrap($c, 100, "\n") ;
        }

        if ($contractorArr['ongoing_works']) {
            $i = 0;
            $o = '';
            foreach ($contractorArr['ongoing_works'] as $ongoing_works) {
                $o .= $i + 1 . '-Ongoing work' ;
                $o .= 'Title: '.$ongoing_works->title_description . PHP_EOL;
                $o .= 'Scope Work: '.$ongoing_works->scope_work . PHP_EOL;
                $o .= 'Location: '.$ongoing_works->location . PHP_EOL;
                $o .= 'Tendered Cost: '.$ongoing_works->tendered_cost . PHP_EOL;
                $o .= 'Stage of Work: '.$ongoing_works->stage_work . PHP_EOL;
                $o .= 'Award Date: '.$ongoing_works->award_date . PHP_EOL;
                if (sizeof($contractorArr['ongoing_works']) > $i+1) {
                    $o .= PHP_EOL;
                }

                //array_push($arr, $o);
                // $arr[$i++] = [
                //     'title_description' => 	$ongoing_works->title_description, //*
                //     'scope_work' => 	$ongoing_works->scope_work,
                //     'location' => 	$ongoing_works->location,
                //     'tendered_cost' => 	$ongoing_works->tendered_cost, //*
                //     'actual_cost' => 	$ongoing_works->actual_cost,
                //     'stage_work' => 	$ongoing_works->stage_work,
                //     'award_date' => 	$ongoing_works->award_date,
                //     'planned_completion_date' => 	$ongoing_works->planned_completion_date,
                //     'actual_completion_date' => 	$ongoing_works->actual_completion_date,
                //     'client_contact_person_name' => 	$ongoing_works->client_contact_person_name,
                //     'client_contact_person_address' => 	$ongoing_works->client_contact_person_address,
                //     'architects_name' => 	$ongoing_works->architects_name,
                //     'architects_address' => 	$ongoing_works->architects_address,
                //     'architects_tel_no' => 	$ongoing_works->architects_tel_no,
                //     'other_consultants_name' => 	$ongoing_works->other_consultants_name,
                //     'other_consultants_address' => 	$ongoing_works->other_consultants_address,
                //     'other_consultants_tel_no' => 	$ongoing_works->other_consultants_tel_no,
                //     'responsible_staff' => 	$ongoing_works->responsible_staff,
                // ];
                $i++;
            }
            $ongoingWorks = wordwrap($o, 100, "\n") ;
        }
        if ($contractorArr['contact_persons']) {
            $i = 0;
            $contact = '';
            foreach ($contractorArr['contact_persons'] as $contact_person) {
                $contact .= $i + 1 . '-Contact Person'. PHP_EOL ;
                $contact .= 'Name: '.$contact_person->name . PHP_EOL;
                $contact .= 'Mobile Number: '.$contact_person->mobile_no . PHP_EOL;

                if (sizeof($contractorArr['contact_persons']) > $i+1) {
                    $contact .= PHP_EOL;
                }
                $i++;
            }
            $contactPerson = wordwrap($contact, 100, "\n") ;
        }

        if ($contractorArr['director_proprietor']) {
            $i = 0;
            $director = '';
            foreach ($contractorArr['director_proprietor'] as $director_proprietor) {
                $director .= $i + 1 . '-Director/Proprietor'. PHP_EOL ;
                $director .= 'Name: '.$director_proprietor->name . PHP_EOL;
                $director .= 'Qualification: '.$director_proprietor->qualification . PHP_EOL;
                $director .= 'Experience: '.$director_proprietor->experience . PHP_EOL;
                $director .= 'Working with Company since: '.$director_proprietor->working_with_company_since . PHP_EOL;

                if (sizeof($contractorArr['director_proprietor']) > $i+1) {
                    $director .= PHP_EOL;
                }
                $i++;
            }
            $directorProprietor = wordwrap($director, 100, "\n") ;
        }



        if ($contractorArr['technical_staff']) {
            $i = 0;
            $technical = '';
            foreach ($contractorArr['technical_staff'] as $technical_staff) {
                $technical .= $i + 1 . '-Technical Staff'. PHP_EOL ;
                $technical .= 'Name: '.$technical_staff->name . PHP_EOL;
                $technical .= 'Qualification: '.$technical_staff->qualification . PHP_EOL;
                $technical .= 'Experience: '.$technical_staff->experience . PHP_EOL;
                $technical .= 'Working with Company since: '.$technical_staff->working_with_company_since . PHP_EOL;

                if (sizeof($contractorArr['director_proprietor']) > $i+1) {
                    $technical  .= PHP_EOL;
                }
                $i++;
            }
            $technicalStaff = wordwrap($technical,  100, "\n") ;
        }


        if ($contractorArr['equipments']) {
            $i = 0;
            $equipmentString = '';
            foreach ($contractorArr['equipments'] as $equipment) {
                $equipmentString .= $i + 1 . '-Equipment'. PHP_EOL ;
                $equipmentString .= 'Name: '.$equipment->name_description . PHP_EOL;
                $equipmentString .= 'Make: '.$equipment->make . PHP_EOL;
                $equipmentString .= 'Manufacturing Year: '.$equipment->mfg_year . PHP_EOL;
                $equipmentString .= 'Purchase Year: '.$equipment->year_purchase . PHP_EOL;

                if (sizeof($contractorArr['ongoing_works']) > $i+1) {
                    $equipmentString .= PHP_EOL;
                }
                $i++;
            }
            $equipments = wordwrap($equipmentString, 45, "\n") ;
        }

        if ($contractor->quality_certificates) {

        }

        if ($contractorArr['turnovers']) {
            $turnOver = '';
            foreach ($contractorArr['turnovers'] as $turnover) {
                $turnOver .= $turnover->year . ': '. $turnover->turnover .'; '. PHP_EOL ;
            }
            $turnOvers = wordwrap($turnOver, 25, "\n") ;
        }
        return [
            /**Basic details  */
            $contractor->id,
            $contractor->cUID,
            $contractor->name,
            $contractor->email,
            $contractor->mobile_no,
            $contractor->company_name,
            $contractor->est_year,
            $contractor->address,
            $contractor->director_name,
            $contractor->director_dob,
            $contractor->director_address,
            $contractor->company_landline_no,
            $contractor->grade ? $contractor->grade: 'NA',
            $contractor->material_work_type->name,
            $panNumber,
            $gstNumber,
            $turnOvers,
            $completedWorks,
            $ongoingWorks,
            $equipments,
            $contactPerson,
            $directorProprietor,
            $technicalStaff,
            $contractor->created_at,
        ];
    }

    public function headings(): array {
        /*
        'cUID' => $this->cUID,
        'name' => $this->name,
        'company_name' => $this->company_name,
        'est_year' => $this->est_year,
        'director_name' => $this->director_name,
        'address' => $this->address,
        'director_dob' => $this->director_dob,
        'director_address' => $this->director_address,
        'director_avatar' => $this->director_avatar,
        'director_avatar_url' => (file_exists($this->director_avatar)) ? URL::to($this->director_avatar) : URL::to("storage/images/no-image.jpg"),
        'email' => $this->email,
        'mobile_no' => $this->mobile_no,
        'company_landline_no' => $this->company_landline_no,
        'material_work_type_id' => $this->material_work_type_id,
        'material_work_type' => $this->material_work_type->name,
        'with_labour_material' => $materials,
        'material' => $this->material,
        'checked_by' => $this->checked_by,
        'checked_name' => $this->checked_by ? $this->checked->name: null,
        'verified_by' => $this->verified_by,
        'avp_by ' => $this->avp_by,
        'approved_status' => $approvedStatus,
        'director' => $this->director,
        'last_login' => $this->last_login,
        'login_attempts' => $this->login_attempts,
        'contractor_bank_details' => $this->contractor_bank_details,
        'completed_works' => ContractorWorkResource::collection($this->contractor_works->where('completed', '=', 1)),
        'ongoing_works' => ContractorWorkResource::collection($this->contractor_works->where('completed', '=', 0)),
        'contact_persons' => $this->contractor_contacts,
        'director_proprietor' => ContractorDirectorTechnicalStaffResource::collection($this->contractor_director_technical_staffs->where('working_with_company_since', '=', null)),
        'technical_staff' => ContractorDirectorTechnicalStaffResource::collection($this->contractor_director_technical_staffs->where('working_with_company_since', '!=', null)),
        'equipments' => ContractorEquipmentResource::collection($this->contractor_equipments),
        'contractor_documents' => ContractorDocumentResource::collection($this->contractor_documents),
        'quality_certificates' => ContractorQualityCertificateResource::collection($this->contractor_quality_certificates),
        'turnovers' => ContractorTurnoverResource::collection($this->contractor_turnovers),
        'grade' => $this->grade,
        'user' => $this->user,
        'role' => implode(', ', $this->user->roles->pluck('name')->toArray()),
        'pan_no' => $panNumber,
        'gstin_no' => $gstNumber,
        'company_address' => $companyAddress,
        'director_address_proof' => $directorAddressProof,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        */
        return [
            'ID',
            'CUID',
            'NAME',
            'EMAIL',
            'MOBILE NO',
            'COMPANY NAME',
            'EST YEAR',
            'COMPANY ADDRESS',
            'DIRECTOR NAME',
            'DIRECTOR DOB',
            'DIRECTOR ADDRESS',
            'COMPANY LANDLINE NO.',
            'GRADE',
            'MATERIAL TYPE',
            'PAN NO',
            'GSTIN NO',
            'TURN OVERS(In Lakhs)',
            'COMPLETED WORKS',
            'ONGOING WORKS',
            'MACHINERY/EQUIPMENTS',
            'CONTACT PERSON',
            'DIRECTOR/PROPRIETOR',
            'TECHNICAL STAFF',
            'CREATED AT',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:Z1')->applyFromArray([
                    'font'=> [
                        'bold' => true,
                    ],
                    /*'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]*/
                ]);
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'C' => 35,
            'D' => 35,
            'F' => 35,
            'H' => 35,
            'I' => 35,
            'K' => 35,
            'L' => 15,
            'N' => 20,
            'O' => 20,
            'Q' => 30,
            'R' => 55,
            'S' => 55,
            'T' => 35,
            'U' => 35,
            'V' => 35,
            'W' => 35,
        ];
    }

    public function  styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
        $sheet->getStyle('G1:G'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('J1:J'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('P1:P'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q1:Q'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('R1:R'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('S1:S'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('T1:T'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('U1:U'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('V1:V'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
    }
    public function defaultStyles() {

    }
}
