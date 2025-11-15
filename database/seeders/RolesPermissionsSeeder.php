<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserRole;
use App\Models\User;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function ($table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
            });
        }
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function ($table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
            });
        }
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function ($table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
            });
        }
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('role_id');
            });
        }
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('permission_id');
            });
        }
        if (!Schema::hasTable('permission_change_logs')) {
            Schema::create('permission_change_logs', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('action');
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        $roles = [
            ['code' => 'admin', 'name' => 'Administradores'],
            ['code' => 'staff', 'name' => 'Funcionários'],
            ['code' => 'patient', 'name' => 'Pacientes'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['code' => $r['code']], ['name' => $r['name']]);
        }

        $perms = [
            ['code' => 'manage_users', 'name' => 'Gerenciar Usuários'],
            ['code' => 'manage_permissions', 'name' => 'Gerenciar Permissões'],
            ['code' => 'view_account', 'name' => 'Ver Minha Conta'],
            ['code' => 'change_password', 'name' => 'Alterar Senha'],
            ['code' => 'view_stock', 'name' => 'Ver Estoque'],
            ['code' => 'view_dispensation', 'name' => 'Ver Dispensações'],
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['code' => $p['code']], ['name' => $p['name']]);
        }

        $roleIds = Role::pluck('id', 'code');
        $permIds = Permission::pluck('id', 'code');

        $adminPerms = array_values($permIds->all());
        foreach ($adminPerms as $pid) {
            RolePermission::firstOrCreate(['role_id' => $roleIds['admin'], 'permission_id' => $pid]);
        }

        foreach (['view_stock', 'view_dispensation', 'view_account', 'change_password'] as $code) {
            RolePermission::firstOrCreate(['role_id' => $roleIds['staff'], 'permission_id' => $permIds[$code]]);
        }

        foreach (['view_account', 'change_password'] as $code) {
            RolePermission::firstOrCreate(['role_id' => $roleIds['patient'], 'permission_id' => $permIds[$code]]);
        }

        $admin = User::where('email', 'admin@intrafarma.com')->first();
        if ($admin) {
            UserRole::firstOrCreate(['user_id' => $admin->id, 'role_id' => $roleIds['admin']]);
        }
        $patient = User::where('email', 'paciente@intrafarma.com')->first();
        if ($patient) {
            UserRole::firstOrCreate(['user_id' => $patient->id, 'role_id' => $roleIds['patient']]);
        }
    }
}