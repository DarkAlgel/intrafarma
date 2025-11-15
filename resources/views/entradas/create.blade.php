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
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-pills mr-3"></i>
                Medicamentos
            </a>
            {{-- Link Ativo para Estoque --}}
            <a href="{{ route('estoque.index') }}" class="nav-link active">
                <i class="fas fa-boxes mr-3"></i>
                Estoque
            </a>
            <a href="{{ route('pacientes.index') }}" class="nav-link">
                <i class="fas fa-users mr-3"></i>
                Pacientes
            </a>
            <a href="{{ route('dispensacoes.create') }}" class="nav-link {{ request()->routeIs('dispensacoes.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                Dispensações
            </a>
            <a href="{{ route('fornecedores.index') }}" class="nav-link {{ request()->routeIs('fornecedores.*') ? 'active' : '' }}">
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
                    <i class="fas fa-truck-loading mr-2 text-purple-600"></i>
                    Nova Entrada de Lote
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
            <div class="card max-w-4xl mx-auto">
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Detalhes da Entrada</h2>
                    <p class="text-sm text-gray-600 mt-1">Preencha os dados do medicamento e lote.</p>
                </div>
                
                {{-- Exibição de Erros de Validação usando a classe alert-error --}}
                @if ($errors->any())
                <div class="alert-error p-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Ops!</strong> Alguns campos precisam ser corrigidos:
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form method="POST" action="{{ route('entradas.store') }}" class="p-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <label for="medicamento_id" class="block text-sm font-medium text-gray-700">Medicamento *</label>
                            <select id="medicamento_id" name="medicamento_id" class="form-input" required>
                                <option value="">Selecione um Medicamento</option>
                                @foreach($medicamentos as $id => $nome)
                                    <option value="{{ $id }}" {{ old('medicamento_id') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                @endforeach
                            </select>
                            @error('medicamento_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fornecedor_id" class="block text-sm font-medium text-gray-700">Fornecedor *</label>
                            <select id="fornecedor_id" name="fornecedor_id" class="form-input" required>
                                <option value="">Selecione um Fornecedor</option>
                                @foreach($fornecedores as $id => $nome)
                                    <option value="{{ $id }}" {{ old('fornecedor_id') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                @endforeach
                            </select>
                            @error('fornecedor_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="data_entrada" class="block text-sm font-medium text-gray-700">Data de Entrada *</label>
                            <input type="date" id="data_entrada" name="data_entrada" class="form-input" value="{{ old('data_entrada', date('Y-m-d')) }}" required>
                            @error('data_entrada') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 border-t pt-6 mt-6 mb-4">Dados do Lote e Produto</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        
                        <div>
                            <label for="numero_lote_fornecedor" class="block text-sm font-medium text-gray-700">Nº do Lote (Fornecedor) *</label>
                            <input type="text" id="numero_lote_fornecedor" name="numero_lote_fornecedor" class="form-input" value="{{ old('numero_lote_fornecedor') }}" required>
                            @error('numero_lote_fornecedor') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="data_fabricacao" class="block text-sm font-medium text-gray-700">Data de Fabricação *</label>
                            <input type="date" id="data_fabricacao" name="data_fabricacao" class="form-input" value="{{ old('data_fabricacao') }}" required>
                            @error('data_fabricacao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="validade" class="block text-sm font-medium text-gray-700">Data de Validade *</label>
                            <input type="date" id="validade" name="validade" class="form-input" value="{{ old('validade') }}" required>
                            @error('validade') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="nome_comercial" class="block text-sm font-medium text-gray-700">Nome Comercial (Opcional)</label>
                            <input type="text" id="nome_comercial" name="nome_comercial" class="form-input" value="{{ old('nome_comercial') }}">
                            @error('nome_comercial') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 border-t pt-6 mt-6 mb-4">Quantidade e Unidade</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        
                        <div>
                            <label for="quantidade_informada" class="block text-sm font-medium text-gray-700">Qtd. da Embalagem *</label>
                            <input type="number" step="any" id="quantidade_informada" name="quantidade_informada" class="form-input" value="{{ old('quantidade_informada') }}" required>
                            @error('quantidade_informada') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="unidade" class="block text-sm font-medium text-gray-700">Unidade da Embalagem *</label>
                            <select id="unidade" name="unidade" class="form-input" required>
                                <option value="">Selecione a Unidade</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u }}" {{ old('unidade') == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                                @endforeach
                            </select>
                            @error('unidade') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="unidades_por_embalagem" class="block text-sm font-medium text-gray-700">Unidades por Embalagem (Opcional)</label>
                            <input type="number" step="1" id="unidades_por_embalagem" name="unidades_por_embalagem" class="form-input" value="{{ old('unidades_por_embalagem') }}">
                            <p class="text-xs text-gray-500 mt-1">Ex: 1 Caixa com 10 comprimidos.</p>
                            @error('unidades_por_embalagem') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="estado" name="estado" class="form-input">
                                <option value="">Selecione o Estado</option>
                                @foreach($estados as $e)
                                    <option value="{{ $e }}" {{ old('estado') == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                                @endforeach
                            </select>
                            @error('estado') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                        <textarea id="observacao" name="observacao" rows="3" class="form-input">{{ old('observacao') }}</textarea>
                        @error('observacao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                        <a href="{{ route('estoque.index') }}" class="btn-secondary mr-4">
                            <i class="fas fa-arrow-left mr-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i> Registrar Entrada de Lote
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection