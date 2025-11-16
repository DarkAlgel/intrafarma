@extends('layouts.app')

@section('content')

{{-- ⭐️ CORRIGIDO: Removendo a classe md:ml-64 que estava causando a margem dupla. ⭐️ --}}
<div class="flex-1 flex flex-col">
    
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-file-medical mr-2 text-purple-600"></i>
                Ficha do Paciente: {{ $paciente->nome }}
            </h1>
            <div class="flex items-center space-x-4">
                @auth
                {{-- Adicionado o badge de verificação completo para consistência --}}
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
        </div>
    </header>

    <main class="flex-1 p-6">
        
        <div class="card mb-6 p-6">
            <h2 class="text-xl font-bold text-gray-700 mb-4">
                Informações Pessoais
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <p><strong>CPF:</strong> {{ $paciente->cpf }}</p>
                <p><strong>Telefone:</strong> {{ $paciente->telefone ?? 'N/A' }}</p>
                <p><strong>Cidade:</strong> {{ $paciente->cidade ?? 'N/A' }}</p>
            </div>
            <div class="mt-4">
                <a href="{{ route('pacientes.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar para a Lista
                </a>
            </div>
        </div>

        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-700">Histórico de Movimentação (Saídas)</h2>
                <p class="text-sm text-gray-600 mt-1">Todas as retiradas registradas para este paciente.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header whitespace-nowrap">Data/Hora</th>
                            <th class="table-header">Medicamento</th>
                            <th class="table-header text-center">Qtd. Retirada</th>
                            <th class="table-header">Lote/Validade</th>
                            <th class="table-header">Receita</th>
                            <th class="table-header">Responsável</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($movimentacoes as $mov)
                        <tr class="table-row">
                            <td class="table-cell align-top whitespace-nowrap">{{ date('d/m/Y H:i', strtotime($mov->data_dispensa)) }}</td>
                            
                            <td class="table-cell align-top">
                                {{ $mov->lote->medicamento->nome ?? 'Medicamento Indisponível' }}<br>
                                <span class="text-xs text-gray-500">Lote F.: {{ $mov->lote->numero_lote_fornecedor ?? 'N/A' }}</span>
                            </td>
                            
                            <td class="table-cell text-center font-bold text-red-600 align-top whitespace-nowrap">
                                {{ number_format($mov->quantidade_informada, 2, ',', '.') }} {{ $mov->unidade }}
                            </td>
                            
                            <td class="table-cell align-top whitespace-nowrap">
                                Val.: {{ date('d/m/Y', strtotime($mov->lote->validade ?? '')) }}
                            </td>

                            <td class="table-cell align-top whitespace-nowrap">{{ $mov->numero_receita ?? '-' }}</td>
                            <td class="table-cell align-top">{{ $mov->responsavel ?? 'Sistema' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center py-8 text-gray-500">
                                <i class="fas fa-notes-medical text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Nenhuma dispensação registrada para este paciente.</p>
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