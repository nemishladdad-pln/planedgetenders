<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Services\Organization\OrganizationService;


class OrganizationController extends Controller
{
    public function __construct(protected OrganizationService $organizationService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->organizationService->all($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        $organization = $this->organizationService->create($request);

        if (!$organization) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $organization], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return OrganizationResource::make(Organization::findOrFail($organization->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $organization = $this->organizationService->update($request, $organization);

        if (!$organization) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $organization], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization = $this->organizationService->delete($organization);

        if (!$organization) {
            return response()->json(['message' => 'There are a few errors while removing organization. Please check again.'], 403);
        }
        return response()->json(['message' => 'Deleted Successfully', 'data' => $organization], 201);
    }


    public function register(StoreOrganizationRequest $request)
    {
        $organization = $this->organizationService->create($request);

        if (!$organization) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Registered Successfully', 'data' => $organization], 201);
    }
}
