@extends('layouts.app')

@section('content')
<div class="auth-container flex items-center justify-center px-4">
    <div class="auth-card w-full max-w-md">
        <div class="auth-header text-white text-center p-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-key text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Recuperar Senha</h1>
            </div>
            <p class="text-purple-100 text-sm">Digite seu e-mail para receber o link de recuperação</p>
        </div>
        
        <div class="p-8">
            
            @if (session('status'))
                <div class="alert-success mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p class="text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>
                        Endereço de Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           class="form-input @error('email') border-red-500 @enderror"
                           placeholder="seu@email.com">
                           
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div class="space-y-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Enviar Link de Recuperação
                    </button>
                </div>
                
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Lembrou a senha? 
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