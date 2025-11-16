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
        $isAdmin = DB::table('usuarios_papeis')
            ->join('papeis', 'papeis.id', '=', 'usuarios_papeis.papel_id')
            ->join('usuarios', 'usuarios.id', '=', 'usuarios_papeis.usuario_id')
            ->where('usuarios_papeis.usuario_id', $userId)
            ->where('usuarios.ativo', true)
            ->where('papeis.nome', 'Administradores')
            ->exists();
        if ($isAdmin) {
            return true;
        }
        try {
            $row = DB::selectOne('SELECT fn_usuario_tem_permissao(?, ?) AS ok', [$userId, $code]);
            if ($row && isset($row->ok)) {
                return (bool) $row->ok;
            }
        } catch (\Throwable $e) {
            // fallback para joins caso função não exista
        }

        $direct = DB::table('usuarios_permissoes')
            ->join('permissoes', 'permissoes.id', '=', 'usuarios_permissoes.permissao_id')
            ->where('usuarios_permissoes.usuario_id', $userId)
            ->where('permissoes.codigo', $code)
            ->exists();
        if ($direct) return true;

        $viaRole = DB::table('usuarios_papeis')
            ->join('papeis_permissoes', 'papeis_permissoes.papel_id', '=', 'usuarios_papeis.papel_id')
            ->join('permissoes', 'permissoes.id', '=', 'papeis_permissoes.permissao_id')
            ->where('usuarios_papeis.usuario_id', $userId)
            ->where('permissoes.codigo', $code)
            ->exists();
        return $viaRole;
    }

    public static function userIsAdmin(int $userId): bool
    {
        return DB::table('usuarios_papeis')
            ->join('papeis', 'papeis.id', '=', 'usuarios_papeis.papel_id')
            ->join('usuarios', 'usuarios.id', '=', 'usuarios_papeis.usuario_id')
            ->where('usuarios_papeis.usuario_id', $userId)
            ->where('usuarios.ativo', true)
            ->where('papeis.nome', 'Administradores')
            ->exists();
    }
}