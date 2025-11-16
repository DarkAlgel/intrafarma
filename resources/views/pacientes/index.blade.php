@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2 text-purple-600"></i>
                Gerenciamento de Pacientes
            </h1>
            
            {{-- BARRA DE STATUS DO USUÁRIO --}}
            <div class="flex items-center space-x-4">
                @auth
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
                    @if(Auth::user()->email_verified_at)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Email verificado</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Email não verificado</span>
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
            {{-- FIM BARRA DE STATUS DO USUÁRIO --}}
        </div>
    </header>

    <main class="flex-1 p-6">
        <div class="mb-6">
            <a href="{{ route('pacientes.create') }}" class="btn-primary">
                <i class="fas fa-user-plus mr-2"></i>
                Novo Paciente
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Pacientes</h2>
                <p class="text-sm text-gray-600 mt-1">Gerencie todos os pacientes cadastrados no sistema</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header">
                                <i class="fas fa-user mr-2"></i>Nome
                            </th>
                            <th class="table-header">
                                <i class="fas fa-id-card mr-2"></i>CPF
                            </th>
                            <th class="table-header">
                                <i class="fas fa-phone mr-2"></i>Telefone
                            </th>
                            <th class="table-header">
                                <i class="fas fa-city mr-2"></i>Cidade
                            </th>
                            <th class="table-header text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pacientes as $paciente)
                        <tr class="table-row">
                            <td class="table-cell">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                    </div>
                                    {{-- LINK PARA FICHA/HISTÓRICO --}}
                                    <a href="{{ route('pacientes.show', $paciente->id) }}" class="font-medium text-gray-900 hover:text-purple-600 transition duration-200">
                                        {{ $paciente->nome }}
                                    </a>
                                </div>
                            </td>
                            <td class="table-cell font-mono text-sm">{{ $paciente->cpf }}</td>
                            <td class="table-cell">{{ $paciente->telefone }}</td>
                            <td class="table-cell">{{ $paciente->cidade }}</td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    
                                    {{-- Botão de Ver Ficha/Histórico --}}
                                    <a href="{{ route('pacientes.show', $paciente->id) }}" 
                                       class="text-purple-600 hover:text-purple-800 transition duration-200"
                                       title="Ver Ficha e Histórico">
                                        <i class="fas fa-file-medical"></i>
                                    </a>
                                    
                                    {{-- Botão de Edição --}}
                                    <a href="{{ route('pacientes.edit', $paciente->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200"
                                       title="Editar paciente">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    {{-- ⭐️ Botão de Exclusão (Chama SweetAlert) ⭐️ --}}
                                    <form action="{{ route('pacientes.destroy', $paciente->id) }}" 
                                             method="POST"
                                             class="inline delete-form-{{ $paciente->id }}">
                                        @csrf 
                                        @method('DELETE')
                                        
                                        <button type="button" 
                                                onclick="confirmDelete('{{ $paciente->id }}', '{{ $paciente->nome }}')" 
                                                class="text-red-600 hover:text-red-800 transition duration-200"
                                                title="Excluir paciente">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center py-8">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Nenhum paciente cadastrado</p>
                                    <p class="text-sm mt-1">Comece adicionando seu primeiro paciente</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pacientes->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Mostrando <span class="font-medium">{{ $pacientes->count() }}</span> paciente(s)
                    </p>
                    <div class="text-sm text-gray-500">
                        Atualizado em {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>
</div>

{{-- 🚀 SCRIPT DO SWEETALERT (CORRIGIDO) - Deve ser adicionado na View 🚀 --}}
@push('scripts')
<script>
/**
 * Exibe o Modal SweetAlert de confirmação antes de excluir.
 */
function confirmDelete(pacienteId, pacienteNome) {
    // 1. Verifica se o SweetAlert2 está carregado globalmente (depende do seu app.blade.php)
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 não está carregado. Verifique o app.blade.php.');
        return;
    }

    Swal.fire({
        title: 'Tem certeza?',
        html: `Você realmente deseja <strong>excluir permanentemente</strong> o paciente <strong>${pacienteNome}</strong>? Esta ação é <strong>irreversível</strong>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Vermelho para exclusão
        cancelButtonColor: '#6c757d', // Cinza para cancelar
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sim, Excluir!',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        // 2. Submete o formulário APENAS se o usuário confirmar
        if (result.isConfirmed) {
            // Encontra e submete o formulário correto usando a classe única
            const form = document.querySelector(`.delete-form-${pacienteId}`);
            if (form) {
                form.submit();
            } else {
                console.error(`Formulário de exclusão para ID ${pacienteId} não encontrado.`);
            }
        }
    })
}
</script>
@endpush

@endsection