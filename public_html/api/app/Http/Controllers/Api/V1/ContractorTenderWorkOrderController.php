<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractorTenderWorkOrder;
use App\Http\Requests\StoreContractorTenderWorkOrderRequest;
use App\Http\Requests\UpdateContractorTenderWorkOrderRequest;
use App\Http\Resources\ContractorTenderWorkOrderResource;
use App\Models\ContractorTender;
use App\Services\ContractorTenderWorkOrder\ContractorTenderWorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorTenderWorkOrderController extends Controller
{

    public function __construct(protected ContractorTenderWorkOrderService $contractorTenderWorkOrderService)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->contractorTenderWorkOrderService->all($request, Auth::user()->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractorTenderWorkOrderRequest $request)
    {
        if (!$request->validated()) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        $contractorTenderWorkOrder = $this->contractorTenderWorkOrderService->create($request, Auth::user()->id);
        if (!$contractorTenderWorkOrder) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Work Order Generated Successfully', 'data' => $contractorTenderWorkOrder], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorTenderWorkOrder $contractorTenderWorkOrder)
    {
        return ContractorTenderWorkOrderResource::make(ContractorTenderWorkOrder::findOrFail($contractorTenderWorkOrder->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorTenderWorkOrderRequest $request, ContractorTenderWorkOrder $contractorTenderWorkOrder)
    {
        // if (!$request->validated()) {
        //     return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        // }
        $contractorTenderWorkOrder = $this->contractorTenderWorkOrderService->update($request, Auth::user()->id);
        if (!$contractorTenderWorkOrder) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Work Order Generated Successfully', 'data' => $contractorTenderWorkOrder], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorTenderWorkOrder $contractorTenderWorkOrder)
    {
        //
    }

}
