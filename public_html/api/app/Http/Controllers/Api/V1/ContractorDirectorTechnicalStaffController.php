<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorDirectorTechnicalStaff;
use App\Http\Requests\StoreContractorDirectorTechnicalStaffRequest;
use App\Http\Requests\UpdateContractorDirectorTechnicalStaffRequest;

class ContractorDirectorTechnicalStaffController extends Controller
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
    public function store(StoreContractorDirectorTechnicalStaffRequest $request)
    {
        $contractorDirectorTechnicalStaff = ContractorDirectorTechnicalStaff::create($request->all());
        return response()->json($contractorDirectorTechnicalStaff, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorDirectorTechnicalStaff $contractorDirectorTechnicalStaff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorDirectorTechnicalStaffRequest $request, ContractorDirectorTechnicalStaff $contractorDirectorTechnicalStaff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorDirectorTechnicalStaff $contractorDirectorTechnicalStaff)
    {
        //
    }
}
