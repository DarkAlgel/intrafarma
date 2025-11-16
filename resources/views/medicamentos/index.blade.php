@extends('layouts.app')

@section('content')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-pills mr-2 text-purple-600"></i>
                Gestão de Medicamentos
            </h1>
            
            {{-- BARRA DE STATUS/LOGOUT --}}
            <div class="flex items-center space-x-4">
                @auth
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
                    @if(Auth::user()->email_verified_at)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Email verificado</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Email não verificado</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sair
                    </button>
                </form>
                @endauth
            </div>
            {{-- FIM BARRA DE STATUS/LOGOUT --}}
        </div>
    </header>

    <main class="flex-1 p-6">
        
        {{-- Botão 'Novo Medicamento' mantido no conteúdo principal --}}
        <div class="mb-6 flex justify-between items-center">
            
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Lista de Medicamentos</h2>
                <p class="text-sm text-gray-600 mt-1">Visão geral dos medicamentos cadastrados.</p>
            </div>
            
            <a href="{{ route('medicamentos.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i> Novo Medicamento
            </a>
        </div>
        
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">{{ session('error') }}</div>
        @endif

        <div class="card">
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header">Código</th>
                            <th class="table-header">Nome/Dosagem</th>
                            <th class="table-header">Laboratório</th>
                            <th class="table-header">Classe</th>
                            <th class="table-header text-center">Tarja</th>
                            <th class="table-header text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($medicamentos as $medicamento)
                        <tr class="table-row">
                            <td class="table-cell font-mono text-sm">{{ $medicamento->codigo }}</td>
                            <td class="table-cell font-medium text-gray-900">
                                {{ $medicamento->nome }}<br>
                                <span class="text-xs text-gray-500">
                                    {{ $medicamento->dosagem_valor }} {{ $medicamento->dosagem_unidade }} - {{ ucfirst($medicamento->apresentacao) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $medicamento->laboratorio->nome ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $medicamento->classeTerapeutica->nome ?? 'N/A' }}</td>
                            <td class="table-cell text-center">
                                <span class="status-badge 
                                    @if($medicamento->tarja === 'tarja_preta') status-danger 
                                    @elseif($medicamento->tarja === 'tarja_vermelha') status-warning 
                                    @else status-success @endif">
                                    {{ ucfirst(str_replace('_', ' ', $medicamento->tarja)) }}
                                </span>
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('medicamentos.edit', $medicamento->id) }}" title="Editar" class="text-blue-600 hover:text-blue-800 transition duration-200">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Implementar botão de exclusão com SweetAlert se necessário --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center py-8">
                                <div class="text-gray-500">
                                    <i class="fas fa-pills text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Nenhum medicamento cadastrado.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $medicamentos->links() }}
            </div>
        </div>
    </main>
@endsection