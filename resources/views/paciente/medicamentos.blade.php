@extends('layouts.app')

@section('content')
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-pills mr-2 text-purple-600"></i> Medicamentos Disponíveis
        </h1>
    </div>
</header>

<main class="flex-1 p-6">
    <div class="mb-6 flex items-center justify-between">
        <form method="GET" action="{{ route('paciente.medicamentos') }}" class="flex items-center space-x-2 w-full md:w-3/4">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome ou código" class="w-full py-2 px-4 border rounded-lg" />
            <select name="tarja" class="py-2 px-3 border rounded-lg">
                <option value="">Tarja</option>
                <option value="sem_tarja" {{ $tarja==='sem_tarja' ? 'selected' : '' }}>Sem Tarja</option>
                <option value="tarja_vermelha" {{ $tarja==='tarja_vermelha' ? 'selected' : '' }}>Vermelha</option>
                <option value="tarja_preta" {{ $tarja==='tarja_preta' ? 'selected' : '' }}>Preta</option>
            </select>
            <select name="generico" class="py-2 px-3 border rounded-lg">
                <option value="">Tipo</option>
                <option value="sim" {{ $generico==='sim' ? 'selected' : '' }}>Genérico</option>
                <option value="nao" {{ $generico==='nao' ? 'selected' : '' }}>Marca</option>
            </select>
            <button type="submit" class="btn-primary"><i class="fas fa-search mr-2"></i>Buscar</button>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-header text-left">Medicamento</th>
                    <th class="table-header text-left">Código</th>
                    <th class="table-header text-center">Disponível</th>
                    <th class="table-header text-left">Tarja</th>
                    <th class="table-header text-left">Tipo</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($medicamentos as $m)
                <tr>
                    <td class="px-6 py-3 text-gray-800">{{ $m->nome }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $m->codigo }}</td>
                    <td class="px-6 py-3 text-center font-semibold">{{ $m->quantidade_disponivel }}</td>
                    <td class="px-6 py-3">{{ str_replace('_',' ', strtoupper($m->tarja)) }}</td>
                    <td class="px-6 py-3">{{ $m->generico ? 'Genérico' : 'Marca' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum medicamento encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection