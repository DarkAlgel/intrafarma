@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="bg-gradient-to-r from-indigo-700 via-violet-700 to-indigo-900 text-white text-center px-8 py-7">
            <div class="flex items-center justify-center mb-3">
                <i class="fas fa-pills text-3xl mr-3"></i>
                <h1 class="text-2xl font-semibold tracking-tight">IntraFarma</h1>
            </div>
            <p class="text-indigo-100 text-xs uppercase tracking-[0.2em]">
                Sistema de Gestão de Farmácia
            </p>
        </div>

        <div class="px-8 py-7">

            {{-- Erros --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Mensagem de sucesso / status --}}
            @if (session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p>{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- E-mail --}}
                <div class="space-y-1">
                    <label for="email" class="flex items-center text-sm font-medium text-slate-800">
                        <i class="fas fa-envelope mr-2 text-indigo-600"></i>
                        E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="seu@email.com"
                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm text-slate-900
                               shadow-sm outline-none transition
                               focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                    >
                </div>

                {{-- Senha --}}
                <div class="space-y-1">
                    <label for="password" class="flex items-center text-sm font-medium text-slate-800">
                        <i class="fas fa-lock mr-2 text-indigo-600"></i>
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm text-slate-900
                               shadow-sm outline-none transition
                               focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                    >
                </div>

                {{-- Lembrar-me --}}
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-xs text-slate-600">
                            Lembrar-me
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline"
                        >
                            Esqueceu sua senha?
                        </a>
                    @endif
                </div>

                {{-- Botão --}}
                <div class="space-y-3 pt-1">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-700 px-4 py-2.5
                               text-sm font-semibold text-white shadow-md shadow-indigo-300/40
                               transition hover:bg-indigo-800 focus:outline-none focus:ring-2
                               focus:ring-indigo-500 focus:ring-offset-1 focus:ring-offset-slate-100"
                    >
                        <i class="fas fa-sign-in-alt mr-2 text-sm"></i>
                        Entrar
                    </button>
                </div>

                {{-- Separador / registro --}}
                <div class="pt-5 border-t border-slate-200">
                    <p class="text-center text-xs text-slate-600">
                        Não tem uma conta?
                        <a
                            href="{{ route('register') }}"
                            class="font-semibold text-indigo-700 hover:text-indigo-900 hover:underline"
                        >
                            Registre-se
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection