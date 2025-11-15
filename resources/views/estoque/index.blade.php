@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="sidebar w-64">
        <div class="p-4 border-b border-purple-700">
            <h1 class="text-xl font-bold flex items-center text-white">
                <i class="fas fa-pills mr-2"></i>
                INTRAFARMA
            </h1>
        </div>
        
        <nav class="mt-6">
            
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-pills mr-3"></i>
                Medicamentos
            </a>
            {{-- Link Ativo para Estoque --}}
            <a href="{{ route('estoque.index') }}" 
               class="nav-link {{ request()->routeIs('estoque.*') ? 'active' : '' }}">
                <i class="fas fa-boxes mr-3"></i>
                Estoque
            </a>
            <a href="{{ route('pacientes.index') }}" class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                <i class="fas fa-users mr-3"></i>
                Pacientes
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-clipboard-list mr-3"></i>
                Dispensações
            </a>
            <a href="{{ route('fornecedores.index') }}" class="nav-link">
                <i class="fas fa-truck mr-3"></i>
                Fornecedores
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-cog mr-3"></i>
                Configurações
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col md:ml-64">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-boxes mr-2 text-purple-600"></i>
                    Controle de Estoque por Lote
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
                <a href="{{ route('entradas.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Entrada de Lote
                </a>
            </div>

            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Lotes no Inventário</h2>
                    <p class="text-sm text-gray-600 mt-1">Status, validade e saldo de cada lote de medicamento.</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-header"><i class="fas fa-pills mr-2"></i>Medicamento (Lote F.)</th>
                                <th class="table-header"><i class="fas fa-clock mr-2"></i>Validade</th>
                                <th class="table-header text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Dias Vencimento</th>
                                <th class="table-header text-center"><i class="fas fa-sort-numeric-up-alt mr-2"></i>Qtd. Disponível</th>
                                <th class="table-header text-center"><i class="fas fa-info-circle mr-2"></i>Status</th>
                                <th class="table-header text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            
                            @forelse ($estoques as $item)
                                <tr class="table-row">
                                    <td class="table-cell font-medium text-gray-900">
                                        {{ $item->medicamento }} <br> 
                                        <span class="text-xs text-gray-500">Cód: {{ $item->codigo }} | Lote F.: {{ $item->numero_lote_fornecedor ?? 'N/A' }}</span>
                                    </td>
                                    
                                    <td class="table-cell font-semibold">
                                        {{ date('d/m/Y', strtotime($item->validade)) }}
                                    </td>
                                    
                                    <td class="table-cell text-center">
                                        @if ($item->dias_para_vencimento <= 0)
                                            <span class="text-red-600 font-extrabold">VENCIDO</span>
                                        @else
                                            <span class="text-gray-700 font-medium">{{ $item->dias_para_vencimento }} dias</span>
                                        @endif
                                    </td>
                                    
                                    <td class="table-cell text-center text-lg font-bold text-indigo-700">
                                        {{ number_format($item->quantidade_disponivel, 2, ',', '.') }} 
                                        <span class="text-base font-normal text-gray-600">{{ $item->unidade_base }}</span>
                                    </td>
                                    
                                    <td class="table-cell text-center">
                                        @php
                                            $statusClass = '';
                                            if ($item->dias_para_vencimento <= 0) {
                                                $statusClass = 'status-danger';
                                            } elseif ($item->status === 'Próximo de vencer') {
                                                $statusClass = 'status-warning';
                                            } else {
                                                $statusClass = 'status-success';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    
                                    {{-- CÓDIGO CORRIGIDO (USANDO $item->id, pois é a PK da sua tabela Lotes) --}}
                                    <td class="table-cell text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('estoque.showEntradas', $item->lote_id) }}"
                                               class="text-purple-600 hover:text-purple-800 transition duration-200"
                                               title="Ver Histórico de Entradas deste lote">
                                                <i class="fas fa-clipboard-list text-lg"></i>
                                            </a>
                                            {{-- Aqui você pode adicionar botões de Editar/Excluir do Lote se existirem --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="table-cell text-center py-8">
                                        <div class="text-gray-500">
                                            <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                                            <p class="text-lg font-medium">Nenhum lote em estoque ou entrada registrada.</p>
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
</div>
@endsection