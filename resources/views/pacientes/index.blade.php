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
            <a href="#" class="nav-link">
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
                    <i class="fas fa-users mr-2 text-purple-600"></i>
                    Gerenciamento de Pacientes
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
            <!-- Action Bar -->
            <div class="mb-6">
                <a href="{{ route('pacientes.create') }}" class="btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Novo Paciente
                </a>
            </div>

            <!-- Patients Table -->
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
                                        
                                        <form action="{{ route('pacientes.destroy', $paciente->id) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Deseja realmente excluir este paciente?')"
                                              class="inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
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
</div>
@endsection
