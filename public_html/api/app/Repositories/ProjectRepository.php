<?php
namespace App\Repositories;

use App\Http\Resources\ProjectResource;
use App\Imports\ProjectScheduleImport;
use App\Models\Builder;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectBuilding;
use App\Models\ProjectSchedule;
use App\Models\User;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProjectRepository implements ProjectRepositoryInterface
{

    /**
     * @param mixed $request
     */
    public function all($request, $userId)
    {
        $user = User::findOrFail($userId);
        $userRole = $user->roles->pluck('name', 'id')->toArray();
        if (in_array('Organization', $userRole)) {
            $organization = Organization::where('user_id', $userId)->first();
            $organizationId = $organization->id;
        }
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;



        if (isset($organizationId)) {
            return ProjectResource::collection(
                Project::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->pUID && $searchObj->pUID != '') {
                        $query->where('pUID', 'like', '%' . strtoupper($searchObj->pUID) . '%');
                    }
                    if ($searchObj->name && $searchObj->name != '') {
                        $query->where('name', 'like', '%' . $searchObj->name . '%');
                    }
                    if ($searchObj->total_project_area && $searchObj->total_project_area != '') {
                        $query->where('total_project_area', $searchObj->total_project_area);
                    }
                    if ($searchObj->start_date && $searchObj->start_date != '') {
                        $query->where('start_date', $searchObj->start_date);
                    }
                    if ($searchObj->completion_date && $searchObj->completion_date != '') {
                        $query->where('completion_date', $searchObj->completion_date);
                    }
                })->where('organization_id', $organizationId)->orderBy($field, $order)->paginate($perPage)
            );
        }

        if (in_array('General Manager', $userRole)) {
            return ProjectResource::collection(
                Project::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->pUID && $searchObj->pUID != '') {
                        $query->where('pUID', 'like', '%' . strtoupper($searchObj->pUID) . '%');
                    }
                    if ($searchObj->name && $searchObj->name != '') {
                        $query->where('name', 'like', '%' . $searchObj->name . '%');
                    }
                    if ($searchObj->total_project_area && $searchObj->total_project_area != '') {
                        $query->where('total_project_area', $searchObj->total_project_area);
                    }
                    if ($searchObj->start_date && $searchObj->start_date != '') {
                        $query->where('start_date', $searchObj->start_date);
                    }
                    if ($searchObj->completion_date && $searchObj->completion_date != '') {
                        $query->where('completion_date', $searchObj->completion_date);
                    }
                })->where('general_manager_id', $userId)
                    ->orderBy($field, $order)
                    ->paginate($perPage)
            );
        }
        if (in_array('Site Project Manager', $userRole)) {
            return ProjectResource::collection(
                Project::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->pUID && $searchObj->pUID != '') {
                        $query->where('pUID', 'like', '%' . strtoupper($searchObj->pUID) . '%');
                    }
                    if ($searchObj->name && $searchObj->name != '') {
                        $query->where('name', 'like', '%' . $searchObj->name . '%');
                    }
                    if ($searchObj->total_project_area && $searchObj->total_project_area != '') {
                        $query->where('total_project_area', $searchObj->total_project_area);
                    }
                    if ($searchObj->start_date && $searchObj->start_date != '') {
                        $query->where('start_date', $searchObj->start_date);
                    }
                    if ($searchObj->completion_date && $searchObj->completion_date != '') {
                        $query->where('completion_date', $searchObj->completion_date);
                    }
                })->where('site_project_manager_id', $userId)->orderBy($field, $order)->paginate($perPage)
            );
        }
        return ProjectResource::collection(
            Project::when(request('search'), function ($query) {
                $searchObj = json_decode(request('search'));

                if ($searchObj->pUID && $searchObj->pUID != '') {
                    $query->where('pUID', 'like', '%' . strtoupper($searchObj->pUID) . '%');
                }
                if ($searchObj->name && $searchObj->name != '') {
                    $query->where('name', 'like', '%' . $searchObj->name . '%');
                }
                if ($searchObj->total_project_area && $searchObj->total_project_area != '') {
                    $query->where('total_project_area', 'like', '%' . $searchObj->total_project_area. '%');
                }
                if ($searchObj->start_date && $searchObj->start_date != '') {
                    $query->where('start_date', $searchObj->start_date);
                }
                if ($searchObj->completion_date && $searchObj->completion_date != '') {
                    $query->where('completion_date', $searchObj->completion_date);
                }

            })->orderBy($field, $order)->paginate($perPage)
        );
    }


    public function create($data)
    {
        $input = [
            'pUID' => $data->pUID,
            'name' => $data->name,
            'billing_name' => $data->billing_name,
            'organization_id' => $data->organization_id,
            'location' => $data->location,
            'project_type_id' => $data->project_type_id,
            'total_project_area' => $data->total_project_area,
            'site_project_manager_id' => $data->site_project_manager_id,
            'general_manager_id' => $data->general_manager_id,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'is_active' => 1,
            'start_date' => $data->start_date,
            'completion_date' => $data->completion_date,
        ];
        $project = Project::create($input);

        // Save Bank details

        if (!empty((array) json_decode($data->building_names))) {
            foreach ((array) json_decode($data->building_names) as $building) {
                $project->project_buildings()->create(
                    [
                        'name' => $building->name,
                        'floors' => $building->floors,
                    ]
                );
            }
        }

        // Now save the schedule of work of project.
        $this->__importProjectSchedule($data, $project->id);

        return $project;
    }

    private function __importProjectSchedule($data, $projectId)
    {
        $file = $data->file('schedule_list')->store('import');


        try {
            $response = Excel::import(new ProjectScheduleImport(projectId: (int)$projectId), $file);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $response = [
                'success' => false,
                'message' => '(s) are not imported.',
                'failures' => $failures,
             ];
        }
        return $response;
    }

    public function update($data, $project)
    {
        $input = [
            'name' => $data->name,
            'pUID' => $data->pUID,
            'billing_name' => $data->billing_name,
            'organization_id' => $data->organization_id,
            'location' => $data->location,
            'project_type_id' => $data->project_type_id,
            'total_project_area' => $data->total_project_area,
            'site_project_manager_id' => $data->site_project_manager_id,
            'general_manager_id' => $data->general_manager_id,
            'updated_by' => Auth::user()->id,
            'start_date' => $data->start_date,
            'completion_date' => $data->completion_date,
        ];

        $project->update($input);

        if (!empty($data->project_buildings)) {
            foreach ($data->project_buildings as $building) {
                if (isset($building['id'])) {
                    $obj = ProjectBuilding::find($building['id']);
                    $obj->name = $building['name'];
                    $obj->floors = $building['floors'];
                    $obj->save();
                } else {
                    $project->project_buildings()->create($building);
                }
            }
        }
        // Now we will all the items that are removed.
        if (!empty($data->project_buildings_removed)) {
            ProjectBuilding::destroy($data->project_buildings_removed);
        }
        if (!empty($data->project_schedules)) {
            foreach ($data->project_schedules as $schedule) {
                if (isset($schedule['id'])) {
                    $obj = ProjectSchedule::find($schedule['id']);
                    $obj->task = $schedule['task'];
                    $obj->start_date = $schedule['start_date'];
                    $obj->end_date = $schedule['end_date'];
                    $obj->save();
                } else {
                    $project->project_schedules()->create($schedule);
                }
            }
        }
        // Now we will all the items that are removed.
        if (!empty($data->project_schedules_removed)) {
            ProjectSchedule::destroy($data->project_schedules_removed);
        }
        return $project;
    }

    private function generate_project_unique_id(String $name = null):string
    {
        $count = 0;
        $project = Project::get();
        $count = str_pad( $project->count() + 1, 4, "0", STR_PAD_LEFT );
        return 'PL'.$count;
    }
}
