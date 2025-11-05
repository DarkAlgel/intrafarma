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
    
    {{-- Título da Página --}}
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-person-plus-fill me-2 fs-3 text-primary"></i>
        <h2 class="page-title">Cadastrar Paciente</h2>
    </div>

    {{-- Card do Form --}}
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

        <form action="{{ route('pacientes.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="label-custom">Nome</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="nome" value="{{ old('nome') }}" class="form-control" placeholder="Nome completo" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">CPF</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="cpf" value="{{ old('cpf') }}" class="form-control" placeholder="000.000.000-00" required oninput="mascaraCPF(this)" maxlength="14">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">Telefone</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" name="telefone" value="{{ old('telefone') }}" class="form-control" placeholder="(00) 00000-0000" oninput="mascaraTelefone(this)" maxlength="15">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="label-custom">Cidade</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                        <input type="text" name="cidade" value="{{ old('cidade') }}" class="form-control" placeholder="Cidade">
                    </div>
                </div>

            </div>

            {{-- Botões --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-lg"></i> Salvar Paciente
                </button>
            </div>

        </form>

    </div>
</div>

@push('scripts')
<script>
function mascaraCPF(input) {
    let v = input.value.replace(/\D/g, "");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    input.value = v;
}

function mascaraTelefone(input) {
    let v = input.value.replace(/\D/g, "");

    if (v.length <= 10) {
        v = v.replace(/(\d{2})(\d)/, "($1) $2");
        v = v.replace(/(\d{4})(\d)/, "$1-$2");
    } else {
        v = v.replace(/(\d{2})(\d)/, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
    }

    input.value = v;
}
</script>
@endpush

@endsection