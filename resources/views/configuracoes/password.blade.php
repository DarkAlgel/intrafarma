@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Alterar Senha</h1>
            </div>
        </header>
        <main class="flex-1 p-6">
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Segurança</h2>
                </div>
                <form method="POST" action="{{ route('configuracoes.password.update') }}" class="px-6 py-6 space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                        <input type="password" name="current_password" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                        <input type="password" name="password" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" class="form-input">
                    </div>
                    <button class="btn-primary">Alterar</button>
                </form>
            </div>
        </main>
    </div>
@endsection