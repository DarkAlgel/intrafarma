<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intrafarma - Sistema de Gestão de Farmácia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero {
            background-color: #28a745;
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
        }
        .feature-box {
            padding: 30px;
            border-radius: 10px;
            /* box-shadow sutil para efeito 3D */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            height: 100%; /* Essencial para herdar a altura da coluna */
            transition: transform 0.3s;
        }
        /* Efeito visual ao passar o mouse */
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15); /* Sombra mais forte no hover */
        }
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: rgba(40, 167, 69, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .icon-box i {
            font-size: 30px;
            color: #28a745;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">INTRAFARMA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn nav-link">Sair</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">INTRAFARMA</h1>
            <p class="lead">Sistema de Gestão de Farmácia</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4 gap-3">Entrar no Sistema</a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Criar Conta</a>
            </div>
        </div>
    </section>

    <!-- Seção de Funcionalidades (Recursos) -->
    <div class="container mb-5">
        <div class="row mb-4">
            
            <!-- 1º Bloco: Gestão de Medicamentos -->
            <div class="col-md-4 mb-4">
                <!-- Adicionado h-100 para garantir a altura total -->
                <div class="feature-box bg-white h-100"> 
                    <div class="icon-box">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <h3>Gestão de Medicamentos</h3>
                    <p>Controle completo do seu estoque de medicamentos com alertas de validade e níveis baixos.</p>
                </div>
            </div>

            <!-- 2º Bloco: Cadastro de Pacientes (Link Corrigido e Estilizado) -->
            <div class="col-md-4 mb-4">
                <!-- Adicionado h-100 para altura total e text-body para remover a cor azul do link -->
                <a href="{{ route('pacientes.index') }}" class="text-decoration-none d-block h-100 text-body"> 
                    <!-- Adicionado h-100 para garantir que o box ocupe todo o link -->
                    <div class="feature-box bg-white h-100">
                        <div class="icon-box">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3>Cadastro de Pacientes</h3>
                        <p>Mantenha um registro organizado de todos os pacientes e seu histórico de dispensações.</p>
                    </div>
                </a>
            </div>
            
            <!-- 3º Bloco: Controle de Dispensações -->
            <div class="col-md-4 mb-4">
                <!-- Adicionado h-100 para garantir a altura total -->
                <div class="feature-box bg-white h-100">
                    <div class="icon-box">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <h3>Controle de Dispensações</h3>
                    <p>Registre todas as dispensações com controle de receitas e prescrições médicas.</p>
                </div>
            </div>
        </div> 
    </div>

    <!-- Bloco de Status da Conexão (Movido para dentro de um container para melhor alinhamento) -->
    <div class="container mb-5">
        <div class="bg-white p-4 rounded-3 shadow-sm">
            <h2 class="text-center mb-4">Status da Conexão com o Banco de Dados</h2>
            @if(isset($dbStatus) && $dbStatus == 'Conectado com sucesso!')
                <div class="alert alert-success">
                    <p class="fw-bold mb-1">Status: {{ $dbStatus }}</p>
                    <p class="small mb-0">Info: <code>{{ $dbInfo }}</code></p>
                </div>
            @else
                <div class="alert alert-danger">
                    <p class="fw-bold mb-1">Status: {{ $dbStatus ?? 'Erro desconhecido' }}</p>
                    <p class="small mb-0">Erro: <code>{{ $dbInfo }}</code></p>
                </div>
            @endif
        </div>
    </div>
    <!-- Fim do container -->

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>© 2023 Intrafarma - Sistema de Gestão de Farmácia</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>