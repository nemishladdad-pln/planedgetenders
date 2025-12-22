<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorEquipment;
use App\Http\Requests\StoreContractorEquipmentRequest;
use App\Http\Requests\UpdateContractorEquipmentRequest;

class ContractorEquipmentController extends Controller
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
    public function store(StoreContractorEquipmentRequest $request)
    {
        $contractorEquipment = ContractorEquipment::create($request->all());
        return response()->json($contractorEquipment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorEquipment $contractorEquipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorEquipmentRequest $request, ContractorEquipment $contractorEquipment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorEquipment $contractorEquipment)
    {
        //
    }
}
