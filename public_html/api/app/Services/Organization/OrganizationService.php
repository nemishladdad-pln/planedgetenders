<?php
namespace App\Services\Organization;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Http\Request;

class OrganizationService
{

    public function __construct(protected OrganizationRepositoryInterface $organizationRepository) { }

    public function all($request)
    {
        return $this->organizationRepository->all($request);
    }


    public function create(StoreOrganizationRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->organizationRepository->create($request);
    }

    public function update(Request $request, $organization)
    {
        // if (!$request->validated()) {
        //     return false;
        // }

        return $this->organizationRepository->update($request, $organization);
    }

    public function delete(Organization $organization)
    {
        return $this->organizationRepository->delete($organization);
    }

    public function findByUser($userId) {
        return $this->organizationRepository->findByUser($userId);
    }


}
