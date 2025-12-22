<?php
namespace App\Repositories;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\OrganizationDirector;
use App\Models\OrganizationDocument;
use App\Models\OrganizationWork;
use App\Models\ProfileUser;
use App\Models\User;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;

class OrganizationRepository implements OrganizationRepositoryInterface
{

    /**
     * @param mixed $request
     */
    public function all($request)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        return OrganizationResource::collection(
            Organization::when(request('search'), function ($query) {
                $searchObj = json_decode(request('search'));

                if ($searchObj->bUID && $searchObj->bUID != '') {
                    $query->where('bUID', 'like', '%' . strtoupper($searchObj->bUID) . '%');
                }
                if ($searchObj->name && $searchObj->name != '') {
                    $query->where('name', 'like', '%' . $searchObj->name . '%');
                }
                if ($searchObj->company_name && $searchObj->company_name != '') {
                    $query->where('company_name', 'like', '%' . $searchObj->company_name . '%');
                }
                if ($searchObj->director_name && $searchObj->director_name != '') {
                    $query->where('director_name', 'like', '%' . $searchObj->director_name . '%');
                }
                if ($searchObj->email && $searchObj->email != '') {
                    $query->where('email', 'like', '%' . $searchObj->email . '%');
                }
            })->orderBy($field, $order)->paginate($perPage)
        );
    }


    public function create($data)
    {
        // Firstly, we need to add an entry users table
        // with director email address as login id and auto-generated password.

        $user = User::create([
            'name' => $data->director_name,
            'password' => Hash::make(123456789),
            'email' => $data->email,
        ]);
        if (!$user) {
            return false;
        }
        $user->assignRole('Organization');

        $buID = $this->generate_organization_unique_id();

        $input = [
            'bUID' => $buID,
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
        ];
        if (!empty($data->file('director_avatar'))) {
            $input['director_avatar'] = save_image($data->file('director_avatar'), 'organizations', $buID);
        }
        if (Auth::user()) {
            $input['created_by'] = Auth::user()->id;
            $input['updated_by'] = Auth::user()->id;
        }
        $organization = Organization::create($input);

        // Save Bank details
        //$organization->organization_bank_details()->create((array) json_decode($data->organization_bank_details, true, 10));

        // Save documents like PAN file, GST-IN file etc..

        if (!empty($data->documents)) {
            $i = 1;
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                if (isset($document['document_type_id'], $document['value'])) {
                if ($data->file('document_'.$i)) {
                    $organization->organization_documents()->create([
                        'document_type_id'=> $document['document_type_id'],
                        'value' => $document['value'],
                        'storage' => save_documents($data->file('document_'.$i), 'organizations', $buID),
                    ]);
                }
                $i++;
            }
             }
        }
        // Save organization directors
        $organization->organization_directors()->createMany((array) json_decode($data->organization_directors, true, 10));

        // Save organization works
        $organization->organization_works()->createMany((array) json_decode($data->organization_works, true, 10));

        if ($organization->mobile_no) {
            $user->mobile = $organization->mobile_no;
            $user->save();
        }

        $profile = new ProfileUser([
            'mobile_no' => $organization->mobile_no,
        ]);

        event(new Registered($user));

        $user->profile_user()->save($profile);

        return $organization;
    }

    public function update($data, $organization)
    {
        if ($data->form === 'basic') {
            $organization = $this->_saveBasic($data, $organization);
        }

        if ($data->form === 'partners') {
            $organization = $this->_savePartners($data, $organization);
        }

        if ($data->form === 'works') {
            $organization = $this->_saveWorks($data, $organization);
        }

        if ($data->form === 'documents') {
            $organization = $this->_saveDocument($data, $organization);
        }
        return $organization;
    }

    private function _saveBasic($data, $organization)
    {
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
        ];
        if (Auth::user()) {
            $input['updated_by'] = Auth::user()->id;
        }
        if (!empty($data->file('director_avatar'))) {
            if (file_exists($organization->director_avatar)) {
                unlink($organization->director_avatar);
            }
            $input['director_avatar'] = save_image($data->file('director_avatar'), 'organizations', $organization->bUID);
        }
        $organization->update($input);
        return $organization;
    }

    private function _savePartners($data, $organization)
    {
        // Save contractor directors
        if (!empty((array) json_decode($data->organization_directors))) {

            foreach ((array) json_decode($data->organization_directors) as $partner) {
                if (isset($partner->id)) {
                    $dirObj = OrganizationDirector::find($partner->id);
                    $dirObj->name = $partner->name;
                    $dirObj->qualification = $partner->qualification;
                    $dirObj->experience = $partner->experience;
                    $dirObj->save();
                } else {
                    $organization->organization_directors()->create([
                        'name' => $partner->name,
                        'qualification' => $partner->qualification,
                        'experience' => $partner->experience,
                    ]);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->partner_removed)) {
            OrganizationDirector::destroy((array) json_decode($data->partner_removed));
        }

        return $organization;
    }

    private function _saveWorks($data, $organization)
    {
        // Save contractor directors
        if (!empty((array) json_decode($data->organization_works))) {

            foreach ((array) json_decode($data->organization_works) as $work) {

                if (isset($work->id)) {
                    $obj = OrganizationWork::find($work->id);
                    $obj->name = $work->name;
                    $obj->number_buildings = $work->number_buildings;
                    $obj->number_floors = $work->number_floors;
                    $obj->total_area = $work->total_area;
                    $obj->location = $work->location;
                    $obj->planned_completion_date = $work->planned_completion_date;
                    $obj->actual_completion_date = $work->actual_completion_date;
                    $obj->type_construction = $work->type_construction;

                    $obj->save();
                } else {
                    $organization->organization_works()->create((array) $work);
                }
            }
        }

        // Now we will all the items that are removed.
        if (!empty($data->work_removed)) {
            OrganizationWork::destroy((array) json_decode($data->work_removed));
        }
        return $organization;
    }

    private function _saveDocument($data, $organization)
    {
        // Save documents like PAN file, GST-IN file etc..

        if (!empty($data->documents)) {
            $i = 0;
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                $storage = null;
                if ($data->file('document_'.$i)) {
                    $storage = save_documents($data->file('document_'.$i), 'organizations', $organization->bUID);
                }
                if (isset($document['id'])) {

                    $docObj = OrganizationDocument::find($document['id']);
                    $docObj->value = $document['document_value'];

                    if ($storage) {
                        $docObj->storage = $storage;
                    }
                    $docObj->save();
                }
                $i++;
             }
        }

        return $organization;
    }


    private function generate_organization_unique_id():string
    {

        $i = 1;
        $organization = Organization::latest()->first();
        $bUID = 'PLB-'.str_pad($organization->id +1, 4, '0', STR_PAD_LEFT);

        while (Organization::where('bUID', $bUID)->exists()) {
            $bUID = 'PLB-'.str_pad($organization->id + ++$i, 4, '0', STR_PAD_LEFT);
        }
        return $bUID;
    }


    public function delete($organization)
    {
        $obj = Organization::findOrFail($organization->id);
        return $obj->delete();
    }

    public function findByUser($userId)
    {
        return OrganizationResource::make(Organization::where('user_id', $userId)->first());
    }
}
