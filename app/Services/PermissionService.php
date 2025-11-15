<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Verifica se o usuário possui a permissão especificada, seja diretamente ou via papel (role).
     *
     * @param int $userId O ID do usuário.
     * @param string $code O código da permissão (ex: 'manage_users').
     * @return bool
     */
    public static function userHas(int $userId, string $code): bool
    {
        // 1. Verificação de Permissão Direta (Tabela usuarios_permissoes)
        $direct = DB::table('usuarios_permissoes') // ⭐️ CORRIGIDO: user_permissions -> usuarios_permissoes
            
            // Join: usuarios_permissoes -> permissoes
            ->join('permissoes', 'permissoes.id', '=', 'usuarios_permissoes.permissao_id') 
            
            // Where: Verifica se o usuário e o código de permissão existem
            ->where('usuarios_permissoes.usuario_id', $userId) // Usando a coluna 'usuario_id'
            ->where('permissoes.codigo', $code) // Usando a coluna 'codigo' da tabela permissoes
            ->exists();

        if ($direct) return true;

        // 2. Verificação de Permissão Via Papel (Tabelas usuarios_papeis e papeis_permissoes)
        $viaRole = DB::table('usuarios_papeis') // ⭐️ CORRIGIDO: user_roles -> usuarios_papeis
            
            // Join 1: usuarios_papeis -> papeis_permissoes
            ->join('papeis_permissoes', 'papeis_permissoes.papel_id', '=', 'usuarios_papeis.papel_id') // ⭐️ CORRIGIDO: role_permissions -> papeis_permissoes
            
            // Join 2: papeis_permissoes -> permissoes
            ->join('permissoes', 'permissoes.id', '=', 'papeis_permissoes.permissao_id') // Usando a coluna 'permissao_id'
            
            // Where: Verifica se o usuário e o código de permissão existem
            ->where('usuarios_papeis.usuario_id', $userId)
            ->where('permissoes.codigo', $code)
            ->exists();

        return $viaRole;
    }
}