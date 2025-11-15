@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-history mr-2 text-purple-600"></i>
                    Histórico de Entradas
                </h1>
                
                <div class="flex items-center space-x-4">
                    @auth
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
                        {{-- @if (!Auth::user()->hasVerifiedEmail()) ... --}}
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Sair
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            <div class="mb-6">
                <a href="{{ route('estoque.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar ao Estoque
                </a>
            </div>

            <div class="card max-w-full mx-auto">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $lote->medicamento->nome ?? 'Medicamento Não Encontrado' }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        **Validade do Lote:** {{ date('d/m/Y', strtotime($lote->validade)) }} | **ID Lote:** {{ $lote->id }}
                    </p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-header">Data Entrada</th>
                                <th class="table-header">Fornecedor</th>
                                <th class="table-header">Nº Lote Fornecedor</th>
                                <th class="table-header text-center">Qtd. Informada</th>
                                <th class="table-header text-center">Unidade</th>
                                <th class="table-header">Estado</th>
                                <th class="table-header">Observação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($entradas as $entrada)
                                <tr class="table-row">
                                    <td class="table-cell">{{ date('d/m/Y', strtotime($entrada->data_entrada)) }}</td>
                                    <td class="table-cell">{{ $entrada->fornecedor->nome ?? 'N/A' }}</td>
                                    <td class="table-cell font-mono text-sm">{{ $entrada->numero_lote_fornecedor }}</td>
                                    <td class="table-cell text-center font-semibold text-indigo-700">
                                        {{ number_format($entrada->quantidade_informada, 0, ',', '.') }}
                                    </td>
                                    <td class="table-cell text-center">{{ $entrada->unidade }}</td>
                                    <td class="table-cell">
                                        {{-- Exemplo simples de badge para estado --}}
                                        <span class="status-badge status-success">{{ ucfirst($entrada->estado ?? 'Novo') }}</span>
                                    </td>
                                    <td class="table-cell text-sm text-gray-500">{{ Str::limit($entrada->observacao, 50) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="table-cell text-center py-8">
                                        <div class="text-gray-500">
                                            <i class="fas fa-search-minus text-4xl mb-4 text-gray-300"></i>
                                            <p class="text-lg font-medium">Nenhum registro de entrada encontrado para este lote específico.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection