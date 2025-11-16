@extends('layouts.app')

@section('content')
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-notes-medical mr-2 text-purple-600"></i> Meu Histórico
        </h1>
    </div>
</header>

<main class="flex-1 p-6">
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-header">Data</th>
                    <th class="table-header">Medicamento</th>
                    <th class="table-header">Nome Comercial</th>
                    <th class="table-header">Dosagem</th>
                    <th class="table-header">Quantidade</th>
                    <th class="table-header">Unidade</th>
                    <th class="table-header">Responsável</th>
                    <th class="table-header">Receita</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($historico as $h)
                <tr>
                    <td class="px-6 py-3">{{ date('d/m/Y', strtotime($h->data_dispensa)) }}</td>
                    <td class="px-6 py-3">{{ $h->medicamento }} ({{ $h->codigo }})</td>
                    <td class="px-6 py-3">{{ $h->nome_comercial }}</td>
                    <td class="px-6 py-3">{{ $h->dosagem }}</td>
                    <td class="px-6 py-3">{{ $h->quantidade_informada }}</td>
                    <td class="px-6 py-3">{{ $h->unidade }}</td>
                    <td class="px-6 py-3">{{ $h->responsavel }}</td>
                    <td class="px-6 py-3">{{ $h->numero_receita }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Nenhum registro encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection