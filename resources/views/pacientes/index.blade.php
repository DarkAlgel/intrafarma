@extends('layouts.app')

@section('content')
{{-- ⭐️ START: Alpine Data para o Live Search ⭐️ --}}
{{-- O flex-1 flex flex-col é o wrapper de conteúdo, sem a margem dupla. --}}
<div x-data="{ searchTermClient: '{{ request('search') ?? '' }}' }" class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2 text-purple-600"></i>
                Gerenciamento de Pacientes
            </h1>
            
            {{-- BARRA DE STATUS DO USUÁRIO --}}
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
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </button>
                </form>
                @endauth
            </div>
            {{-- FIM BARRA DE STATUS DO USUÁRIO --}}
        </div>
    </header>

    <main class="flex-1 p-6">
        
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
        @endif

        {{-- ⭐️ START: FILTRO / BUSCA E BOTÃO NOVO PACIENTE ⭐️ --}}
        <div class="card p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtros e Ações</h3>
            
            <form method="GET" action="{{ route('pacientes.index') }}" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end w-full">

                    {{-- 🔍 Campo de busca (Controlado pelo Alpine para Live Search) --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Pesquisar (Nome, CPF ou Telefone)</label>
                        <input type="text"
                            name="search"
                            {{-- ⭐️ MUDANÇA: x-model armazena o valor localmente para o filtro instantâneo ⭐️ --}}
                            x-model="searchTermClient"
                            value="{{ request('search') }}"
                            placeholder="Digite para filtrar..."
                            class="w-full h-[42px] px-3 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    {{-- 🏙️ Select Cidade --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <select name="cidade"
                            class="w-full h-[42px] px-3 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Todas</option>
                            @if(isset($cidades))
                                @foreach($cidades as $cidade)
                                    <option value="{{ $cidade }}" {{ request('cidade') == $cidade ? 'selected' : '' }}>
                                        {{ $cidade }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Botões de Ação do Filtro --}}
                    <div class="flex space-x-3">
                        <button type="submit" class="btn-primary flex-1 h-[42px] flex items-center justify-center">
                            <i class="fas fa-search mr-1"></i> Filtrar
                        </button>

                        @if(request()->has('search') || request()->has('cidade'))
                            <a href="{{ route('pacientes.index') }}"
                            class="btn-secondary flex-1 h-[42px] flex items-center justify-center">
                                <i class="fas fa-times mr-1"></i> Limpar
                            </a>
                        @endif
                    </div>
                    
                    {{-- Botão Novo Paciente (Ao lado dos filtros) --}}
                    <div class="flex justify-end">
                        <a href="{{ route('pacientes.create') }}" class="btn-primary h-[42px] flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i> Novo Paciente
                        </a>
                    </div>

                </div>
            </form>
        </div>
        {{-- ⭐️ END: FILTRO / BUSCA E BOTÃO NOVO PACIENTE ⭐️ --}}


        {{-- LISTA --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Pacientes</h2>
                <p class="text-sm text-gray-600 mt-1">Gerencie todos os pacientes cadastrados</p>
            </div>

            @php $dir = request('dir') === 'asc' ? 'desc' : 'asc'; @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- NOME --}}
                            <th class="table-header">
                                <a href="{{ route('pacientes.index', array_merge(request()->except(['search', 'cidade']), ['sort'=>'nome','dir'=>$dir])) }}"
                                    class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Nome
                                    @if(request('sort')==='nome')
                                        <i class="fas fa-sort-{{ request('dir')=='asc'?'up':'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>

                            {{-- CPF --}}
                            <th class="table-header">
                                <i class="fas fa-id-card mr-2"></i>CPF
                            </th>

                            {{-- TELEFONE --}}
                            <th class="table-header">
                                <i class="fas fa-phone mr-2"></i>Telefone
                            </th>

                            {{-- CIDADE --}}
                            <th class="table-header">
                                <a href="{{ route('pacientes.index', array_merge(request()->except(['search', 'cidade']), ['sort'=>'cidade','dir'=>$dir])) }}"
                                    class="flex items-center">
                                    <i class="fas fa-city mr-2"></i>
                                    Cidade
                                    @if(request('sort')==='cidade')
                                        <i class="fas fa-sort-{{ request('dir')=='asc'?'up':'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>

                            {{-- AÇÕES --}}
                            <th class="table-header text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pacientes as $paciente)
                        {{-- ⭐️ LÓGICA DE FILTRAGEM INSTANTÂNEA ⭐️ --}}
                        <tr class="table-row" 
                            x-show="!searchTermClient || 
                                (
                                '{{ strtolower($paciente->nome) }}'.includes(searchTermClient.toLowerCase()) || 
                                '{{ str_replace(['.', '-'], '', $paciente->cpf) }}'.includes(searchTermClient.replace(/[\.\-]/g, '')) || 
                                '{{ str_replace(['(', ')', ' ', '-'], '', $paciente->telefone) }}'.includes(searchTermClient.replace(/[\(\) \-]/g, ''))
                                )"
                        >

                            {{-- Nome + foto --}}
                            <td class="table-cell">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                    </div>

                                    {{-- LINK PARA A FICHA / HISTÓRICO --}}
                                    <a href="{{ route('pacientes.show', $paciente->id) }}"
                                        class="font-medium text-gray-900 hover:text-purple-600 transition">
                                        {{ $paciente->nome }}
                                    </a>
                                </div>
                            </td>

                            <td class="table-cell font-mono text-sm">{{ $paciente->cpf }}</td>
                            <td class="table-cell">{{ $paciente->telefone }}</td>
                            <td class="table-cell">{{ $paciente->cidade }}</td>

                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center space-x-3">

                                    {{-- Ver Ficha --}}
                                    <a href="{{ route('pacientes.show', $paciente->id) }}"
                                        class="text-purple-600 hover:text-purple-800"
                                        title="Ver Ficha e Histórico">
                                        <i class="fas fa-file-medical"></i>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('pacientes.edit', $paciente->id) }}" 
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Excluir (Chama SweetAlert) --}}
                                    <form action="{{ route('pacientes.destroy', $paciente->id) }}" 
                                            method="POST"
                                            class="inline delete-form-{{ $paciente->id }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete('{{ $paciente->id }}', '{{ $paciente->nome }}')"
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center py-8">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Nenhum paciente encontrado</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Rodapé da tabela com contagem --}}
            @if(isset($pacientes) && count($pacientes) > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Mostrando <span class="font-medium">{{ count($pacientes) }}</span> paciente(s)
                    </p>
                    <div class="text-sm text-gray-500">
                        Atualizado em {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>
</div>

{{-- SWEETALERT JS FUNCTION (Deve estar no final da View) --}}
@push('scripts')
<script>
/**
 * Exibe o Modal SweetAlert de confirmação antes de excluir.
 */
function confirmDelete(pacienteId, pacienteNome) {
    if (typeof Swal === 'undefined') {
        alert(`Confirma a exclusão de ${pacienteNome}?`);
        document.querySelector(`.delete-form-${pacienteId}`).submit();
        return;
    }

    Swal.fire({
        title: "Tem certeza?",
        html: `Você realmente deseja <strong>excluir permanentemente</strong> o paciente <strong>${pacienteNome}</strong>? Esta ação é <strong>irreversível</strong>.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sim, Excluir!',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector(`.delete-form-${pacienteId}`).submit();
        }
    });
}
</script>
@endpush

@endsection