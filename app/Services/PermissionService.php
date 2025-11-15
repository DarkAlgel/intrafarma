<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PermissionService
{
    public static function userHas(int $userId, string $code): bool
    {
        $direct = DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('user_permissions.user_id', $userId)
            ->where('permissions.code', $code)
            ->exists();

        if ($direct) return true;

        $viaRole = DB::table('user_roles')
            ->join('role_permissions', 'role_permissions.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.code', $code)
            ->exists();

        return $viaRole;
    }
}