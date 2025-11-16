@extends('layouts.app')

@section('content')

{{-- Container de alinhamento para o layout mestre --}}
{{-- ⭐️ CORRIGIDO: Removida a classe md:ml-64 daqui para evitar margem dupla. ⭐️ --}}
<div class="flex-1 flex flex-col">
    
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-history mr-2 text-purple-600"></i>
                Histórico de Dispensações
            </h1>
            {{-- Barra de status do usuário --}}
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
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Transações Recentes</h2>
                    <p class="text-sm text-gray-600 mt-1">Lista de todas as dispensações registradas.</p>
                </div>
                <a href="{{ route('dispensacoes.create') }}" class="btn-primary px-4 py-2">
                    <i class="fas fa-plus mr-2"></i> Nova Dispensação
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header align-top whitespace-nowrap">Data</th>
                            <th class="table-header align-top whitespace-nowrap">Paciente (CPF)</th>
                            <th class="table-header align-top">Medicamento</th>
                            <th class="table-header text-center align-top whitespace-nowrap">Qtd. Dispensada</th>
                            <th class="table-header align-top whitespace-nowrap">Lote Fornecedor</th>
                            <th class="table-header align-top whitespace-nowrap">Receita</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($dispensacoes as $dispensa)
                        <tr class="table-row">
                            <td class="table-cell align-top whitespace-nowrap">{{ date('d/m/Y H:i', strtotime($dispensa->data_dispensa)) }}</td>
                            <td class="table-cell font-medium text-gray-900 align-top">
                                {{ $dispensa->paciente->nome ?? 'N/A' }}<br>
                                <span class="text-xs text-gray-500">{{ $dispensa->paciente->cpf ?? '' }}</span>
                            </td>
                            <td class="table-cell align-top">
                                {{ $dispensa->lote->medicamento->nome ?? 'N/A' }} 
                                <span class="text-xs text-gray-500">({{ $dispensa->lote->nome_comercial ?? 'S/Nome Comercial' }})</span>
                            </td>
                            <td class="table-cell text-center font-bold text-red-600 align-top whitespace-nowrap">
                                {{ number_format($dispensa->quantidade_informada, 2, ',', '.') }} {{ $dispensa->unidade }}
                            </td>
                            <td class="table-cell align-top whitespace-nowrap">{{ $dispensa->lote->numero_lote_fornecedor ?? 'N/A' }}</td>
                            <td class="table-cell align-top whitespace-nowrap">{{ $dispensa->numero_receita ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center py-8 text-gray-500">
                                <i class="fas fa-archive text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Nenhuma dispensação registrada ainda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Links de Paginação --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $dispensacoes->links() }}
            </div>
        </div>
    </main>
</div>
@endsection