<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $code)
    {
        $user = Auth::user();
        if (!$user || !PermissionService::userHas($user->id, $code)) {
            abort(403);
        }
        return $next($request);
    }
}