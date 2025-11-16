@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-cog mr-2 text-purple-600"></i>
                    Configurações
                </h1>
            </div>
        </header>

        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('configuracoes.account') }}" class="card p-6 block">
                    <h2 class="text-lg font-semibold text-gray-800">Minha Conta</h2>
                    <p class="text-sm text-gray-600 mt-1">Dados pessoais e preferências</p>
                </a>
                <a href="{{ route('configuracoes.password') }}" class="card p-6 block">
                    <h2 class="text-lg font-semibold text-gray-800">Alterar Senha</h2>
                    <p class="text-sm text-gray-600 mt-1">Segurança da conta</p>
                </a>
                @if($isAdmin || $canManageUsers)
                <a href="{{ route('usuarios.index') }}" class="card p-6 block">
                    <h2 class="text-lg font-semibold text-gray-800">Usuários</h2>
                    <p class="text-sm text-gray-600 mt-1">Cadastro e edição de usuários</p>
                </a>
                @endif
                @if($isAdmin || $canManagePerms)
                <a href="{{ route('permissoes.index') }}" class="card p-6 block">
                    <h2 class="text-lg font-semibold text-gray-800">Permissões</h2>
                    <p class="text-sm text-gray-600 mt-1">Gerenciamento de roles e acessos</p>
                </a>
                @endif
            </div>
        </main>
    </div>
@endsection