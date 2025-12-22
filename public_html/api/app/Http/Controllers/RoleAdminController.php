<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleAdminController extends Controller
{
    // Return all permissions
    public function indexPermissions()
    {
        $this->authorizeAdmin();
        return response()->json(Permission::all()->pluck('name'));
    }

    // Return roles with permissions
    public function indexRoles()
    {
        $this->authorizeAdmin();
        $roles = Role::with('permissions')->get()->map(function ($r) {
            return ['id' => $r->id, 'name' => $r->name, 'permissions' => $r->permissions->pluck('name')];
        });
        return response()->json($roles);
    }

    // Create a new role with optional permissions
    public function createRole(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);
        $role = Role::create(['name' => $data['name']]);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        return response()->json(['role' => $role->load('permissions')], 201);
    }

    // Update role's permissions (sync)
    public function updateRolePermissions(Request $request, $id)
    {
        $this->authorizeAdmin();
        $data = $request->validate(['permissions' => 'nullable|array']);
        $role = Role::findOrFail($id);
        $role->syncPermissions($data['permissions'] ?? []);
        return response()->json(['role' => $role->load('permissions')]);
    }

    // Assign roles to a user (replace roles)
    public function assignRoleToUser(Request $request, $userId)
    {
        $this->authorizeAdmin();
        $data = $request->validate(['roles' => 'required|array']);
        $user = User::findOrFail($userId);
        $user->syncRoles($data['roles']);
        return response()->json(['user' => $user->load('roles')]);
    }

    // List users with roles
    public function usersWithRoles()
    {
        $this->authorizeAdmin();
        $users = User::with('roles')->get()->map(function ($u) {
            return ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'roles' => $u->roles->pluck('name')];
        });
        return response()->json($users);
    }

    // Simple admin check helper
    protected function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('Admin')) {
            abort(403, 'forbidden');
        }
    }
}
