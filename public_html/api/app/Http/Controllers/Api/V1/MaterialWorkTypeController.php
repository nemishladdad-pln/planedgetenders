<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MaterialWorkType;
use App\Http\Requests\StoreMaterialWorkTypeRequest;
use App\Http\Requests\UpdateMaterialWorkTypeRequest;

class MaterialWorkTypeController extends Controller
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
    public function store(StoreMaterialWorkTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialWorkType $materialWorkType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialWorkTypeRequest $request, MaterialWorkType $materialWorkType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialWorkType $materialWorkType)
    {
        //
    }
}
