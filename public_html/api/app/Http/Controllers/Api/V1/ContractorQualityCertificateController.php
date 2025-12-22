<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorQualityCertificate;
use App\Http\Requests\StoreContractorQualityCertificateRequest;
use App\Http\Requests\UpdateContractorQualityCertificateRequest;

class ContractorQualityCertificateController extends Controller
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
    public function store(StoreContractorQualityCertificateRequest $request)
    {
        $contractorQualityCertificate = ContractorQualityCertificate::create($request->all());
        return response()->json($contractorQualityCertificate, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorQualityCertificate $contractorQualityCertificate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorQualityCertificateRequest $request, ContractorQualityCertificate $contractorQualityCertificate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorQualityCertificate $contractorQualityCertificate)
    {
        //
    }
}
