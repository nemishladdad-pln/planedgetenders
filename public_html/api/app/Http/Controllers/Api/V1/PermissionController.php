<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Permission::class, 'permission');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $field = $request->input('sortBy') ?? 'id';
        $order = $request->input('sortType') ?? 'desc';
        $perPage = $request->input('limit') ?? 10;

        $permissions = PermissionResource::collection(
            Permission::when($request->input('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
                $query->orWhere('guard_name', 'like', '%' . request('search') . '%');
            })->orderBy($field, $order)->paginate($perPage)
        );

        //dd($permissions);
        return $permissions;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        if ($request->validated()) {
            $inputs = [
                'name' => $request->name,
                'guard_name' => 'web',
            ];
            $permission = Permission::create($inputs);

            $response = [
                'success' => true,
                'message' => 'Permission created successfully.',
                'permission' => $permission,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Oops, there seems to have some errors.',
                'errors' => $this->validated()->errors(),
            ];
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $inputs = $request->all();
        if ($request->validated()) {
            $permission->update($inputs);

            $response = [
                'success' => true,
                'message' => 'Permission updated successfully.',
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Oops, there seems to have some errors.',
                'errors' => $this->validated()->errors(),
            ];
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $response = [
            'success' => false,
            'message' => null,
            'errors' => null,
        ];

        if ($permission->delete()) {
            $response = [
                'success' => true,
                'message' => 'Permission deleted successfully.',
                'permission' => $permission,
                'permissions' => Permission::latest()->get(),
            ];
        }
        return response()->json($response);
    }

    public function modules()
    {
        $permissions = Permission::all();
        $final = [];
        foreach ($permissions as $permission) {
            $permissionId = $permission->id;
            $explodedPermission = array_reverse(explode(' ', $permission->name));

            $moduleName = $explodedPermission[0];

            if (count($explodedPermission) > 2) {
                $j = 0;
                for ($i = 1; $i < count($explodedPermission); $i++) {
                    $remaining[$j++] = $explodedPermission[$i];
                }
                $actionName = implode(' ', array_reverse($remaining));
            } else {
                $actionName = $explodedPermission[1];
            }
            $key = 'All';
            if (isset($remaining) && count($remaining) > 0) {
                $key = 'Own';
            }
            $final[$key . " " . ucFirst($moduleName) . " Management"][$permissionId] = $actionName;
        }
        return response()->json($final);
    }
}
