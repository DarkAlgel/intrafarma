@extends('layouts.app')

@section('content')
<div class="auth-container flex items-center justify-center px-4">
    <div class="auth-card w-full max-w-md">
        <div class="auth-header text-white text-center p-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-envelope-open-text text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Verificar Email</h1>
            </div>
            <p class="text-purple-100 text-sm">Confirmação de email necessária</p>
        </div>
        
        <div class="p-8">
            
            @if (session('resent'))
                <div class="alert-success mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p class="text-sm">Um novo link de verificação foi enviado para seu email.</p>
                    </div>
                </div>
            @endif
            
            <div class="text-center mb-8">
                <div class="text-6xl text-purple-600 mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <p class="text-gray-700 mb-4">
                    Antes de continuar, por favor verifique seu email para o link de verificação.
                </p>
                <p class="text-sm text-gray-600">
                    Se você não recebeu o email, clique no botão abaixo para solicitar outro.
                </p>
            </div>
            
            <form method="POST" action="{{ route('verification.resend') }}" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Reenviar Email de Verificação
                    </button>
                </div>
                
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Já verificou seu email? 
                        <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-medium transition-colors">
                            Faça login
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection