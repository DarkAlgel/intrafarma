<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserRole;
use App\Models\UserPermission;
use App\Models\PermissionChangeLog;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $permissionsById = $permissions->keyBy('id');
        $permissionDescriptions = [];
        foreach ($permissions as $p) {
            $permissionDescriptions[$p->id] = match($p->code) {
                'manage_users' => 'Gerencia cadastro, edição e exclusão de usuários.',
                'manage_permissions' => 'Gerencia concessão e revogação de permissões.',
                'view_account' => 'Acessa e edita dados da própria conta.',
                'change_password' => 'Altera a própria senha com segurança.',
                'view_stock' => 'Visualiza informações de estoque.',
                'view_dispensation' => 'Visualiza registro de dispensações.',
                default => 'Acesso relacionado à funcionalidade do sistema.'
            };
        }
        $userRoles = UserRole::pluck('role_id', 'user_id');
        $userPerms = UserPermission::select('user_id', 'permission_id')->get()->groupBy('user_id');
        $rolePerms = RolePermission::select('role_id', 'permission_id')->get()->groupBy('role_id');
        $rolePermNames = [];
        foreach ($roles as $r) {
            $pids = ($rolePerms[$r->id] ?? collect())->pluck('permission_id')->all();
            $rolePermNames[$r->id] = collect($pids)->map(fn($id) => $permissionsById[$id]->name ?? null)->filter()->values()->all();
        }
        $effectivePermsByUser = [];
        foreach ($users as $u) {
            $rid = $userRoles[$u->id] ?? null;
            $roleNames = $rid ? ($rolePermNames[$rid] ?? []) : [];
            $userPids = ($userPerms[$u->id] ?? collect())->pluck('permission_id')->all();
            $userNames = collect($userPids)->map(fn($id) => $permissionsById[$id]->name ?? null)->filter()->values()->all();
            $effectivePermsByUser[$u->id] = collect(array_merge($roleNames, $userNames))->unique()->values()->all();
        }
        return view('usuarios.index', compact('users', 'roles', 'permissions', 'userRoles', 'userPerms', 'rolePermNames', 'effectivePermsByUser', 'permissionDescriptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'integer'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
            'initial_permission_id' => ['nullable', 'integer'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        if (!empty($data['role_id'])) {
            UserRole::create(['user_id' => $user->id, 'role_id' => $data['role_id']]);
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'assign_role',
                'details' => json_encode(['role_id' => $data['role_id']]),
            ]);
        }
        if (!empty($data['permission_ids'])) {
            foreach ($data['permission_ids'] as $pid) {
                UserPermission::firstOrCreate(['user_id' => $user->id, 'permission_id' => $pid]);
            }
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'user_set_permissions',
                'details' => json_encode(['permission_ids' => $data['permission_ids']]),
            ]);
        }
        if (!empty($data['initial_permission_id'])) {
            UserPermission::firstOrCreate(['user_id' => $user->id, 'permission_id' => $data['initial_permission_id']]);
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'user_add_permission',
                'details' => json_encode(['permission_id' => $data['initial_permission_id']]),
            ]);
        }
        return redirect()->route('usuarios.index');
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role_id' => ['nullable', 'integer'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
            'set_permission_id' => ['nullable', 'integer'],
        ]);
        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        if (!empty($data['role_id'])) {
            UserRole::where('user_id', $user->id)->delete();
            UserRole::create(['user_id' => $user->id, 'role_id' => $data['role_id']]);
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'change_role',
                'details' => json_encode(['role_id' => $data['role_id']]),
            ]);
        }
        if (array_key_exists('permission_ids', $data)) {
            UserPermission::where('user_id', $user->id)->delete();
            foreach ($data['permission_ids'] ?? [] as $pid) {
                UserPermission::firstOrCreate(['user_id' => $user->id, 'permission_id' => $pid]);
            }
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'user_set_permissions',
                'details' => json_encode(['permission_ids' => $data['permission_ids'] ?? []]),
            ]);
        }
        if (!empty($data['set_permission_id'])) {
            UserPermission::where('user_id', $user->id)->delete();
            UserPermission::firstOrCreate(['user_id' => $user->id, 'permission_id' => $data['set_permission_id']]);
            PermissionChangeLog::create([
                'user_id' => $user->id,
                'actor_id' => $request->user()->id,
                'action' => 'user_set_permission_single',
                'details' => json_encode(['permission_id' => $data['set_permission_id']]),
            ]);
        }
        return redirect()->route('usuarios.index');
    }
}