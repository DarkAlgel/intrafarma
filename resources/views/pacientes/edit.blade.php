@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
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
            <a href="#" class="nav-link">
                <i class="fas fa-boxes mr-3"></i>
                Estoque
            </a>
            <a href="{{ route('pacientes.index') }}" class="nav-link active">
                <i class="fas fa-users mr-3"></i>
                Pacientes
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-clipboard-list mr-3"></i>
                Dispensações
            </a>
            <a href="{{ route('fornecedores.index') }}" class="nav-link">
                <i class="fas fa-truck mr-3"></i>
                Fornecedores
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-cog mr-3"></i>
                Configurações
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-user-edit mr-2 text-purple-600"></i>
                    Editar Paciente
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

        <!-- Content -->
        <main class="flex-1 p-6">
            <!-- Form Card -->
            <div class="card max-w-4xl mx-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Dados do Paciente</h2>
                    <p class="text-sm text-gray-600 mt-1">Atualize as informações do paciente</p>
                </div>
                
                @if ($errors->any())
                <div class="alert-error">
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

                <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nome" class="form-label">
                                <i class="fas fa-user mr-2 text-purple-600"></i>Nome Completo
                            </label>
                            <input type="text" 
                                   name="nome" 
                                   id="nome"
                                   value="{{ old('nome', $paciente->nome) }}" 
                                   class="form-input" 
                                   required>
                        </div>

                        <div>
                            <label for="cpf" class="form-label">
                                <i class="fas fa-id-card mr-2 text-purple-600"></i>CPF
                            </label>
                           <input type="text" 
                                   name="cpf" 
                                   id="cpf"
                                   value="{{ old('cpf', $paciente->cpf) }}" 
                                   class="form-input" 
                                   placeholder="000.000.000-00"
                                   oninput="mascaraCPF(this)"
                                   maxlength="14"
                                   required>
                        </div>

                        <div>
                            <label for="telefone" class="form-label">
                                <i class="fas fa-phone mr-2 text-purple-600"></i>Telefone
                            </label>
                           <input type="text" 
                                   name="telefone" 
                                   id="telefone"
                                   value="{{ old('telefone', $paciente->telefone) }}" 
                                   class="form-input" 
                                   placeholder="(00) 00000-0000"
                                   oninput="mascaraTelefone(this)"
                                   maxlength="15">
                        </div>

                        <div>
                            <label for="cidade" class="form-label">
                                <i class="fas fa-city mr-2 text-purple-600"></i>Cidade
                            </label>
                            <input type="text" 
                                   name="cidade" 
                                   id="cidade"
                                   value="{{ old('cidade', $paciente->cidade) }}" 
                                   class="form-input" 
                                   placeholder="Digite a cidade">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('pacientes.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
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

            document.addEventListener('DOMContentLoaded', () => {
                const cpf = document.getElementById('cpf');
                const tel = document.getElementById('telefone');
                if (cpf && cpf.value) mascaraCPF(cpf);
                if (tel && tel.value) mascaraTelefone(tel);
            });
            </script>
            @endpush
        </main>
    </div>
</div>

@push('scripts')
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
@endpush

@endsection
