<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('papeis') || !Schema::hasTable('permissoes')) {
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

        if (Schema::hasTable('usuarios') && Schema::hasTable('usuarios_papeis')) {
            $adminUsuario = DB::table('usuarios')->where('email', 'admin@intrafarma.com')->first();
            if ($adminUsuario) {
                $ex = DB::table('usuarios_papeis')->where('usuario_id', $adminUsuario->id)->where('papel_id', $papeisMap['Administradores'])->exists();
                if (!$ex) DB::table('usuarios_papeis')->insert(['usuario_id' => $adminUsuario->id, 'papel_id' => $papeisMap['Administradores']]);
            }
        }
    }
}