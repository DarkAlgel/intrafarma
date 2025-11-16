<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Mostra o formulário de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Processa o registro de um novo usuário
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $usuario = new Usuario([
            'nome' => $request->name,
            'email' => $request->email,
            'login' => strtolower(str_replace(' ', '.', $request->name)),
            'senha_hash' => Hash::make($request->password),
            'ativo' => true,
        ]);
        $usuario->save();

        Auth::login($usuario);

        return redirect()->route('dashboard');
    }
}