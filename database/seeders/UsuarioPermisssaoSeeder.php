<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class UsuarioPermisssaoSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('usuarios') || !Schema::hasTable('papeis') || !Schema::hasTable('permissoes')) {
            return;
        }

        $papeis = ['Administradores', 'Funcionários', 'Pacientes'];
        foreach ($papeis as $nome) {
            $existe = DB::table('papeis')->where('nome', $nome)->exists();
            if (!$existe) {
                DB::table('papeis')->insert(['nome' => $nome]);
            }
        }

        $permissoes = [
            ['codigo' => 'gerenciar_usuarios', 'nome' => 'Gerenciar Usuários'],
            ['codigo' => 'gerenciar_permissoes', 'nome' => 'Gerenciar Permissões'],
            ['codigo' => 'ver_minha_conta', 'nome' => 'Ver Minha Conta'],
            ['codigo' => 'alterar_senha', 'nome' => 'Alterar Senha'],
            ['codigo' => 'ver_estoque', 'nome' => 'Ver Estoque'],
            ['codigo' => 'ver_dispensacoes', 'nome' => 'Ver Dispensações'],
        ];
        foreach ($permissoes as $p) {
            DB::table('permissoes')->updateOrInsert(['codigo' => $p['codigo']], ['nome' => $p['nome']]);
        }

        $papeisMap = DB::table('papeis')->pluck('id', 'nome');
        $permsMap = DB::table('permissoes')->pluck('id', 'codigo');

        $todosPerms = array_values($permsMap->toArray());
        foreach ($todosPerms as $pid) {
            $ex = DB::table('papeis_permissoes')->where('papel_id', $papeisMap['Administradores'])->where('permissao_id', $pid)->exists();
            if (!$ex) DB::table('papeis_permissoes')->insert(['papel_id' => $papeisMap['Administradores'], 'permissao_id' => $pid]);
        }

        foreach (['ver_estoque', 'ver_dispensacoes', 'ver_minha_conta', 'alterar_senha'] as $codigo) {
            $pid = $permsMap[$codigo] ?? null;
            if ($pid) {
                $ex = DB::table('papeis_permissoes')->where('papel_id', $papeisMap['Funcionários'])->where('permissao_id', $pid)->exists();
                if (!$ex) DB::table('papeis_permissoes')->insert(['papel_id' => $papeisMap['Funcionários'], 'permissao_id' => $pid]);
            }
        }

        foreach (['ver_minha_conta', 'alterar_senha'] as $codigo) {
            $pid = $permsMap[$codigo] ?? null;
            if ($pid) {
                $ex = DB::table('papeis_permissoes')->where('papel_id', $papeisMap['Pacientes'])->where('permissao_id', $pid)->exists();
                if (!$ex) DB::table('papeis_permissoes')->insert(['papel_id' => $papeisMap['Pacientes'], 'permissao_id' => $pid]);
            }
        }

        $usuariosSeed = [
            ['nome' => 'Administrador', 'email' => 'admin@intrafarma.com', 'login' => 'admin', 'senha' => 'admin123', 'papel' => 'Administradores'],
            ['nome' => 'Funcionário Demo', 'email' => 'staff@intrafarma.com', 'login' => 'staff', 'senha' => 'staff123', 'papel' => 'Funcionários'],
            ['nome' => 'Paciente Demo', 'email' => 'paciente@intrafarma.com', 'login' => 'paciente', 'senha' => 'paciente123', 'papel' => 'Pacientes'],
            ['nome' => 'Operador Estoque', 'email' => 'operador@intrafarma.com', 'login' => 'operador', 'senha' => 'operador123', 'papel' => 'Funcionários'],
        ];

        foreach ($usuariosSeed as $u) {
            $usuario = DB::table('usuarios')->where('email', $u['email'])->first();
            if (!$usuario) {
                $uid = DB::table('usuarios')->insertGetId([
                    'nome' => $u['nome'],
                    'celular' => null,
                    'email' => $u['email'],
                    'login' => $u['login'],
                    'senha_hash' => Hash::make($u['senha']),
                    'datacadastro' => DB::raw('now()'),
                    'ultimoacesso' => null,
                    'ativo' => true,
                ]);
            } else {
                $uid = $usuario->id;
            }

            $papelId = $papeisMap[$u['papel']] ?? null;
            if ($papelId) {
                $ex = DB::table('usuarios_papeis')->where('usuario_id', $uid)->where('papel_id', $papelId)->exists();
                if (!$ex) DB::table('usuarios_papeis')->insert(['usuario_id' => $uid, 'papel_id' => $papelId]);
            }

            if ($u['email'] === 'operador@intrafarma.com') {
                $pid = $permsMap['ver_estoque'] ?? null;
                if ($pid) DB::table('usuarios_permissoes')->updateOrInsert(['usuario_id' => $uid, 'permissao_id' => $pid]);
            }
        }
    }
}