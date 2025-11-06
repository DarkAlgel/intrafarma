@extends('layouts.app')

@section('content')
<div class="auth-container flex items-center justify-center px-4">
    <div class="auth-card w-full max-w-md">
        <div class="auth-header text-white text-center p-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-key text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Redefinir Senha</h1>
            </div>
            <p class="text-purple-100 text-sm">Crie sua nova senha</p>
        </div>
        
        <div class="p-8">
            
            @if ($errors->any())
                <div class="alert-error mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div>
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>
                        Endereço de Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ $email ?? old('email') }}" 
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
                
                <div>
                    <label for="password" class="form-label">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>
                        Nova Senha
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required
                           class="form-input @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                           
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label for="password-confirm" class="form-label">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>
                        Confirmar Nova Senha
                    </label>
                    <input id="password-confirm" 
                           type="password" 
                           name="password_confirmation" 
                           required
                           class="form-input"
                           placeholder="Confirme sua nova senha">
                </div>
                
                <div class="space-y-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check mr-2"></i>
                        Redefinir Senha
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