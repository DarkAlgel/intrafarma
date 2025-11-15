@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Minha Conta</h1>
            </div>
        </header>
        <main class="flex-1 p-6">
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Perfil</h2>
                </div>
                <form method="POST" action="{{ route('configuracoes.account.update') }}" class="px-6 py-6 space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                        <input type="text" name="name" class="form-input" value="{{ Auth::user()->name }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ Auth::user()->email }}">
                    </div>
                    <button class="btn-primary">Salvar</button>
                </form>
            </div>
        </main>
    </div>
@endsection