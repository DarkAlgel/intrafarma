<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intrafarma - Sistema de Gestão de Farmácia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gray-900 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-xl font-bold">
                    <i class="fas fa-pills mr-2"></i>INTRAFARMA
                </div>
                <div class="hidden md:flex space-x-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-purple-300 transition duration-300">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-purple-300 transition duration-300">
                                <i class="fas fa-sign-out-alt mr-1"></i> Sair
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-purple-300 transition duration-300">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="hover:text-purple-300 transition duration-300">
                            <i class="fas fa-user-plus mr-1"></i> Registrar
                        </a>
                    @endauth
                </div>
                <button class="md:hidden" onclick="toggleMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <div id="mobileMenu" class="hidden md:hidden mt-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="block py-2 hover:text-purple-300">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="py-2">
                        @csrf
                        <button type="submit" class="hover:text-purple-300">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-2 hover:text-purple-300">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="block py-2 hover:text-purple-300">
                        <i class="fas fa-user-plus mr-1"></i> Registrar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-purple-600 to-blue-600 text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-4">INTRAFARMA</h1>
            <p class="text-xl mb-8">Sistema de Gestão de Farmácia</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i> Entrar no Sistema
                </a>
                <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i> Criar Conta
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <div class="container mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Gestão de Medicamentos -->
            <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-pills text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center mb-4 text-gray-800">Gestão de Medicamentos</h3>
                <p class="text-gray-600 text-center">Controle completo do seu estoque de medicamentos com alertas de validade e níveis baixos.</p>
            </div>

            <!-- Cadastro de Pacientes -->
            @auth
            <a href="{{ route('pacientes.index') }}" class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300 transform hover:-translate-y-2 block">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center mb-4 text-gray-800">Cadastro de Pacientes</h3>
                <p class="text-gray-600 text-center">Mantenha um registro organizado de todos os pacientes e seu histórico de dispensações.</p>
            </a>
            @else
            <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center mb-2 text-gray-800">Cadastro de Pacientes</h3>
                <p class="text-gray-600 text-center">Mantenha um registro organizado de todos os pacientes e seu histórico de dispensações.</p>
                
            </div>
            @endauth

            <!-- Controle de Dispensações -->
            <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-clipboard-list text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center mb-4 text-gray-800">Controle de Dispensações</h3>
                <p class="text-gray-600 text-center">Registre todas as dispensações com controle de receitas e prescrições médicas.</p>
            </div>
        </div>
    </div>

    <!-- Database Status Section -->
    <div class="container mx-auto px-6 pb-16">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Status da Conexão com o Banco de Dados</h2>
            @if(isset($dbStatus) && $dbStatus == 'Conectado com sucesso!')
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <p class="font-bold mb-1">Status: {{ $dbStatus }}</p>
                    <p class="text-sm">Info: <code class="bg-green-200 px-2 py-1 rounded">{{ $dbInfo }}</code></p>
                </div>
            @else
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <p class="font-bold mb-1">Status: {{ $dbStatus ?? 'Erro desconhecido' }}</p>
                    <p class="text-sm">Erro: <code class="bg-red-200 px-2 py-1 rounded">{{ $dbInfo }}</code></p>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 Intrafarma - Sistema de Gestão de Farmácia</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>