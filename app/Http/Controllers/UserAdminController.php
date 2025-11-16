<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = DB::table('usuarios')->select('id', DB::raw('nome as name'), 'email')->orderBy('nome')->get();
        $roles = DB::table('papeis')->select('id', DB::raw('nome as name'))->orderBy('nome')->get();
        $permissions = DB::table('permissoes')->select('id', DB::raw('nome as name'), 'codigo')->orderBy('nome')->get();
        $permissionsById = $permissions->keyBy('id');
        $permissionDescriptions = [];
        foreach ($permissions as $p) {
            $permissionDescriptions[$p->id] = match($p->codigo) {
                'gerenciar_usuarios' => 'Gerencia cadastro, edição e exclusão de usuários.',
                'gerenciar_permissoes' => 'Gerencia concessão e revogação de permissões.',
                'ver_minha_conta' => 'Acessa e edita dados da própria conta.',
                'alterar_senha' => 'Altera a própria senha com segurança.',
                'ver_estoque' => 'Visualiza informações de estoque.',
                'ver_dispensacoes' => 'Visualiza registro de dispensações.',
                default => 'Acesso relacionado à funcionalidade do sistema.'
            };
        }
        $userRoles = DB::table('usuarios_papeis')->pluck('papel_id', 'usuario_id');
        $userPerms = DB::table('usuarios_permissoes')->select('usuario_id', DB::raw('permissao_id as permission_id'))->get()->groupBy('usuario_id');
        $rolePerms = DB::table('papeis_permissoes')->select('papel_id', DB::raw('permissao_id as permission_id'))->get()->groupBy('papel_id');
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
            $userNames = collect($userPids)->map(fn($id) => ($permissionsById[$id]->name ?? null))->filter()->values()->all();
            $effectivePermsByUser[$u->id] = collect(array_merge($roleNames, $userNames))->unique()->values()->all();
        }
        return view('usuarios.index', compact('users', 'roles', 'permissions', 'userRoles', 'userPerms', 'rolePermNames', 'effectivePermsByUser', 'permissionDescriptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'integer'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
            'initial_permission_id' => ['nullable', 'integer'],
        ]);
        $userId = DB::table('usuarios')->insertGetId([
            'nome' => $data['name'],
            'email' => $data['email'],
            'login' => strtolower(str_replace(' ', '.', $data['name'])),
            'senha_hash' => Hash::make($data['password']),
            'ativo' => true,
            'datacadastro' => DB::raw('now()'),
        ]);
        if (!empty($data['role_id'])) {
            DB::table('usuarios_papeis')->insert(['usuario_id' => $userId, 'papel_id' => $data['role_id']]);
        }
        if (!empty($data['permission_ids'])) {
            foreach ($data['permission_ids'] as $pid) {
                DB::table('usuarios_permissoes')->updateOrInsert(['usuario_id' => $userId, 'permissao_id' => $pid]);
            }
        }
        if (!empty($data['initial_permission_id'])) {
            DB::table('usuarios_permissoes')->updateOrInsert(['usuario_id' => $userId, 'permissao_id' => $data['initial_permission_id']]);
        }
        return redirect()->route('usuarios.index');
    }

    public function update(Request $request, int $id)
    {
        $user = DB::table('usuarios')->where('id', $id)->first();
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:usuarios,email,'.$id],
            'role_id' => ['nullable', 'integer'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
            'set_permission_id' => ['nullable', 'integer'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        DB::table('usuarios')->where('id', $id)->update(['nome' => $data['name'], 'email' => $data['email']]);
        if (!empty($data['password'])) {
            DB::table('usuarios')->where('id', $id)->update(['senha_hash' => Hash::make($data['password'])]);
        }
        if (!empty($data['role_id'])) {
            DB::table('usuarios_papeis')->where('usuario_id', $id)->delete();
            DB::table('usuarios_papeis')->insert(['usuario_id' => $id, 'papel_id' => $data['role_id']]);
        }
        if (array_key_exists('permission_ids', $data)) {
            DB::table('usuarios_permissoes')->where('usuario_id', $id)->delete();
            foreach ($data['permission_ids'] ?? [] as $pid) {
                DB::table('usuarios_permissoes')->updateOrInsert(['usuario_id' => $id, 'permissao_id' => $pid]);
            }
        }
        if (!empty($data['set_permission_id'])) {
            DB::table('usuarios_permissoes')->where('usuario_id', $id)->delete();
            DB::table('usuarios_permissoes')->updateOrInsert(['usuario_id' => $id, 'permissao_id' => $data['set_permission_id']]);
        }
        return redirect()->route('usuarios.index');
    }

    public function destroy(int $id)
    {
        DB::table('usuarios_papeis')->where('usuario_id', $id)->delete();
        DB::table('usuarios_permissoes')->where('usuario_id', $id)->delete();
        DB::table('usuarios')->where('id', $id)->delete();
        return redirect()->route('usuarios.index');
    }
}