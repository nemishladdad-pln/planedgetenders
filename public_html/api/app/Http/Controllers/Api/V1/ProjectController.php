<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Services\Project\ProjectService;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $projectService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->projectService->all($request, Auth::user()->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->create($request);
        if (!$project) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Project created Successfully', 'data' => $project], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return ProjectResource::make(Project::findOrFail($project->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project = $this->projectService->update($request, $project);
        if (!$project) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Project updated Successfully', 'data' => $project], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if (!$project->delete()) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Information deleted Successfully'], 201);
    }
}
