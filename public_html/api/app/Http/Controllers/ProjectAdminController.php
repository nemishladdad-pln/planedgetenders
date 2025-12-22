<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ProjectAdminController extends Controller
{
    protected function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('Admin')) {
            abort(403, 'forbidden');
        }
    }

    // GET /api/admin/projects
    public function listProjects()
    {
        $this->authorizeAdmin();
        // Prefer Eloquent Project model if available; otherwise fallback to DB
        if (class_exists('\\App\\Models\\Project')) {
            $projects = \App\Models\Project::select('id','name')->get();
        } else {
            $projects = DB::table('projects')->select('id','name')->get();
        }
        return response()->json($projects);
    }

    // GET /api/admin/projects/{id}/roles  -> roles assigned to project
    public function getProjectRoles($projectId)
    {
        $this->authorizeAdmin();
        $assigned = DB::table('project_role_assignments')
            ->where('project_id', $projectId)
            ->join('roles', 'project_role_assignments.role_id', '=', 'roles.id')
            ->select('roles.id','roles.name')
            ->get();
        return response()->json($assigned);
    }

    // GET /api/admin/roles -> list available roles
    public function listRoles()
    {
        $this->authorizeAdmin();
        $roles = Role::all(['id','name']);
        return response()->json($roles);
    }

    // POST /api/admin/projects/{id}/roles -> replace roles for single project
    public function assignRolesToProject(Request $request, $projectId)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        DB::transaction(function () use ($projectId, $data) {
            DB::table('project_role_assignments')->where('project_id', $projectId)->delete();
            $now = now();
            $rows = [];
            foreach ($data['roles'] as $rid) {
                $rows[] = [
                    'project_id' => $projectId,
                    'role_id' => $rid,
                    'created_by' => auth()->id(),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            if (!empty($rows)) DB::table('project_role_assignments')->insert($rows);
        });

        return response()->json(['message' => 'assigned']);
    }

    // NEW: POST /api/admin/projects/assign-multiple
    // Body: { project_ids: [1,2], roles: [roleId,...] }
    public function assignRolesToProjects(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        DB::transaction(function () use ($data) {
            $now = now();
            foreach ($data['project_ids'] as $projectId) {
                DB::table('project_role_assignments')->where('project_id', $projectId)->delete();
                $rows = [];
                foreach ($data['roles'] as $rid) {
                    $rows[] = [
                        'project_id' => $projectId,
                        'role_id' => $rid,
                        'created_by' => auth()->id(),
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                if (!empty($rows)) DB::table('project_role_assignments')->insert($rows);
            }
        });

        return response()->json(['message' => 'assigned_to_multiple_projects']);
    }

    // DELETE /api/admin/projects/{id}/roles/{roleId}
    public function removeRoleFromProject($projectId, $roleId)
    {
        $this->authorizeAdmin();
        DB::table('project_role_assignments')->where('project_id',$projectId)->where('role_id',$roleId)->delete();
        return response()->json(['message' => 'removed']);
    }

    // GET /api/admin/project-assignments -> list all assignments (for admin table), include project name when possible
    public function listAssignments()
    {
        $this->authorizeAdmin();

        $hasProjects = DB::getSchemaBuilder()->hasTable('projects');

        if ($hasProjects) {
            $rows = DB::table('project_role_assignments')
                ->join('roles','project_role_assignments.role_id','=','roles.id')
                ->leftJoin('projects','project_role_assignments.project_id','=','projects.id')
                ->select(
                    'project_role_assignments.id',
                    'project_role_assignments.project_id',
                    'projects.name as project_name',
                    'roles.id as role_id',
                    'roles.name as role_name',
                    'project_role_assignments.created_by',
                    'project_role_assignments.created_at'
                )
                ->get();
        } else {
            $rows = DB::table('project_role_assignments')
                ->join('roles','project_role_assignments.role_id','=','roles.id')
                ->select(
                    'project_role_assignments.id',
                    'project_role_assignments.project_id',
                    'roles.id as role_id',
                    'roles.name as role_name',
                    'project_role_assignments.created_by',
                    'project_role_assignments.created_at'
                )
                ->get();
        }

        return response()->json($rows);
    }
}
