<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorBankDetail;
use App\Http\Requests\StoreContractorBankDetailRequest;
use App\Http\Requests\UpdateContractorBankDetailRequest;

class ContractorBankDetailController extends Controller
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
    public function store(StoreContractorBankDetailRequest $request)
    {
        $contractorBankDetail = ContractorBankDetail::create($request->all());
        return response()->json($contractorBankDetail, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorBankDetail $contractorBankDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorBankDetailRequest $request, ContractorBankDetail $contractorBankDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorBankDetail $contractorBankDetail)
    {
        //
    }
}
