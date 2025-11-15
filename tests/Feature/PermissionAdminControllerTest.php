<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => \Database\Seeders\AdminUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\PatientUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesPermissionsSeeder::class]);
    }

    private function actingAsAdmin(): User
    {
        $user = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        return $user;
    }

    public function test_index_page_loads(): void
    {
        $this->actingAsAdmin();
        $resp = $this->get(route('permissoes.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Permissões');
    }

    public function test_create_role_with_permissions(): void
    {
        $this->actingAsAdmin();
        $permIds = Permission::pluck('id')->slice(0, 2)->values()->all();
        $resp = $this->post(route('permissoes.roles.create'), [
            'name' => 'Supervisores',
            'permission_ids' => $permIds,
        ]);
        $resp->assertRedirect(route('permissoes.index'));
        $role = Role::where('code', 'supervisores')->first();
        $this->assertNotNull($role);
        foreach ($permIds as $pid) {
            $this->assertTrue(RolePermission::where(['role_id' => $role->id, 'permission_id' => $pid])->exists());
        }
    }

    public function test_update_role_changes_name_and_permissions(): void
    {
        $this->actingAsAdmin();
        $role = Role::create(['code' => 'temp_role', 'name' => 'Temporário']);
        $permIds = Permission::pluck('id')->slice(0, 3)->values()->all();
        foreach ($permIds as $pid) { RolePermission::firstOrCreate(['role_id' => $role->id, 'permission_id' => $pid]); }

        $newPerms = Permission::pluck('id')->slice(1, 2)->values()->all();
        $resp = $this->put(route('permissoes.roles.update', ['id' => $role->id]), [
            'name' => 'Atualizado',
            'permission_ids' => $newPerms,
        ]);
        $resp->assertRedirect(route('permissoes.index'));
        $role->refresh();
        $this->assertEquals('Atualizado', $role->name);
        $this->assertEquals(count($newPerms), RolePermission::where('role_id', $role->id)->count());
    }

    public function test_delete_role_removes_role_and_assignments(): void
    {
        $this->actingAsAdmin();
        $role = Role::create(['code' => 'to_delete', 'name' => 'Excluir']);
        $pid = Permission::pluck('id')->first();
        RolePermission::firstOrCreate(['role_id' => $role->id, 'permission_id' => $pid]);
        $resp = $this->delete(route('permissoes.roles.delete', ['id' => $role->id]));
        $resp->assertRedirect(route('permissoes.index'));
        $this->assertFalse(Role::where('id', $role->id)->exists());
        $this->assertFalse(RolePermission::where('role_id', $role->id)->exists());
    }

    public function test_assign_and_revoke_permission(): void
    {
        $this->actingAsAdmin();
        $role = Role::where('code', 'staff')->first();
        $pid = Permission::pluck('id')->last();
        $respAssign = $this->post(route('permissoes.assign'), [
            'role_id' => $role->id,
            'permission_id' => $pid,
        ]);
        $respAssign->assertRedirect(route('permissoes.index'));
        $this->assertTrue(RolePermission::where(['role_id' => $role->id, 'permission_id' => $pid])->exists());
        $respRevoke = $this->post(route('permissoes.revoke'), [
            'role_id' => $role->id,
            'permission_id' => $pid,
        ]);
        $respRevoke->assertRedirect(route('permissoes.index'));
        $this->assertFalse(RolePermission::where(['role_id' => $role->id, 'permission_id' => $pid])->exists());
    }

    public function test_export_csv_returns_csv_content_type(): void
    {
        $this->actingAsAdmin();
        $resp = $this->get(route('permissoes.export.csv'));
        $resp->assertStatus(200);
        $this->assertStringContainsString('text/csv', $resp->headers->get('Content-Type'));
        $resp->assertSee('role,permission');
    }
}