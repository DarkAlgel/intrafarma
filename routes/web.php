<?php

// Adicione esta linha no topo do seu arquivo de rotas
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas de registro
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
