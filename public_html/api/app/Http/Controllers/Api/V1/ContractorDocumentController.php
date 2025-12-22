<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorDocument;
use App\Http\Requests\StoreContractorDocumentRequest;
use App\Http\Requests\UpdateContractorDocumentRequest;

class ContractorDocumentController extends Controller
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
    public function store(StoreContractorDocumentRequest $request)
    {
        $contractorDocument = ContractorDocument::create($request->all());
        return response()->json($contractorDocument, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorDocument $contractorDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorDocumentRequest $request, ContractorDocument $contractorDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorDocument $contractorDocument)
    {
        //
    }
}
