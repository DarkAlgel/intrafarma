@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-truck mr-2 text-purple-600"></i>
                    Fornecedores
                </h1>
                <div class="flex items-center space-x-4">
                    @auth
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
                        @if(!Auth::user()->hasVerifiedEmail())
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Email não verificado</span>
                        @else
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Email verificado</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Lista de Fornecedores</h2>
                    <p class="text-sm text-gray-600 mt-1">Gerencie os fornecedores cadastrados</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-header">
                                    <a href="{{ route('fornecedores.index', ['sort' => 'nome', 'direction' => $sort === 'nome' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>Nome
                                        @if($sort === 'nome')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="table-header">
                                    <a href="{{ route('fornecedores.index', ['sort' => 'tipo', 'direction' => $sort === 'tipo' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        <i class="fas fa-tags mr-2"></i>Tipo
                                        @if($sort === 'tipo')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="table-header">
                                    <a href="{{ route('fornecedores.index', ['sort' => 'contato', 'direction' => $sort === 'contato' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        <i class="fas fa-phone mr-2"></i>Contato
                                        @if($sort === 'contato')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($fornecedores as $fornecedor)
                            <tr class="table-row">
                                <td class="table-cell">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-truck text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $fornecedor->nome }}</span>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <span class="status-badge {{ $fornecedor->tipo === 'doacao' ? 'status-success' : 'status-warning' }}">
                                        {{ ucfirst($fornecedor->tipo) }}
                                    </span>
                                </td>
                                <td class="table-cell">{{ $fornecedor->contato }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="table-cell text-center py-8">
                                    <div class="text-gray-500">
                                        <i class="fas fa-truck text-4xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Nenhum fornecedor cadastrado</p>
                                        <p class="text-sm mt-1">Comece adicionando seu primeiro fornecedor</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fornecedores->count() > 0)
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Mostrando <span class="font-medium">{{ $fornecedores->count() }}</span> fornecedor(es)
                        </p>
                        <div>
                            {{ $fornecedores->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection