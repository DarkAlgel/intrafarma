<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use App\Models\Usuario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $canManageUsers = PermissionService::userHas($user->id, 'gerenciar_usuarios');
        $canManagePerms = PermissionService::userHas($user->id, 'gerenciar_permissoes');
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
            'email' => ['required', 'email', 'unique:usuarios,email,'.$user->id],
        ]);
        DB::table('usuarios')->where('id', $user->id)->update([
            'nome' => $data['name'],
            'email' => $data['email'],
        ]);
        return redirect()->route('configuracoes.account');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ]);
        if (!Hash::check($data['current_password'], $user->senha_hash)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta']);
        }
        DB::table('usuarios')->where('id', $user->id)->update(['senha_hash' => Hash::make($data['password'])]);
        return redirect()->route('configuracoes.password');
    }
}