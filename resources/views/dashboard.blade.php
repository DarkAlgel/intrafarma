@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar w-64 bg-purple-800 text-white">
        <div class="p-4 border-b border-purple-700">
            <h1 class="text-xl font-bold flex items-center">
                <i class="fas fa-pills mr-2"></i>
                INTRAFARMA
            </h1>
        </div>
        
        <nav class="mt-6">
            <a href="#" class="nav-link active">
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
            <a href="pacientes" class="nav-link">
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
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                
                <div class="flex items-center space-x-4">
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
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Medicamentos</h3>
                            <p class="text-3xl font-bold text-blue-600">120</p>
                            <p class="text-sm text-gray-500">Total cadastrados</p>
                        </div>
                        <div class="text-blue-500 text-4xl">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Dispensações</h3>
                            <p class="text-3xl font-bold text-green-600">45</p>
                            <p class="text-sm text-gray-500">Realizadas hoje</p>
                        </div>
                        <div class="text-green-500 text-4xl">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Alertas</h3>
                            <p class="text-3xl font-bold text-yellow-600">8</p>
                            <p class="text-sm text-gray-500">Estoque baixo</p>
                        </div>
                        <div class="text-yellow-500 text-4xl">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Medicamentos a vencer nos próximos 30 dias</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-header">Código</th>
                                <th class="table-header">Nome</th>
                                <th class="table-header">Lote</th>
                                <th class="table-header">Validade</th>
                                <th class="table-header">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="table-cell">MED001</td>
                                <td class="table-cell">Paracetamol 500mg</td>
                                <td class="table-cell">L123456</td>
                                <td class="table-cell">15/06/2023</td>
                                <td class="table-cell">120</td>
                            </tr>
                            <tr>
                                <td class="table-cell">MED002</td>
                                <td class="table-cell">Dipirona 500mg</td>
                                <td class="table-cell">L789012</td>
                                <td class="table-cell">20/06/2023</td>
                                <td class="table-cell">85</td>
                            </tr>
                            <tr>
                                <td class="table-cell">MED003</td>
                                <td class="table-cell">Amoxicilina 500mg</td>
                                <td class="table-cell">L345678</td>
                                <td class="table-cell">25/06/2023</td>
                                <td class="table-cell">45</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection