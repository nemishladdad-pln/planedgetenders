<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorContact;
use App\Http\Requests\StoreContractorContactRequest;
use App\Http\Requests\UpdateContractorContactRequest;

class ContractorContactController extends Controller
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
    public function store(StoreContractorContactRequest $request)
    {
        $contractorContact = ContractorContact::create($request->all());
        return response()->json($contractorContact, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorContact $contractorContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorContactRequest $request, ContractorContact $contractorContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorContact $contractorContact)
    {
        //
    }
}
