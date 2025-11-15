<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionService;

class UserAdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => \Database\Seeders\AdminUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\PatientUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesPermissionsSeeder::class]);
    }

    public function test_criacao_usuario_com_papel_staff_concede_permissoes_do_papel()
    {
        $admin = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($admin);
        $roleStaff = Role::where('code', 'staff')->first();
        $resp = $this->post(route('usuarios.store'), [
            'name' => 'Funcionario 1',
            'email' => 'func1@intrafarma.com',
            'password' => 'Senha123',
            'password_confirmation' => 'Senha123',
            'role_id' => $roleStaff->id,
        ]);
        $resp->assertRedirect(route('usuarios.index'));
        $u = User::where('email', 'func1@intrafarma.com')->first();
        $this->assertTrue(PermissionService::userHas($u->id, 'view_stock'));
        $this->assertTrue(PermissionService::userHas($u->id, 'view_dispensation'));
    }

    public function test_atribuicao_permissoes_diretas_para_usuario()
    {
        $admin = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($admin);
        $rolePatient = Role::where('code', 'patient')->first();
        $permViewDisp = Permission::where('code', 'view_dispensation')->first();

        $this->post(route('usuarios.store'), [
            'name' => 'Paciente 2',
            'email' => 'paciente2@intrafarma.com',
            'password' => 'Senha123',
            'password_confirmation' => 'Senha123',
            'role_id' => $rolePatient->id,
            'permission_ids' => [$permViewDisp->id],
        ])->assertRedirect(route('usuarios.index'));

        $u = User::where('email', 'paciente2@intrafarma.com')->first();
        $this->assertTrue(PermissionService::userHas($u->id, 'view_dispensation'));
        $this->assertTrue(PermissionService::userHas($u->id, 'view_account'));
    }

    public function test_troca_de_papel_para_admin_concede_manage_users()
    {
        $admin = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($admin);
        $roleStaff = Role::where('code', 'staff')->first();
        $roleAdmin = Role::where('code', 'admin')->first();

        $this->post(route('usuarios.store'), [
            'name' => 'Funcionario 3',
            'email' => 'func3@intrafarma.com',
            'password' => 'Senha123',
            'password_confirmation' => 'Senha123',
            'role_id' => $roleStaff->id,
        ])->assertRedirect(route('usuarios.index'));

        $u = User::where('email', 'func3@intrafarma.com')->first();

        $this->put(route('usuarios.update', $u->id), [
            'name' => $u->name,
            'email' => $u->email,
            'role_id' => $roleAdmin->id,
        ])->assertRedirect(route('usuarios.index'));

        $this->assertTrue(PermissionService::userHas($u->id, 'manage_users'));
    }
}