<?php
namespace App\Services\Role;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleService
{

    public function __construct(protected RoleRepositoryInterface $roleRepository) { }

    public function all($request)
    {
        return $this->roleRepository->all($request);
    }


    public function create(StoreRoleRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->roleRepository->create($request);
    }

    public function edit(Role $role): mixed
    {
        if (!$role) {
            return false;
        }
        return $this->roleRepository->getInfo($role);
    }

    public function update(UpdateRoleRequest $request, $role)
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->roleRepository->update($request, $role);
    }

    public function show(Role $role): mixed
    {
        if (!$role) {
            return false;
        }
        return $this->roleRepository->getInfo($role);
    }
}
