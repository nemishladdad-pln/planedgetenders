<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\Role\RoleService;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->roleService->all($request);
    }

    public function edit(Role $role)
    {
        list($response, $final) = $this->roleService->edit($role);

        return response()->json(['response' => $response, 'final' => $final], 200);
    }


    public function show(Role $role)
    {
        list($response, $final) = $this->roleService->show($role);

        return response()->json(['response' => $response, 'final' => $final], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request);

        if (!$role) {
            return response()->json(['message' => 'There are a few errors. Please check again.'], 403);
        }
        return response()->json(['message' => 'Save Successfully', 'data' => $role], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleService->update($request, $role);

        if (!$role) {
            return response()->json(['message' => 'There are a few errors. Please check again.'], 403);
        }
        return response()->json(['message' => 'Save Successfully', 'data' => $role], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $response = [
            'success' => false,
            'message' => null,
            'errors' => null,
        ];

        if ($role->delete()) {
            $response = [
                'success' => true,
                'message' => 'Role deleted successfully.',
                'roles' => Role::latest()->get(),
            ];
        }
        return response()->json($response);
    }
}
