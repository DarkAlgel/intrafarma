@extends('layouts.app')

@section('content')
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-user-cog mr-2 text-purple-600"></i> Configurações
        </h1>
    </div>
</header>

<main class="flex-1 p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border">
            <h2 class="text-lg font-semibold mb-4">Minha Conta</h2>
            <a href="{{ route('configuracoes.account') }}" class="btn-primary"><i class="fas fa-id-card mr-2"></i>Editar Dados</a>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border">
            <h2 class="text-lg font-semibold mb-4">Senha</h2>
            <a href="{{ route('configuracoes.password') }}" class="btn-secondary"><i class="fas fa-key mr-2"></i>Alterar Senha</a>
        </div>
    </div>
</main>
@endsection