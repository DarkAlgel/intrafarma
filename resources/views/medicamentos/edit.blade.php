@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Editar Medicamento</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('medicamentos.update', $medicamento->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-2 font-bold text-gray-700">Nome</label>
            <input type="text" name="nome" value="{{ old('nome', $medicamento->nome) }}"
                class="shadow border rounded w-full py-2 px-3">
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Código</label>
            <input type="text" name="codigo" value="{{ old('codigo', $medicamento->codigo) }}"
                class="shadow border rounded w-full py-2 px-3">
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Laboratório</label>
            <select name="laboratorio_id" class="shadow border rounded w-full py-2 px-3">
                @foreach($laboratorios as $l)
                    <option value="{{ $l->id }}"
                        {{ $l->id == $medicamento->laboratorio_id ? 'selected' : '' }}>
                        {{ $l->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- CAMPO REMOVIDO: classe terapêutica --}}

        <div>
            <label class="block mb-2 font-bold text-gray-700">Tarja</label>
            <select name="tarja" class="shadow border rounded w-full py-2 px-3">
                @foreach($tarjaTipos as $t)
                    <option value="{{ $t }}"
                        {{ $t == $medicamento->tarja ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $t)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Forma de Retirada</label>
            <select name="forma_retirada" class="shadow border rounded w-full py-2 px-3">
                @foreach($formaRetiradaTipos as $f)
                    <option value="{{ $f }}"
                        {{ $f == $medicamento->forma_retirada ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $f)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Forma Física</label>
            <select name="forma_fisica" class="shadow border rounded w-full py-2 px-3">
                @foreach($formaFisicaTipos as $f)
                    <option value="{{ $f }}"
                        {{ $f == $medicamento->forma_fisica ? 'selected' : '' }}>
                        {{ ucfirst($f) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Unidade de Contagem</label>
            <select name="unidade_contagem" class="shadow border rounded w-full py-2 px-3">
                @foreach($unidadeContagemTipos as $u)
                    <option value="{{ $u }}"
                        {{ $u == $medicamento->unidade_contagem ? 'selected' : '' }}>
                        {{ ucfirst($u) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Dosagem</label>
            <input type="text" name="dosagem" value="{{ old('dosagem', $medicamento->dosagem) }}"
                class="shadow border rounded w-full py-2 px-3">
        </div>

        <div>
            <label class="block mb-2 font-bold text-gray-700">Unidade da Dosagem</label>
            <select name="dosagem_unidade" class="shadow border rounded w-full py-2 px-3">
                @foreach($dosagemUnidadeSugestoes as $d)
                    <option value="{{ $d }}"
                        {{ $d == $medicamento->dosagem_unidade ? 'selected' : '' }}>
                        {{ strtoupper($d) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="generico" value="1"
                {{ old('generico', $medicamento->generico) ? 'checked' : '' }}
                class="mr-2">
            <label class="font-bold text-gray-700">Genérico</label>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
            Salvar
        </button>
    </form>
</div>
@endsection
