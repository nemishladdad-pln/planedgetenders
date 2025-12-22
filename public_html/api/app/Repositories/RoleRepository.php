<?php
namespace App\Repositories;

use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
//use Spatie\Permission\Models\Permission as ModelsPermission;

class RoleRepository implements RoleRepositoryInterface
{

    public function store($data)
    {

    }
    public function all($request)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        return RoleResource::collection(
            Role::when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function create($request)
    {
        $inputs = [
            'name'=> $request->name,
            'guard_name' => 'web',
        ];
        $role = Role::create($inputs);
        $role->syncPermissions($request->permissions);

        return $role;
    }

    public function getInfo($role)
    {
        return $this->permissionWithAction($role);
    }

    public function update($data, $role)
    {
        $inputs = [
            'name'=> $data->name,
            'permissions' => $data->permissions,
        ];
        $role->name = $data->name;
        $role->save($inputs);
        $role->syncPermissions($inputs['permissions']);

        return $role;
    }

    public function delete($role)
    {
        if (!$role) {
            return false;
        }
        return $role->delete();
    }

    private function permissionWithAction($role)
    {
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $permissionId = $permission->id;
            $explodedPermission = array_reverse(explode(' ', $permission->name));

            $moduleName = $explodedPermission[0];

            if (count($explodedPermission) > 2) {
                $j = 0;
                for ($i = 1; $i < count($explodedPermission); $i++ ) {
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
            $final[$moduleName][$key][$permissionId] = $actionName;
        }
        $response = $role;
        $oldPermissions = [];
        $rolePermissions = $role->permissions;
        $i = 0;
        foreach ($rolePermissions as $permission) {
            $oldPermissions[$i++] = $permission->id;
        }
        $response->id = $role->id;
        $response->name = $role->name;
        $response->oldpermissions = $oldPermissions;
        $response->users = $role->users;
        return [$response, $final];
    }
}
