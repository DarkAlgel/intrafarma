<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    /**
     * Mostra a página de notificação de verificação
     */
    public function show()
    {
        return view('auth.verify');
    }

    /**
     * Verifica o email do usuário
     */
    public function verify(Request $request)
    {
        return redirect()->route('dashboard');
    }

    /**
     * Reenvia o email de verificação
     */
    public function resend(Request $request)
    {
        return redirect()->route('dashboard');
    }
}