<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Http\Requests\StoreContractorRequest;
use App\Http\Requests\UpdateContractorRequest;
use App\Http\Resources\ContractorResource;
use App\Services\Contractor\ContractorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ContractorController extends Controller
{
    public function __construct(protected ContractorService $contractorService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->contractorService->all($request, Auth::user()->id);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreContractorRequest $request)
    {
        if (!$request->validated()) {
            return false;
        }
        // Create a new contractor record in the database
        $contractor = $this->contractorService->create($request);

        if (!$contractor) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $contractor], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Contractor $contractor)
    {
        return ContractorResource::make(Contractor::findOrFail($contractor->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contractor $contractor)
    {
        // Update contractor details
        $contractor = $this->contractorService->update($request, $contractor);
        if (!$contractor) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Information Updated Successfully', 'data' => $contractor], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contractor $contractor)
    {
        if (!$this->contractorService->delete($contractor)) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Information deleted Successfully'], 201);
    }

    public function register(StoreContractorRequest $request)
    {

        $contractor = $this->contractorService->create($request);

        if (!$contractor) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $contractor], 201);
    }
}
