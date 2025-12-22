<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorTender;
use App\Http\Requests\StoreContractorTenderRequest;
use App\Http\Requests\UpdateContractorTenderRequest;
use App\Http\Resources\ContractorTenderResource;
use App\Models\Tender;
use App\Services\ContractorTender\ContractorTenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorTenderController extends Controller
{
    public function __construct(protected ContractorTenderService $contractorTenderService)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->contractorTenderService->all($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $contractorTender = $this->contractorTenderService->create($request);
        if (!$contractorTender) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $contractorTender], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorTender $contractorTender)
    {
        return ContractorTenderResource::make(ContractorTender::findOrFail($contractorTender->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorTenderRequest $request, ContractorTender $contractorTender)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorTender $contractorTender)
    {
        //
    }

    public function contractors_bids(Request $request)
    {
        return $this->contractorTenderService->contractors_bids($request, Auth::user()->id);
    }

    public function tender_wise(Request $request)
    {
        return $this->contractorTenderService->tender_wise($request, Auth::user()->id);
    }

    public function my_tenders(Request $request)
    {
        return $this->contractorTenderService->my_tenders($request, Auth::user()->id);
    }

    public function no_of_tenders(Request $request)
    {
        return $this->contractorTenderService->no_of_tenders($request);
    }

    public function automatic_comparison(Tender $tender)
    {
        return $this->contractorTenderService->automatic_comparison($tender);
    }
}
