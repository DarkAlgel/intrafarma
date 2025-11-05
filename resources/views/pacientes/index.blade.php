@extends('layouts.app')

@section('content')

<style>
    .page-title {
        color: #164A73;
        font-weight: 700;
    }

    .btn-primary-custom {
        background-color: #1F6AA5;
        border: none;
        transition: 0.2s;
    }
    .btn-primary-custom:hover {
        background-color: #164A73;
    }

    .btn-outline-custom {
        color: #164A73;
        border: 1px solid #164A73;
    }
    .btn-outline-custom:hover {
        background-color: #164A73;
        color: #fff;
    }

    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.08);
        padding: 20px;
        background: #fff;
    }

    table thead {
        background-color: #164A73;
        color: #fff;
        font-weight: 600;
    }

    td {
        vertical-align: middle;
    }
</style>

<div class="container mt-5">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill fs-2 text-primary"></i>
            <h2 class="page-title m-0">Pacientes</h2>
        </div>

        <a href="{{ route('pacientes.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-person-plus-fill"></i> Novo Paciente
        </a>
    </div>

    {{-- Tabela --}}
    <div class="card-custom">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Cidade</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pacientes as $paciente)
                <tr>
                    <td>{{ $paciente->nome }}</td>
                    <td>{{ $paciente->cpf }}</td>
                    <td>{{ $paciente->telefone }}</td>
                    <td>{{ $paciente->cidade }}</td>
                    <td class="d-flex gap-2">

                        {{-- Editar --}}
                        <a href="{{ route('pacientes.edit', $paciente->id) }}" 
                           class="btn btn-outline-custom btn-sm">
                           <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- Excluir --}}
                        <form action="{{ route('pacientes.destroy', $paciente->id) }}" 
                              method="POST"
                              onsubmit="return confirm('Deseja realmente excluir este paciente?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        Nenhum paciente cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
