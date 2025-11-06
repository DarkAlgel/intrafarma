@extends('layouts.app')

@section('content')
<div class="auth-container flex items-center justify-center px-4">
    <div class="auth-card w-full max-w-md">
        <div class="auth-header text-white text-center p-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-pills text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">IntraFarma</h1>
            </div>
            <p class="text-purple-100 text-sm">Sistema de Gestão de Farmácia</p>
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
            
            @if (session('status'))
                <div class="alert-success mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p class="text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
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
                           autofocus
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
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="remember" 
                           name="remember"
                           class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Lembrar-me
                    </label>
                </div>
                
                <div class="space-y-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar
                    </button>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="block text-center text-sm text-purple-600 hover:text-purple-800 transition-colors">
                            <i class="fas fa-key mr-1"></i>
                            Esqueceu sua senha?
                        </a>
                    @endif
                </div>
                
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Não tem uma conta? 
                        <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-800 font-medium transition-colors">
                            Registre-se
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection