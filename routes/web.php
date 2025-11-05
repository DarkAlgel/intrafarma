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

Route::get('/', function () {

    $dbStatus = '';
    $dbInfo = '';

    try {
        // Tenta pegar a conexão PDO e rodar uma query de versão
        $pdo = DB::connection()->getPdo();
        
        $dbStatus = 'Conectado com sucesso!';
        
        // Pega a versão do PostgreSQL para confirmar
        $dbInfo = DB::connection()->getPdo()->query('SELECT version()')->fetchColumn();

    } catch (\Exception $e) {
        // Se falhar, captura a mensagem de erro
        $dbStatus = 'Falha na conexão!';
        $dbInfo = $e->getMessage();
    }

    // Envia as variáveis $dbStatus e $dbInfo para a view
    return view('welcome', [
        'dbStatus' => $dbStatus,
        'dbInfo' => $dbInfo
    ]);
});

//Rota Paciente
//Permitir acesso apenas com login
//Route::resource('/pacientes', PacienteController::class)->middleware('auth');
//Permitir acesso sem login
Route::resource('pacientes', PacienteController::class);


// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas de registro
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rotas de verificação de email
Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// Rotas de recuperação de senha
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');


// Rotas protegidas (requerem apenas autenticação)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
