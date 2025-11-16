<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionAdminController extends Controller
{
    public function index(Request $request)
    {
        $sortParam = $request->query('sort');
        $sort = in_array($sortParam, ['name','nome']) ? ($sortParam === 'name' ? 'nome' : 'nome') : 'nome';
        $dir = in_array($request->query('dir'), ['asc','desc']) ? $request->query('dir') : 'asc';
        $roles = DB::table('papeis')->select('id', DB::raw('nome as name'), DB::raw('NULL::text as code'))
            ->orderBy($sort, $dir)->paginate(10);
        $permissions = DB::table('permissoes')->select('id', DB::raw('nome as name'), 'codigo')->orderBy('nome')->get();
        $assigned = DB::table('papeis_permissoes')->select(DB::raw('papel_id as role_id'), DB::raw('permissao_id as permission_id'))->get();
        $defaultCodes = [];
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
        return view('permissoes.index', compact('roles', 'permissions', 'assigned', 'defaultCodes', 'permissionDescriptions', 'sort', 'dir'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
            'permission_id' => ['required', 'integer'],
        ]);
        DB::table('papeis_permissoes')->updateOrInsert(['papel_id' => $data['role_id'], 'permissao_id' => $data['permission_id']]);
        return redirect()->route('permissoes.index');
    }

    public function revoke(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
            'permission_id' => ['required', 'integer'],
        ]);
        DB::table('papeis_permissoes')->where(['papel_id' => $data['role_id'], 'permissao_id' => $data['permission_id']])->delete();
        return redirect()->route('permissoes.index');
    }

    public function createRole(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
        ]);
        $roleId = DB::table('papeis')->insertGetId(['nome' => $data['name']]);
        foreach (($data['permission_ids'] ?? []) as $pid) {
            DB::table('papeis_permissoes')->updateOrInsert(['papel_id' => $roleId, 'permissao_id' => $pid]);
        }
        return redirect()->route('permissoes.index');
    }

    public function updateRole(Request $request, int $id)
    {
        $role = DB::table('papeis')->where('id', $id)->first();
        $data = $request->validate([
            'name' => ['required', 'string'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
        ]);
        DB::table('papeis')->where('id', $id)->update(['nome' => $data['name']]);
        DB::table('papeis_permissoes')->where('papel_id', $id)->delete();
        foreach (($data['permission_ids'] ?? []) as $pid) {
            DB::table('papeis_permissoes')->updateOrInsert(['papel_id' => $id, 'permissao_id' => $pid]);
        }
        return redirect()->route('permissoes.index');
    }

    public function deleteRole(Request $request, int $id)
    {
        DB::table('papeis_permissoes')->where('papel_id', $id)->delete();
        DB::table('papeis')->where('id', $id)->delete();
        return redirect()->route('permissoes.index');
    }

    public function exportCsv()
    {
        $rows = DB::table('papeis')
            ->leftJoin('papeis_permissoes', 'papeis.id', '=', 'papeis_permissoes.papel_id')
            ->leftJoin('permissoes', 'permissoes.id', '=', 'papeis_permissoes.permissao_id')
            ->select('papeis.nome as role', 'permissoes.nome as permission')
            ->orderBy('papeis.nome')->get();
        $csv = "role,permission\n";
        foreach ($rows as $r) {
            $csv .= sprintf("%s,%s\n", $r->role, $r->permission ?? '');
        }
        return response($csv)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="roles_permissions.csv"');
    }

    public function exportPdf()
    {
        $roles = DB::table('papeis')->select('id', DB::raw('nome as name'))->orderBy('nome')->get();
        $assigned = DB::table('papeis_permissoes')->select(DB::raw('papel_id as role_id'), DB::raw('permissao_id as permission_id'))->get();
        $permissions = DB::table('permissoes')->select('id', DB::raw('nome as name'))->get();
        $html = view('permissoes.export', compact('roles','assigned','permissions'))->render();
        return response($html)->header('Content-Type', 'text/html');
    }
}