<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorWork;
use App\Http\Requests\StoreContractorWorkRequest;
use App\Http\Requests\UpdateContractorWorkRequest;

class ContractorWorkController extends Controller
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
    public function store(StoreContractorWorkRequest $request)
    {
        $contractorWork = ContractorWork::create($request->all());
        return response()->json($contractorWork, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(ContractorWork $contractorWork)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorWorkRequest $request, ContractorWork $contractorWork)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorWork $contractorWork)
    {
        //
    }
}
