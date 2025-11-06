<?php

// Adicione esta linha no topo do seu arquivo de rotas
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\EstoqueController; 
use App\Http\Controllers\EntradaController;



Route::get('/', function () {
    // ... Lógica de teste de conexão com o banco ...
    $dbStatus = '';
    $dbInfo = '';

    try {
        $pdo = DB::connection()->getPdo();
        $dbStatus = 'Conectado com sucesso!';
        $dbInfo = DB::connection()->getPdo()->query('SELECT version()')->fetchColumn();
    } catch (\Exception $e) {
        $dbStatus = 'Falha na conexão!';
        $dbInfo = $e->getMessage();
    }

    return view('welcome', [
        'dbStatus' => $dbStatus,
        'dbInfo' => $dbInfo
    ]);
});

// Rotas de Pacientes e outras protegidas - acesso somente autenticado
Route::middleware(['auth'])->group(function () {
    
    // Rotas do Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // MÓDULO: PACIENTES
    Route::resource('pacientes', PacienteController::class);
    
    // MÓDULO: ESTOQUE (Visualização da Lista)
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');

    // 🟢 MÓDULO: ENTRADA (Nova Entrada de Lote) ⬅️ CORREÇÃO E INCLUSÃO
    // Rota para exibir o formulário de Nova Entrada
    Route::get('/estoque/entrada/nova', [EntradaController::class, 'create'])->name('entradas.create');
    // Rota para salvar os dados da Nova Entrada
    Route::post('/estoque/entrada', [EntradaController::class, 'store'])->name('entradas.store');
    
    // 🟢 MÓDULO: DISPENSAÇÃO (Previsão para a Próxima Funcionalidade)
    // Rota para exibir o formulário de Nova Dispensação (Saída)
    Route::get('/dispensacao/nova', [DispensacaoController::class, 'create'])->name('dispensacoes.create');
    // Rota para salvar os dados da Dispensação
    Route::post('/dispensacao', [DispensacaoController::class, 'store'])->name('dispensacoes.store');
    
    // ... Aqui você pode adicionar outras rotas protegidas (Medicamentos, Fornecedores, etc.) ...
});


// Rotas de autenticação (sem alteração)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// ... o restante das rotas de Auth ...
// Rotas de registro, verificação de email, recuperação de senha, etc.
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');