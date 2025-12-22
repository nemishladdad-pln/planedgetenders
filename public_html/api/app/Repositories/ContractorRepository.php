<?php
namespace App\Repositories;

use App\Http\Resources\ContractorResource;
use App\Models\Contractor;
use App\Models\ContractorContact;
use App\Models\ContractorDirectorTechnicalStaff;
use App\Models\ContractorDocument;
use App\Models\ContractorEquipment;
use App\Models\ContractorQualityCertificate;
use App\Models\ContractorTender;
use App\Models\ContractorTurnover;
use App\Models\ContractorWork;
use App\Models\Grade;
use App\Models\MaterialWorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\ProfileUser;
use App\Models\Tender;
use App\Models\User;
use App\Repositories\Interfaces\ContractorRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class ContractorRepository implements ContractorRepositoryInterface
{
    public function all($request, $userId = null)
    {

        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        if ($userId) {
            $user = User::findOrFail($userId);
            $roles = $user->roles->pluck('name')->toArray();

            if (in_array('Organization', $roles)) {
                $organization = Organization::where('user_id', $user->id)->first();
                $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

                $tenders = Tender::where('status', 'active')
                                ->where('project_id', $projectIds)
                                ->get();

                $tenderIds = $tenders->pluck('id', 'project_id')->toArray();

                $contractorTenders = ContractorTender::where('tender_id', $tenderIds)->get();
                $contractorIds = $contractorTenders->pluck('id', 'contractor_id')->toArray();


                if (!empty($contractorIds)) {
                    return ContractorResource::collection(
                        Contractor::when(request('search'), function ($query) {
                            $searchObj = json_decode(request('search'));

                            if ($searchObj->cUID && $searchObj->cUID != '') {
                                $query->where('cUID', 'like', '%' . strtoupper($searchObj->cUID) . '%');
                            }
                            if ($searchObj->company_name && $searchObj->company_name != '') {
                                $query->where('company_name', 'like', '%' . $searchObj->company_name . '%');
                            }
                            if ($searchObj->director_name && $searchObj->director_name != '') {
                                $query->where('director_name', 'like', '%' . $searchObj->director_name . '%');
                            }
                            if ($searchObj->material_work_type_id && $searchObj->material_work_type_id != '') {
                                $query->where('material_work_type_id', $searchObj->material_work_type_id);
                            }
                            if ($searchObj->status && $searchObj->status != '') {
                                if ($searchObj->status == 'pending') {
                                    $query->where('avp_by', null);
                                } else {
                                    $query->where('avp_by', '!=' ,null);
                                }
                            }
                            if ($searchObj->grade && $searchObj->grade != '') {
                                $query->where('grade', "'".$searchObj->grade."'");
                            }
                        })->where('id', $contractorIds)->orderBy($field, $order)->paginate($perPage)
                    );
                } else {
                    return false;
                }
            }
        }

        return ContractorResource::collection(
            Contractor::when(request('search'), function ($query) {
                $searchObj = json_decode(request('search'));

                if ($searchObj->cUID && $searchObj->cUID != '') {
                    $query->where('cUID', 'like', '%' . $searchObj->cUID . '%');
                }
                if ($searchObj->company_name && $searchObj->company_name != '') {
                    $query->where('company_name', 'like', '%' . $searchObj->company_name . '%');
                }
                if ($searchObj->director_name && $searchObj->director_name != '') {
                    $query->where('director_name', 'like', '%' . $searchObj->director_name . '%');
                }
                if ($searchObj->material_work_type_id && $searchObj->material_work_type_id != '') {
                    $query->where('material_work_type_id', $searchObj->material_work_type_id);
                }
                if ($searchObj->status && $searchObj->status != '') {
                    $query->where('avp_by', '!=', null);
                }
                if ($searchObj->grade && $searchObj->grade != '') {
                    $query->where('grade', $searchObj->grade);
                }
            })->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function create($data)
    {
        // Firstly, we need to create entry users table
        // with director email address as login id and auto-generated password.

        $user = User::create([
            'name' => $data->director_name,
            'password' => Hash::make(123456789),
            'email' => $data->email,
        ]);

        $user->assignRole('Contractor');

        if (!$user) {
            return false;
        }
        $cuID = $this->__getContractorUID($data);
        $input = [
            'cUID' => $cuID,
            'user_id' => $user->id,
            'name' => $data->name,
            'password' => Hash::make(123456789),
            'company_name' => $data->company_name,
            'est_year' => $data->est_year,
            'director_name' => $data->director_name,
            'address' => $data->address,
            'director_dob' => $data->director_dob,
            'director_address' => $data->director_address,
            'email' => $data->email,
            'mobile_no' => $data->mobile_no,
            'company_landline_no' => $data->company_landline_no,
            'material_work_type_id' => $data->material_work_type_id,
        ];
        if (!empty($data->file('director_avatar'))) {
            $input['director_avatar'] = save_image($data->file('director_avatar'), 'contractors', $cuID);
        }
        if (Auth::user()) {
            $input['created_by'] = Auth::user()->id;
            $input['updated_by'] = Auth::user()->id;
        }
        $contractor = Contractor::create($input);

        //$contractor->material_work_type()->attach((array) json_decode($data->material_work_type_id, true, 10));
        // Save Bank details
        if (!empty($data->bank_details)) {
            $contractor->contractor_bank_details()->create((array) json_decode($data->bank_details, true, 10));
        }

        // Save Quality Certificate file etc..

        if (isset($data->quality_certificates) && !empty($data->quality_certificates)) {
            $i = 0;

            foreach ((array) json_decode($data->quality_certificates, true, 10) as $quality_certificate) {
                if ($data->file('quality_certificate_'.$i)) {
                $contractor->contractor_quality_certificates()->create([
                    'name' => $quality_certificate['name'],
                    'storage' => save_documents($data->file('quality_certificate_'.$i), 'contractors/'.$cuID.'/quality_certificates', $cuID),
                ]);
                $i++;
             }
            }
        }

        // Save documents like PAN file, GST-IN file etc..

        if (isset($data->documents) && !empty($data->documents)) {
            $i = 1;
        //    dd($data);
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                if (isset($document['document_type_id'], $document['value'])) {
                    if ($data->file('document_'.$i)){
                        $contractor->contractor_documents()->create([
                            'document_type_id'=> $document['document_type_id'],
                            'value' => $document['value'],
                            'storage' => save_documents($data->file('document_'.$i), 'contractors/'.$cuID.'/documents', $cuID),
                        ]);
                        $i++;
                    }
                }
             }
        // }

        // Save Turnover information with certificate as document.

        $turnOverTotalAmount = 0;
        if (isset($data->turnovers) && !empty($data->turnovers)) {
            $i = 0;
            // dd((array) json_decode($data->turnovers));
            foreach ((array) json_decode($data->turnovers, true, 10) as $turnover) {
                $turnOverTotalAmount += intval(str_replace(",","",$turnover['amount']));

                $contractor->contractor_turnovers()->create([
                    'year'=> $turnover['year'],
                    'turnover' => intval(str_replace(",","",$turnover['amount'])),
                    'certificate_storage' => save_documents($data->file('turnover_'.$i), 'contractors/'.$cuID.'/turnover_certificates', $cuID),
                ]);
                $i++;
             }
        }

        // Get material work type row to get grade
        $materialWorkType = MaterialWorkType::findOrFail($data->material_work_type_id);
        $gradeAMaterialType = $materialWorkType->grade_A;
        $gradeAArr = explode('+', $gradeAMaterialType);
        $gradeBArr = explode('-', $gradeAMaterialType);
        if (((int)$turnOverTotalAmount / 3) * 100000 >= (int)$gradeAArr[0]) {
            $grade = 'A';
        } else if (((int)$turnOverTotalAmount/3) * 100000 >= (int)$gradeBArr[0] &&
                   ((int)$turnOverTotalAmount/3) * 100000 <= (int)$gradeBArr[1]) {
            $grade = 'B';
        } else {
            $grade = 'C';
        }

        if (isset($grade)) {
            $contractor->grade = $grade;
            $contractor->save();
        }
        if (isset($data->director_proprietor) && !empty($data->director_proprietor)) {
            // Save contractor directors
            $contractor->contractor_director_technical_staffs()->createMany((array) json_decode($data->director_proprietor, true, 10));
        }
        if (isset($data->technical_staff) && !empty($data->technical_staff)) {
            // Save contractor technical staffs
            $contractor->contractor_director_technical_staffs()->createMany((array) json_decode($data->technical_staff, true, 10));
        }
        if (isset($data->equipments) && !empty($data->equipments)) {
            // Save contractor technical staffs
            $contractor->contractor_equipments()->createMany((array) json_decode($data->equipments, true, 10));
        }
        if (isset($data->completed_works) && !empty($data->completed_works)) {
            // Save contractor completed works
            $contractor->contractor_works()->createMany((array) json_decode($data->completed_works, true, 10));
        }
        if (isset($data->ongoing_works) && !empty($data->ongoing_works)) {
            // Save contractor ongoing works
            $contractor->contractor_works()->createMany((array) json_decode($data->ongoing_works, true, 10));
        }
        if (isset($data->contact_persons) && !empty($data->contact_persons)) {
            // Save contractor contacts
            $contractor->contractor_contacts()->createMany((array) json_decode($data->contact_persons, true, 10));
        }

        if (isset($data->with_labour_material) && !empty($data->with_labour_material)) {
            $contractor->material()->sync((array) json_decode($data->with_labour_material));
        }

        if ($data->payment_id) {
            $payment = Payment::findOrFail($data->payment_id);
            if ($payment) {
                $payment->model_id = $contractor->id;
                $payment->model = 'App/Models/Contractor';
                $payment->user_id = $user->id;
                $payment->save();
            }
        }

        if ($contractor->mobile_no) {
            $user->mobile = $contractor->mobile_no;
            $user->save();
        }
        $profile = new ProfileUser([
            'mobile_no' => $contractor->mobile_no,
        ]);
        $user->profile_user()->save($profile);

        event(new Registered($user));

        return $contractor;
    }
    }
    public function show($data)
    {

    }

    public function update($data, $contractor)
    {
        if (isset($data->material_work_type_id) && ($data->material_work_type_id !== $contractor->material_work_type_id)) {
            $newCUID = $this->__getContractorUID($data);
            $contractor->material_work_type_id = $data->material_work_type_id;
            $contractor->cUID = $newCUID;
        }

        if ($data->form === 'basic') {
            $input = [
                'name' => $data->name,
                'company_name' => $data->company_name,
                'est_year' => $data->est_year,
                'director_name' => $data->director_name,
                'address' => $data->address,
                'director_dob' => $data->director_dob,
                'director_address' => $data->director_address,
                'email' => $data->email,
                'mobile_no' => $data->mobile_no,
                'company_landline_no' => $data->company_landline_no,
                //'material_work_type_id' => $data->material_work_type_id,
                'updated_by' => Auth::user()->id,
            ];
            if (isset($data->material_work_type_id)) {
                $input['material_work_type_id'] = $data->material_work_type_id;
            }
            if (!empty($data->file('director_avatar'))) {
                if (file_exists($contractor->director_avatar)) {
                    unlink($contractor->director_avatar);
                }
                $input['director_avatar'] = save_image($data->file('director_avatar'), 'contractors', $contractor->cUID);
            }
            // $contractor->contractor_bank_details()->update((array) json_decode($data->bank_details, true, 10));
            $contractor->update($input);
            $contractor->material()->sync(json_decode($data->with_labour_material));
        }
        if ($data->form === "directorTechnicalStaff") {
            $contractor = $this->_saveDirectorTechnicalStaff($data, $contractor);
        }

        if ($data->form === "works") {
            $contractor = $this->_saveWorks($data, $contractor);
        }

        if ($data->form === "equipment") {
            $contractor = $this->_saveEquipment($data, $contractor);
        }

        if ($data->form === "document_certificate") {
            $contractor = $this->_saveDocumentQualityCertificate($data, $contractor);
        }

        if ($data->form === "contact_person") {
            $contractor = $this->_saveContactPerson($data, $contractor);
        }
        $contractor->save();
        return $contractor;
    }

    public function delete($contractor)
    {
        $obj = Contractor::findOrFail($contractor->id);
        return $obj->delete();
    }

    private function __getContractorUID($data)
    {
        $contractorMaterialTypeCount = Contractor::where('material_work_type_id', $data->material_work_type_id)->count();
        $cUID = 'PLC-'.(int) $data->material_work_type_id.str_pad($contractorMaterialTypeCount +1, 4, '0', STR_PAD_LEFT);

        $i = 1;
        while (Contractor::where('cUID', $cUID)->exists()) {
            $cUID = 'PLC-'.(int) $data->material_work_type_id.str_pad($contractorMaterialTypeCount + ++$i, 4, '0', STR_PAD_LEFT);
        }

        // if (Contractor::where('cUID', $cUID)->exists()) {
        //     $cUID = 'PLC-'.(int) $data->material_work_type_id.str_pad($contractorMaterialTypeCount + 2, 4, '0', STR_PAD_LEFT);
        // }
        return $cUID;

        // $count = 0;
        // $contractor = Contractor::latest()->first();
        // $count = str_pad( $contractor->id + 1, 4, "0", STR_PAD_LEFT );
        // return 'PLC-'.(int) $data->material_work_type_id.$count;

    }

    private function _saveWorks($data, $contractor)
    {
        // Save contractor directors
        if (!empty((array) json_decode($data->completed_works))) {

            foreach ((array) json_decode($data->completed_works) as $completed) {

                if (isset($completed->id)) {
                    $completedObj = ContractorWork::find($completed->id);
                    $completedObj->title_description = $completed->title_description;
                    $completedObj->scope_work = $completed->scope_work;
                    $completedObj->location = $completed->location;
                    $completedObj->tendered_cost = $completed->tendered_cost;
                    $completedObj->actual_cost = $completed->actual_cost;
                    $completedObj->planned_completion_date = $completed->planned_completion_date;
                    $completedObj->actual_completion_date = $completed->actual_completion_date;
                    $completedObj->client_contact_person_name = $completed->client_contact_person_name;
                    $completedObj->client_contact_person_address = $completed->client_contact_person_address;
                    $completedObj->architects_name = $completed->architects_name;
                    $completedObj->architects_address = $completed->architects_address;
                    $completedObj->architects_tel_no = $completed->architects_tel_no;
                    $completedObj->other_consultants_name = $completed->other_consultants_name;
                    $completedObj->other_consultants_address = $completed->other_consultants_address;
                    $completedObj->other_consultants_tel_no = $completed->other_consultants_tel_no;
                    $completedObj->responsible_staff = $completed->responsible_staff;

                    $completedObj->completed = 1;

                    $completedObj->save();
                } else {
                    $contractor->contractor_works()->create((array) $completed);
                }
            }
        }
        // Save contractor technical staffs
        if (!empty((array) json_decode($data->ongoing_works))) {

            foreach ((array) json_decode($data->ongoing_works) as $ongoing) {

                if (isset($ongoing->id)) {
                    $ongoingObj = ContractorWork::find($ongoing->id);
                    $ongoingObj->title_description = $ongoing->title_description;
                    $ongoingObj->scope_work = $ongoing->scope_work;
                    $ongoingObj->location = $ongoing->location;
                    $ongoingObj->tendered_cost = $ongoing->tendered_cost;
                    $ongoingObj->actual_cost = $ongoing->actual_cost;
                    $ongoingObj->planned_completion_date = $ongoing->planned_completion_date;
                    $ongoingObj->actual_completion_date = $ongoing->actual_completion_date;
                    $completedObj->client_contact_person_name = $completed->client_contact_person_name;
                    $completedObj->client_contact_person_address = $completed->client_contact_person_address;
                    $completedObj->architects_name = $completed->architects_name;
                    $completedObj->architects_address = $completed->architects_address;
                    $completedObj->architects_tel_no = $completed->architects_tel_no;
                    $completedObj->other_consultants_name = $completed->other_consultants_name;
                    $completedObj->other_consultants_address = $completed->other_consultants_address;
                    $completedObj->other_consultants_tel_no = $completed->other_consultants_tel_no;
                    $completedObj->responsible_staff = $completed->responsible_staff;
                    $ongoingObj->stage_work = $ongoing->stage_work;
                    $ongoingObj->award_date = $ongoing->award_date;
                    $ongoingObj->completed = 0;

                    $ongoingObj->save();
                } else {
                    $contractor->contractor_works()->create((array) $ongoing);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->completed_work_removed)) {
            //dd((array) json_decode($data->completed_work_removed));
            ContractorWork::destroy((array) json_decode($data->completed_work_removed));
        }
        if (!empty($data->ongoing_work_removed)) {
            ContractorWork::destroy((array) json_decode($data->ongoing_work_removed));
        }
        return $contractor;
    }

    private function _saveDirectorTechnicalStaff($data, $contractor)
    {
        // Save contractor directors
        if (!empty((array) json_decode($data->director_proprietor))) {

            foreach ((array) json_decode($data->director_proprietor) as $director) {

                if (isset($director->id)) {
                    $dirObj = ContractorDirectorTechnicalStaff::find($director->id);
                    $dirObj->name = $director->name;
                    $dirObj->qualification = $director->qualification;
                    $dirObj->experience = $director->experience;
                    $dirObj->save();
                } else {
                    $contractor->contractor_director_technical_staffs()->create([
                        'type' => 'director',
                        'name' => $director->name,
                        'qualification' => $director->qualification,
                        'experience' => $director->experience,
                    ]);
                }
            }
        }

        // Save contractor technical staffs
        if (!empty((array) json_decode($data->technical_staff))) {

            foreach ((array) json_decode($data->technical_staff) as $technical) {

                if (isset($technical->id)) {
                    $technicalObj = ContractorDirectorTechnicalStaff::find($technical->id);
                    $technicalObj->name = $technical->name;
                    $technicalObj->qualification = $technical->qualification;
                    $technicalObj->experience = $technical->experience;
                    $technicalObj->working_with_company_since = $technical->working_with_company_since;
                    $technicalObj->save();
                } else {
                    $contractor->contractor_director_technical_staffs()->create([
                        'type' => 'technical_staff',
                        'name' => $technical->name,
                        'qualification' => $technical->qualification,
                        'experience' => $technical->experience,
                        'working_with_company_since' => $technical->working_with_company_since,
                    ]);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->director_removed)) {
            ContractorDirectorTechnicalStaff::destroy((array) json_decode($data->director_removed));
        }
        if (!empty($data->technical_staff_removed)) {
            ContractorDirectorTechnicalStaff::destroy((array) json_decode($data->technical_staff_removed));
        }

        return $contractor;
    }

    private function _saveEquipment($data, $contractor)
    {
        // Save contractor technical staffs
        if (!empty((array) json_decode($data->equipments))) {

            foreach ((array) json_decode($data->equipments) as $equipment) {

                if (isset($equipment->id)) {
                    $equipmentObj = ContractorEquipment::find($equipment->id);
                    $equipmentObj->name_description = $equipment->name_description;
                    $equipmentObj->make = $equipment->make;
                    $equipmentObj->mfg_year = $equipment->mfg_year;
                    $equipmentObj->year_purchase = $equipment->year_purchase;
                    $equipmentObj->save();
                } else {
                    $contractor->contractor_equipments()->create([
                        'name_description' => $equipment->name_description,
                        'make' => $equipment->make,
                        'mfg_year' => $equipment->mfg_year,
                        'year_purchase' => $equipment->year_purchase,
                    ]);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->equipments_removed)) {
            ContractorEquipment::destroy((array) json_decode($data->equipments_removed));
        }
        return $contractor;
    }

    private function _saveDocumentQualityCertificate($data, $contractor)
    {
        // Save Quality Certificate file etc..

        $cuID = $contractor->cUID;

        if (!empty($data->quality_certificates)) {
            $i = 0;
            foreach ((array) json_decode($data->quality_certificates, true, 10) as $quality_certificate) {
                $storage = null;
                if ($data->file('quality_certificate_'.$i)) {
                    $storage = save_documents($data->file('quality_certificate_'.$i), 'contractors/'.$cuID.'/quality_certificates', $cuID);
                }

                if (isset($quality_certificate['id'])) {
                    $certObj = ContractorQualityCertificate::find($quality_certificate['id']);
                    $certObj->name = $quality_certificate['name'];
                    if ($storage) {
                        $certObj->storage = $storage;
                    }
                    $certObj->save();
                } else {
                    $contractor->contractor_quality_certificates()->create([
                        'name' => $quality_certificate['name'],
                        'storage' => $storage,
                    ]);
                }
                $i++;
             }
        }

        // Now we will all the items that are removed.
        if (!empty($data->certificates_removed)) {
            ContractorQualityCertificate::destroy((array) json_decode($data->certificates_removed));
        }
        // Save documents like PAN file, GST-IN file etc..

        if (!empty($data->documents)) {
            $i = 0;
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                $storage = null;
                if ($data->file('document_'.$i)) {
                    $storage = save_documents($data->file('document_'.$i), 'contractors/'.$cuID.'/documents', $cuID);
                }

                if (isset($document['id'])) {
                    $docObj = ContractorDocument::find($document['id']);
                    if ($storage) {
                        $docObj->storage = $storage;
                    }
                    $docObj->save();
                }
                $i++;
             }
        }

        // Save Turnover information with certificate as document.

        if (!empty($data->turnovers)) {
            $i = 0;
            foreach ((array) json_decode($data->turnovers, true, 10) as $turnover) {
                $storage = null;
                if ($data->file('turnover_'.$i)) {
                    $storage = save_documents($data->file('turnover_'.$i), 'contractors/'.$cuID.'/turnover_certificates', $cuID);
                }
                if (isset($turnover['id'])) {
                    $turnOverObj = ContractorTurnover::find($turnover['id']);

                    $turnOverObj->turnover = $turnover['turnover'];
                    if ($storage) {
                        $turnOverObj->certificate_storage = $storage;
                    }
                    $turnOverObj->save();
                }
                $i++;
             }
        }

        return $contractor;
    }

    private function _saveContactPerson($data, $contractor)
    {
        if (!empty((array) json_decode($data->contact_persons))) {

            foreach ((array) json_decode($data->contact_persons) as $contactPerson) {

                if (isset($contactPerson->id)) {
                    $contactObj = ContractorContact::find($contactPerson->id);
                    $contactObj->name = $contactPerson->name;
                    $contactObj->mobile_no = $contactPerson->mobile_no;
                    $contactObj->save();
                } else {
                    $contractor->contractor_contacts()->create([
                        'name' => $contactPerson->name,
                        'mobile_no' => $contactPerson->mobile_no,
                    ]);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->contact_persons_removed)) {
            ContractorContact::destroy((array) json_decode($data->contact_persons_removed));
        }
        return $contractor;
    }

    public function findByUser($userId)
    {
        return ContractorResource::make(Contractor::where('user_id', $userId)->first());
    }

    public function approve_contractor($contractorId, $userId = null)
    {
        $contractor = Contractor::findOrFail($contractorId);
        if ($contractor->avp_by == null && $userId != null) {
            $contractor->avp_by = $userId;
        } else if ($userId != null) {
            $contractor->avp_by = null;
        }
        $contractor->save();
        return ContractorResource::make($contractor);
    }
}
