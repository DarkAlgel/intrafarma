<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $canManageUsers = PermissionService::userHas($user->id, 'manage_users');
        $canManagePerms = PermissionService::userHas($user->id, 'manage_permissions');
        return view('configuracoes.index', compact('canManageUsers', 'canManagePerms'));
    }

    public function account()
    {
        return view('configuracoes.account');
    }

    public function password()
    {
        return view('configuracoes.password');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
        ]);
        $user->update($data);
        return redirect()->route('configuracoes.account');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ]);
        if (!\Illuminate\Support\Facades\Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta']);
        }
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($data['password'])]);
        return redirect()->route('configuracoes.password');
    }
}