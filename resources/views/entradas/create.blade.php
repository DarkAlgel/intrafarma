@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-truck-loading mr-3 text-indigo-600"></i>
            Nova Entrada de Lote
        </h1>
        <a href="{{ route('estoque.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Estoque
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        
        {{-- Mensagens de feedback (sucesso/erro) --}}
        @if (session('success'))
            <div class="alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-danger mb-4">
                <p>Por favor, corrija os seguintes erros:</p>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- Formulário que será enviado para EntradaController@store --}}
        <form method="POST" action="{{ route('entradas.store') }}">
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

            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i> Registrar Entrada de Lote
                </button>
            </div>
        </form>
    </div>
</div>
@endsection