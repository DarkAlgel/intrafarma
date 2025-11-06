@extends('layouts.app')

@section('content')
<div class="auth-container flex items-center justify-center px-4">
    <div class="auth-card w-full max-w-md">
        <div class="auth-header text-white text-center p-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-user-plus text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Criar Conta</h1>
            </div>
            <p class="text-purple-100 text-sm">Cadastre-se no IntraFarma</p>
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
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="form-label">
                        <i class="fas fa-user mr-2 text-purple-600"></i>
                        Nome Completo
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus
                           class="form-input"
                           placeholder="Seu nome completo">
                </div>
                
                <div>
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>
                        E-mail
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required
                           class="form-input"
                           placeholder="seu@email.com">
                </div>
                
                <div>
                    <label for="password" class="form-label">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>
                        Senha
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="form-input"
                           placeholder="••••••••">
                </div>
                
                <div>
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>
                        Confirmar Senha
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           class="form-input"
                           placeholder="Confirme sua senha">
                </div>
                
                <div class="space-y-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus mr-2"></i>
                        Criar Conta
                    </button>
                </div>
                
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Já tem uma conta? 
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