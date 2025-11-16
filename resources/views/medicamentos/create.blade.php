@extends('layouts.app')

@section('content')
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-purple-600"></i>
            Cadastrar Novo Medicamento
        </h1>
        <a href="{{ route('medicamentos.index') }}" 
           class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
            Voltar
        </a>
    </div>
</header>

<main class="flex-1 p-6">

    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('medicamentos.store') }}" method="POST">
            @csrf

            <div class="p-6">

                @if ($errors->any())
                    <div class="mb-5 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                        <span class="font-bold">Ops!</span> Corrija os erros abaixo:
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-lg font-medium leading-6 text-gray-900">1. Identificação</h3>
                <p class="mt-1 text-sm text-gray-600">Informações básicas do medicamento.</p>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-2">
                        <label for="codigo" class="block text-sm font-medium text-gray-700">Código Único *</label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-4">
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome (Princípio Ativo + Dosagem + Apresentação) *</label>
                        <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="laboratorio_id" class="block text-sm font-medium text-gray-700">Laboratório *</label>
                        <select id="laboratorio_id" name="laboratorio_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm @error('laboratorio_id') border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            @foreach($laboratorios as $lab)
                                <option value="{{ $lab->id }}" {{ old('laboratorio_id') == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('laboratorio_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="classe_terapeutica_id" class="block text-sm font-medium text-gray-700">Classe Terapêutica *</label>
                        <select id="classe_terapeutica_id" name="classe_terapeutica_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm @error('classe_terapeutica_id') border-red-500 @enderror">
                            <option value="">Selecione...</option>
                             @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_terapeutica_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nome }} (Cód: {{ $classe->codigo_classe }})
                                </option>
                            @endforeach
                        </select>
                        @error('classe_terapeutica_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <h3 class="text-lg font-medium leading-6 text-gray-900">2. Classificação</h3>
                <p class="mt-1 text-sm text-gray-600">Define como o medicamento é controlado e apresentado.</p>
                
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                    <div>
                        <label for="tarja" class="block text-sm font-medium text-gray-700">Tarja *</label>
                        <select id="tarja" name="tarja" required class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm">
                            <option value="">Selecione...</option>
                            @foreach($tarjaTipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('tarja') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="forma_retirada" class="block text-sm font-medium text-gray-700">Forma de Retirada *</label>
                        <select id="forma_retirada" name="forma_retirada" required class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm">
                            <option value="">Selecione...</option>
                            @foreach($formaRetiradaTipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('forma_retirada') == $tipo ? 'selected' : '' }}>
                                    {{ $tipo === 'MIP' ? 'MIP (Sem Prescrição)' : 'Com Prescrição' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="forma_fisica" class="block text-sm font-medium text-gray-700">Forma Física *</label>
                        <select id="forma_fisica" name="forma_fisica" required class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm">
                            <option value="">Selecione...</option>
                            @foreach($formaFisicaTipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('forma_fisica') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst($tipo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <h3 class="text-lg font-medium leading-6 text-gray-900">3. Dosagem e Controle</h3>
                <p class="mt-1 text-sm text-gray-600">Detalhes sobre a dosagem e unidades de medida.</p>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-2">
                        <label for="dosagem_valor" class="block text-sm font-medium text-gray-700">Valor da Dosagem *</label>
                        <input type="number" step="0.001" name="dosagem_valor" id="dosagem_valor" value="{{ old('dosagem_valor') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('dosagem_valor') border-red-500 @enderror"
                               placeholder="Ex: 500">
                    </div>
                    
                    <div class="sm:col-span-1">
                        <label for="dosagem_unidade" class="block text-sm font-medium text-gray-700">Unidade *</label>
                        <input type="text" name="dosagem_unidade" id="dosagem_unidade" value="{{ old('dosagem_unidade') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('dosagem_unidade') border-red-500 @enderror"
                               placeholder="Ex: mg">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="limite_minimo" class="block text-sm font-medium text-gray-700">Estoque Mínimo *</label>
                        <input type="number" step="0.01" name="limite_minimo" id="limite_minimo" value="{{ old('limite_minimo', 0) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('limite_minimo') border-red-500 @enderror"
                               placeholder="0">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="apresentacao" class="block text-sm font-medium text-gray-700">Apresentação (Ex: Comprimido, Caixa) *</label>
                        <select id="apresentacao" name="apresentacao" required class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm">
                            <option value="">Selecione...</option>
                            @foreach($unidadeContagemTipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('apresentacao') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst($tipo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="sm:col-span-3">
                        <label for="unidade_base" class="block text-sm font-medium text-gray-700">Unidade Base (para Estoque) *</label>
                        <select id="unidade_base" name="unidade_base" required class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm">
                            <option value="">Selecione...</option>
                            @foreach($unidadeContagemTipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('unidade_base') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst($tipo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">
                
                <h3 class="text-lg font-medium leading-6 text-gray-900">4. Opções</h3>
                <div class="mt-6 space-y-4">
                    <div class="flex items-start">
                        <input type="hidden" name="generico" value="0">
                        <div class="flex h-5 items-center">
                            <input id="generico" name="generico" type="checkbox" value="1" 
                                   class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500" 
                                   {{ old('generico', '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="generico" class="font-medium text-gray-700">É Genérico?</label>
                            <p class="text-gray-500">Marque se este for um medicamento genérico.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <input type="hidden" name="ativo" value="0">
                        <div class="flex h-5 items-center">
                            <input id="ativo" name="ativo" type="checkbox" value="1" 
                                   class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500" 
                                   {{ old('ativo', '1') == '1' ? 'checked' : '' }}> </div>
                        <div class="ml-3 text-sm">
                            <label for="ativo" class="font-medium text-gray-700">Ativo</label>
                            <p class="text-gray-500">Desmarque para arquivar o medicamento (não aparecerá em novas entradas/saídas).</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end space-x-4 bg-gray-50 px-6 py-4 text-right">
                <a href="{{ route('medicamentos.index') }}" 
                   class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    Cancelar
                </a>
                
                {{-- A classe 'btn-primary' veio do seu exemplo de listagem --}}
                <button type="submit" 
                        class="btn-primary">
                    <i class="fas fa-save mr-2"></i> Salvar Medicamento
                </button>
            </div>

        </form>
    </div>

</main>
@endsection