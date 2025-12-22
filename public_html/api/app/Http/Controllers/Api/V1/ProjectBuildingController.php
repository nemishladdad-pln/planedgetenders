<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProjectBuilding;
use App\Http\Requests\StoreProjectBuildingRequest;
use App\Http\Requests\UpdateProjectBuildingRequest;

class ProjectBuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectBuildingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectBuilding $projectBuilding)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectBuildingRequest $request, ProjectBuilding $projectBuilding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectBuilding $projectBuilding)
    {
        //
    }
}
