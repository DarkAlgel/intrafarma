@extends('layouts.app')

@section('content')

<style>
    .page-title {
        color: #164A73;
        font-weight: 700;
    }

    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.08);
    }

    .label-custom {
        font-weight: 600;
        color: #164A73;
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
</style>

<div class="container mt-5">
    
    {{-- Título --}}
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-pencil-square fs-3 text-primary me-2"></i>
        <h2 class="page-title">Editar Paciente</h2>
    </div>

    {{-- Card --}}
    <div class="card card-custom p-4">

        @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Alguns campos precisam ser corrigidos:
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="label-custom">Nome</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="nome" value="{{ old('nome', $paciente->nome) }}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">CPF</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-card-text"></i></span>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $paciente->cpf) }}" class="form-control" placeholder="000.000.000-00" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">Telefone</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $paciente->telefone) }}" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">Cidade</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                        <input type="text" name="cidade" value="{{ old('cidade', $paciente->cidade) }}" class="form-control" placeholder="Cidade">
                    </div>
                </div>

            </div>

            {{-- Botões --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-custom">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>

                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-lg"></i> Salvar Alterações
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Máscaras --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    // CPF
    const cpfInput = document.getElementById('cpf');
    function formatCPF(value) {
        return value
            .replace(/\D/g, '')
            .slice(0, 11)
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    if (cpfInput) {
        cpfInput.value = formatCPF(cpfInput.value);

        cpfInput.addEventListener('input', function(e) {
            e.target.value = formatCPF(e.target.value);
        });
    }

    // TELEFONE
    const telInput = document.getElementById('telefone');
    function formatPhone(value) {
        return value
            .replace(/\D/g, '')
            .slice(0, 11)
            .replace(/^(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{5})(\d)/, '$1-$2');
    }

    if (telInput) {
        telInput.value = formatPhone(telInput.value);

        telInput.addEventListener('input', function(e) {
            e.target.value = formatPhone(e.target.value);
        });
    }

});
</script>

@endsection
