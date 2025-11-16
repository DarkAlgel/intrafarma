@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-users mr-2 text-purple-600"></i>
                    Gerenciamento de Pacientes
                </h1>
                
                <div class="flex items-center space-x-4">
                    @auth
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
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

        <main class="flex-1 p-6">
            <div class="mb-6">
                <a href="{{ route('pacientes.create') }}" class="btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Novo Paciente
                </a>
            </div>

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
                                        <span class="font-medium text-gray-900">{{ $paciente->nome }}</span>
                                    </div>
                                </td>
                                <td class="table-cell font-mono text-sm">{{ $paciente->cpf }}</td>
                                <td class="table-cell">{{ $paciente->telefone }}</td>
                                <td class="table-cell">{{ $paciente->cidade }}</td>
                                <td class="table-cell text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('pacientes.edit', $paciente->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 transition duration-200"
                                           title="Editar paciente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- 🚀 FORMULÁRIO MODIFICADO PARA SWEETALERT 🚀 --}}
                                        <form action="{{ route('pacientes.destroy', $paciente->id) }}" 
                                              method="POST"
                                              class="inline delete-form-{{ $paciente->id }}"> {{-- Classe única para o JS --}}
                                            @csrf 
                                            @method('DELETE')
                                            
                                            {{-- MUDANÇA: type="button" e chama a função JS --}}
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

{{-- 🚀 SCRIPT DO SWEETALERT - Adicionado no final da página 🚀 --}}
@push('scripts')
<script>
/**
 * Exibe o Modal SweetAlert de confirmação antes de excluir.
 */
function confirmDelete(pacienteId, pacienteNome) {
    // Swal.fire é a função do SweetAlert2
    Swal.fire({
        title: 'Tem certeza?',
        html: `Você realmente deseja <strong>excluir permanentemente</strong> o paciente <strong>${pacienteNome}</strong>? Esta ação é <strong>irreversível<\strong>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Vermelho para exclusão
        cancelButtonColor: '#6c757d', // Cinza para cancelar
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sim, Excluir!',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        // Se o usuário clicar no botão de confirmação
        if (result.isConfirmed) {
            // Submete o formulário correspondente ao ID
            document.querySelector(`.delete-form-${pacienteId}`).submit();
        }
    })
}
</script>
@endpush

@endsection