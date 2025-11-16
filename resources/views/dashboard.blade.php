@extends('layouts.app')

@section('content')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-purple-600"></i> Dashboard
            </h1>
            
            <div class="flex items-center space-x-4">
                @auth
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- CARD 1: MEDICAMENTOS TOTAIS --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Medicamentos</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalMedicamentos }}</p>
                        <p class="text-sm text-gray-500">Total cadastrados (Ativos)</p>
                    </div>
                    <div class="text-blue-500 text-4xl">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
            
            {{-- CARD 2: DISPENSAÇÕES HOJE --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Dispensações</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $dispensacoesHoje }}</p>
                        <p class="text-sm text-gray-500">Realizadas hoje</p>
                    </div>
                    <div class="text-green-500 text-4xl">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            
            {{-- CARD 3: ALERTAS DE ESTOQUE BAIXO --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Alertas</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $alertasBaixo }}</p>
                        <p class="text-sm text-gray-500">Lotes com estoque baixo</p>
                    </div>
                    <div class="text-yellow-500 text-4xl">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Medicamentos Críticos (Próximos 30 dias / Vencidos)</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header">Medicamento</th>
                            <th class="table-header">Código</th>
                            <th class="table-header">Lote Fornecedor</th>
                            <th class="table-header">Validade</th>
                            <th class="table-header">Qtd. Disponível</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($proximosAVencer as $item)
                        <tr>
                            <td class="table-cell">{{ $item->medicamento }}</td>
                            <td class="table-cell">{{ $item->codigo }}</td>
                            <td class="table-cell">{{ $item->numero_lote_fornecedor ?? 'N/A' }}</td> 
                            <td class="table-cell font-semibold text-yellow-600">{{ date('d/m/Y', strtotime($item->validade)) }}</td>
                            <td class="table-cell">{{ number_format($item->quantidade_disponivel, 2, ',', '.') }} {{ $item->unidade_base }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center py-4 text-gray-500">
                                <i class="fas fa-check-circle mr-2 text-green-500"></i> Nenhum medicamento crítico ou próximo de vencer.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection