<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate')->run();
        // seed roles/permissions if seeder exists
        if (class_exists(\Database\Seeders\PermissionRoleUserSeeder::class)) {
            $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PermissionRoleUserSeeder'])->run();
        }
    }

    public function test_admin_can_assign_roles_to_project()
    {
        // create an admin user and give Admin role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $user->assignRole($role);

        // create a fake project row
        DB::table('projects')->insert(['id' => 1, 'name' => 'Test Project', 'created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user, 'sanctum');

        // create another role to assign
        $r2 = Role::create(['name' => 'ProjectManager']);

        $resp = $this->postJson('/api/admin/projects/1/roles', ['roles' => [$r2->id]]);
        $resp->assertStatus(200)->assertJson(['message' => 'assigned']);

        // verify assignment exists
        $this->assertDatabaseHas('project_role_assignments', ['project_id' => 1, 'role_id' => $r2->id]);
    }

    public function test_get_project_roles_returns_assigned_roles()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $user->assignRole($role);

        DB::table('projects')->insert(['id' => 2, 'name' => 'Project Two', 'created_at' => now(), 'updated_at' => now()]);
        $r = Role::create(['name' => 'Contractor']);
        DB::table('project_role_assignments')->insert([
            'project_id' => 2, 'role_id' => $r->id, 'created_by' => $user->id, 'created_at' => now(), 'updated_at' => now()
        ]);

        $this->actingAs($user, 'sanctum');
        $res = $this->getJson('/api/admin/projects/2/roles');
        $res->assertStatus(200)->assertJsonFragment(['name' => 'Contractor']);
    }
}
