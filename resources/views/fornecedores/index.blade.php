@extends('layouts.app')

@section('content')

<div x-data="{ openModal: {{ $errors->any() ? 'true' : 'false' }} }"> 
    
    {{-- Container FLEX Principal: Contém o Menu e o Conteúdo --}}
    <div class="flex min-h-screen bg-gray-100">
        
        {{-- ⭐️ START: SIDEBAR (MENU) ⭐️ --}}
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
                <a href="{{ route('estoque.index') }}" 
                   class="nav-link {{ request()->routeIs('estoque.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes mr-3"></i>
                    Estoque
                </a>
                <a href="{{ route('pacientes.index') }}" class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Pacientes
                </a>
                <a href="{{ route('dispensacoes.create') }}" class="nav-link {{ request()->routeIs('dispensacoes.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Dispensações
                </a>
                <a href="{{ route('fornecedores.index') }}" class="nav-link active">
                    <i class="fas fa-truck mr-3"></i>
                    Fornecedores
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-cog mr-3"></i>
                    Configurações
                </a>
            </nav>
        </div>
        {{-- END: SIDEBAR (MENU) --}}

        {{-- ⭐️ START: CONTEÚDO PRINCIPAL (COM MARGEM md:ml-64) ⭐️ --}}
        <div class="flex-1 flex flex-col md:ml-64">
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
                {{-- Botão que Abre o Modal --}}
                <div class="mb-6 flex justify-end">
                    <button @click="openModal = true" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i> Novo Fornecedor
                    </button>
                </div>

                {{-- Mensagens de Sucesso/Erro --}}
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                {{-- Tabela de Fornecedores --}}
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Lista de Fornecedores</h2>
                        <p class="text-sm text-gray-600 mt-1">Gerencie os fornecedores cadastrados</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Nome (Ordenável) --}}
                                    <th class="table-header">
                                        <a href="{{ route('fornecedores.index', ['sort' => 'nome', 'direction' => $sort === 'nome' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                            <i class="fas fa-user mr-2"></i>Nome
                                            @if($sort === 'nome')
                                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                            @endif
                                        </a>
                                    </th>
                                    {{-- Tipo (Ordenável) --}}
                                    <th class="table-header">
                                        <a href="{{ route('fornecedores.index', ['sort' => 'tipo', 'direction' => $sort === 'tipo' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                            <i class="fas fa-tags mr-2"></i>Tipo
                                            @if($sort === 'tipo')
                                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                            @endif
                                        </a>
                                    </th>
                                    {{-- Contato (Ordenável) --}}
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

                    @if($fornecedores->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Mostrando <span class="font-medium">{{ $fornecedores->firstItem() }}</span> a <span class="font-medium">{{ $fornecedores->lastItem() }}</span> de <span class="font-medium">{{ $fornecedores->total() }}</span> resultado(s)
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
        {{-- END: CONTEÚDO PRINCIPAL --}}
    </div>
    
    {{-- START: ESTRUTURA DO MODAL DE LUXO --}}
    <div x-show="openModal" 
         class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center overflow-y-auto z-50" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">

        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl transition-all duration-300 transform scale-95"
             @click.away="openModal = false">
            
            <div class="px-6 py-4 border-b border-gray-200 bg-purple-50 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold text-purple-800 flex items-center">
                    <i class="fas fa-truck-loading mr-3"></i> Cadastro de Novo Fornecedor
                </h3>
                <button type="button" @click="openModal = false" class="text-purple-500 hover:text-purple-700 transition duration-150">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('fornecedores.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                {{-- Campo Nome --}}
                <div>
                    <label for="nome" class="block text-sm font-semibold text-gray-700 mb-1">Nome da Empresa <span class="text-red-500">*</span></label>
                    <input type="text" name="nome" id="nome" required 
                           class="form-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600 p-3" 
                           value="{{ old('nome') }}">
                    @error('nome')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Tipo (ENUM) --}}
                <div>
                    <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-1">Tipo de Fornecimento <span class="text-red-500">*</span></label>
                    <select name="tipo" id="tipo" required
                            class="form-select block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600 p-3">
                        <option value="">Selecione o Tipo</option>
                        @foreach($fornecedorTipos as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>
                                {{ ucfirst($tipo) }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Campo Contato --}}
                <div>
                    <label for="contato" class="block text-sm font-semibold text-gray-700 mb-1">Contato (Telefone/Email)</label>
                    <input type="text" name="contato" id="contato" 
                           class="form-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600 p-3" 
                           value="{{ old('contato') }}">
                    @error('contato')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões de Ação do Modal --}}
                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="button" @click="openModal = false" 
                            class="btn-secondary px-4 py-2 mr-3 transition duration-150">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary px-4 py-2">
                        <i class="fas fa-save mr-2"></i> Salvar Fornecedor
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- END: ESTRUTURA DO MODAL DE LUXO --}}
    
</div>
@endsection