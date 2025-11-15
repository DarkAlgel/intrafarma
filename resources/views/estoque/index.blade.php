@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
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
            
            {{-- START: BARRA DE PESQUISA NA MESMA LINHA E ESTILIZADA --}}
            <div class="mb-6 flex justify-between items-center">
                
                <a href="{{ route('entradas.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Entrada de Lote
                </a>

                <form method="GET" action="{{ route('estoque.index') }}" class="flex items-center space-x-2 w-full md:w-3/5 lg:w-2/5">
                    {{-- Preserva os filtros e a ordenação atuais ao pesquisar --}}
                    <input type="hidden" name="status_filter" value="{{ $statusFilter ?? '' }}">
                    <input type="hidden" name="validade_min" value="{{ $validadeMin ?? '' }}">
                    <input type="hidden" name="sort" value="{{ $currentSort ?? 'validade' }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection ?? 'asc' }}">
                    
                    <div class="relative w-full shadow-md rounded-full bg-white border-2 border-purple-100 hover:border-purple-300 transition duration-300">
                        <input 
                            type="text" 
                            name="search" 
                            class="w-full py-2 pl-4 pr-12 text-gray-800 bg-transparent border-none focus:ring-0 rounded-full" 
                            placeholder="Buscar medicamento ou código..." 
                            value="{{ $searchTerm ?? '' }}" 
                        >
                        <button type="submit" class="absolute right-0 top-0 h-full w-12 text-xl text-purple-600 hover:text-purple-800 transition duration-150" title="Pesquisar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    @if ($searchTerm)
                        <a href="{{ route('estoque.index') }}" class="btn bg-gray-300 hover:bg-gray-400 text-gray-800">
                            <i class="fas fa-eraser mr-2"></i> Limpar
                        </a>
                    @endif
                </form>
            </div>
            {{-- END: BARRA DE PESQUISA --}}

            {{-- START: BLOCO DE FILTROS --}}
            <div class="card mb-6 p-4 bg-white shadow">
                <form method="GET" action="{{ route('estoque.index') }}" class="space-y-4">
                    
                    <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-filter mr-2 text-purple-600"></i> Opções de Filtro
                    </h3>

                    {{-- Preserva o termo de pesquisa e ordenação --}}
                    @if ($searchTerm)
                        <input type="hidden" name="search" value="{{ $searchTerm }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $currentSort ?? 'validade' }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection ?? 'asc' }}">
                    
                    {{-- BOX DE AGRUPAMENTO VISUAL --}}
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        
                        {{-- Filtro por Status --}}
                        <div>
                            <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Status:</label>
                            <div class="relative">
                                <select 
                                    name="status_filter" 
                                    id="status_filter" 
                                    class="appearance-none block w-full rounded-md border-gray-300 shadow-sm pr-10 focus:border-purple-500 focus:ring-purple-500 text-base" 
                                >
                                    <option value="">-- Todos os Status --</option>
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" {{ ($statusFilter ?? '') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down text-sm"></i> {{-- Ícone de seta para seleção --}}
                                </div>
                            </div>
                        </div>

                        {{-- Filtro por Validade Mínima (CORRIGIDO) --}}
                        <div>
                            <label for="validade_min" class="block text-sm font-medium text-gray-700 mb-1">
                                Vencimento APÓS (Data Mínima) 
                                <i class="fas fa-calendar-alt ml-1 text-purple-600"></i>
                                :
                            </label>
                            <div class="relative">
                                <input 
                                    type="date" 
                                    name="validade_min" 
                                    id="validade_min" 
                                    {{-- Classes limpas, removendo pr-10, para o ícone nativo funcionar --}}
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-base" 
                                    value="{{ $validadeMin ?? '' }}"
                                >
                                {{-- Ícones extras removidos daqui --}}
                            </div>
                        </div>
                        
                        {{-- Botões de Ação --}}
                        <div class="flex space-x-2 pt-2 md:pt-0">
                            <button type="submit" class="btn-primary w-full">
                                Aplicar Filtros
                            </button>
                            <a href="{{ route('estoque.index') }}" class="btn-secondary w-full text-center">
                                Limpar Tudo
                            </a>
                        </div>

                    </div>
                    {{-- FIM DO BOX DE AGRUPAMENTO --}}

                </form>
            </div>
            {{-- END: BLOCO DE FILTROS --}}


            <div class="card">
                
                {{-- Código auxiliar para gerar o link de ordenação na tabela --}}
                @php
                    function buildSortLink($column, $currentSort, $currentDirection, $searchTerm, $statusFilter, $validadeMin) {
                        // Determina a nova direção de ordenação
                        $newDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                        
                        // Determina o ícone de ordenação
                        $icon = ($currentSort === $column) 
                                ? ($currentDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down') 
                                : 'fas fa-sort';
                                
                        // Constrói o URL com todos os parâmetros atuais
                        $url = route('estoque.index', array_filter([
                            'search' => $searchTerm,
                            'status_filter' => $statusFilter,
                            'validade_min' => $validadeMin,
                            'sort' => $column,
                            'direction' => $newDirection
                        ]));

                        // Retorna o HTML do link e ícone
                        return '<a href="'.$url.'" class="text-purple-600 hover:text-purple-800 transition duration-150 ml-2" title="Ordenar por '.ucfirst($column).'">
                                    <i class="'.$icon.' text-xs"></i>
                                </a>';
                    }
                @endphp
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Lotes no Inventário</h2>
                    @if ($searchTerm || ($statusFilter ?? null) || ($validadeMin ?? null))
                        <p class="text-sm text-gray-600 mt-1">
                            Exibindo resultados filtrados.
                        </p>
                    @else
                        <p class="text-sm text-gray-600 mt-1">Status, validade e saldo de cada lote de medicamento.</p>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- ⭐️ Medicamento (Ordenável) --}}
                                <th class="table-header relative text-left whitespace-nowrap">
                                    <span class="flex items-center">
                                        <i class="fas fa-pills mr-2"></i>Medicamento (Lote F.)
                                        {!! buildSortLink('medicamento', $currentSort ?? 'validade', $currentDirection ?? 'asc', $searchTerm ?? '', $statusFilter ?? '', $validadeMin ?? '') !!}
                                    </span>
                                </th>
                                
                                {{-- ⭐️ Validade (Ordenável) --}}
                                <th class="table-header relative text-left whitespace-nowrap">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>Validade
                                        {!! buildSortLink('validade', $currentSort ?? 'validade', $currentDirection ?? 'asc', $searchTerm ?? '', $statusFilter ?? '', $validadeMin ?? '') !!}
                                    </span>
                                </th>
                                
                                <th class="table-header text-center whitespace-nowrap"><i class="fas fa-exclamation-triangle mr-2"></i>Dias Vencimento</th>
                                
                                {{-- ⭐️ Quantidade Disponível (Ordenável) --}}
                                <th class="table-header relative text-center whitespace-nowrap">
                                    <span class="flex items-center justify-center">
                                        <i class="fas fa-sort-numeric-up-alt mr-2"></i>Qtd. Disponível
                                        {!! buildSortLink('quantidade_disponivel', $currentSort ?? 'validade', $currentDirection ?? 'asc', $searchTerm ?? '', $statusFilter ?? '', $validadeMin ?? '') !!}
                                    </span>
                                </th>
                                
                                <th class="table-header text-center whitespace-nowrap"><i class="fas fa-info-circle mr-2"></i>Status</th>
                                <th class="table-header text-center whitespace-nowrap">Ações</th>
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
                                            if ($item->status === 'BLOQUEAR DISPENSAÇÃO' || $item->dias_para_vencimento <= 0) {
                                                $statusClass = 'status-danger';
                                            } elseif ($item->status === 'PRÓXIMO DE VENCER') {
                                                $statusClass = 'status-warning';
                                            } else {
                                                $statusClass = 'status-success';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    
                                    {{-- Ação: Link para ver o histórico de entradas do lote --}}
                                    <td class="table-cell text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('estoque.showEntradas', $item->lote_id) }}"
                                               class="text-purple-600 hover:text-purple-800 transition duration-200"
                                               title="Ver Histórico de Entradas deste lote">
                                                <i class="fas fa-clipboard-list text-lg"></i>
                                            </a>
                                            {{-- Seus botões de ação do lote --}}
                                        </div>
                                    </td>
                                
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="table-cell text-center py-8">
                                        <div class="text-gray-500">
                                            <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                                            <p class="text-lg font-medium">Nenhum lote em estoque encontrado com os filtros atuais.</p>
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