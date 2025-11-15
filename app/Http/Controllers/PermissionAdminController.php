<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\PermissionChangeLog;

class PermissionAdminController extends Controller
{
    public function index(Request $request)
    {
        $sort = in_array($request->query('sort'), ['name','created_at']) ? $request->query('sort') : 'name';
        $dir = in_array($request->query('dir'), ['asc','desc']) ? $request->query('dir') : 'asc';
        $roles = Role::orderBy($sort, $dir)->paginate(10);
        $permissions = Permission::orderBy('name')->get();
        $assigned = RolePermission::get();
        $defaultCodes = ['admin','staff','patient'];
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
        return view('permissoes.index', compact('roles', 'permissions', 'assigned', 'defaultCodes', 'permissionDescriptions', 'sort', 'dir'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
            'permission_id' => ['required', 'integer'],
        ]);
        RolePermission::firstOrCreate($data);
        PermissionChangeLog::create([
            'user_id' => null,
            'actor_id' => $request->user()->id,
            'action' => 'role_add_permission',
            'details' => json_encode($data),
        ]);
        return redirect()->route('permissoes.index');
    }

    public function revoke(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
            'permission_id' => ['required', 'integer'],
        ]);
        RolePermission::where($data)->delete();
        PermissionChangeLog::create([
            'user_id' => null,
            'actor_id' => $request->user()->id,
            'action' => 'role_remove_permission',
            'details' => json_encode($data),
        ]);
        return redirect()->route('permissoes.index');
    }

    public function createRole(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
        ]);
        $role = Role::create(['name' => $data['name'], 'code' => strtolower(str_replace(' ', '_', $data['name']))]);
        foreach (($data['permission_ids'] ?? []) as $pid) {
            RolePermission::firstOrCreate(['role_id' => $role->id, 'permission_id' => $pid]);
        }
        PermissionChangeLog::create([
            'user_id' => null,
            'actor_id' => $request->user()->id,
            'action' => 'create_role',
            'details' => json_encode(['role_id' => $role->id, 'name' => $role->name, 'permissions' => $data['permission_ids'] ?? []]),
        ]);
        return redirect()->route('permissoes.index');
    }

    public function updateRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        if (in_array($role->code, ['admin','staff','patient'])) {
            abort(403);
        }
        $data = $request->validate([
            'name' => ['required', 'string'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
        ]);
        $role->update(['name' => $data['name']]);
        RolePermission::where('role_id', $role->id)->delete();
        foreach (($data['permission_ids'] ?? []) as $pid) {
            RolePermission::firstOrCreate(['role_id' => $role->id, 'permission_id' => $pid]);
        }
        PermissionChangeLog::create([
            'user_id' => null,
            'actor_id' => $request->user()->id,
            'action' => 'update_role',
            'details' => json_encode(['role_id' => $role->id, 'name' => $role->name, 'permissions' => $data['permission_ids'] ?? []]),
        ]);
        return redirect()->route('permissoes.index');
    }

    public function deleteRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        if (in_array($role->code, ['admin','staff','patient'])) {
            abort(403);
        }
        RolePermission::where('role_id', $role->id)->delete();
        $role->delete();
        PermissionChangeLog::create([
            'user_id' => null,
            'actor_id' => $request->user()->id,
            'action' => 'delete_role',
            'details' => json_encode(['role_id' => $id]),
        ]);
        return redirect()->route('permissoes.index');
    }

    public function exportCsv()
    {
        $rows = Role::leftJoin('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
            ->leftJoin('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('roles.name as role', 'permissions.name as permission')
            ->orderBy('roles.name')->get();
        $csv = "role,permission\n";
        foreach ($rows as $r) {
            $csv .= sprintf("%s,%s\n", $r->role, $r->permission ?? '');
        }
        return response($csv)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="roles_permissions.csv"');
    }

    public function exportPdf()
    {
        $roles = Role::orderBy('name')->get();
        $assigned = RolePermission::get();
        $permissions = Permission::get();
        $html = view('permissoes.export', compact('roles','assigned','permissions'))->render();
        return response($html)->header('Content-Type', 'text/html');
    }
}