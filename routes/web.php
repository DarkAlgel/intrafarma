<?php

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
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\PermissionAdminController;
use App\Http\Controllers\DispensacaoController; 
use App\Http\Controllers\DashboardController; // ⭐️ NOVO: Importando DashboardController
use App\Models\Usuario;


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
    // ⭐️ CORRIGIDO: Conectando a rota do dashboard ao Controller para buscar dados reais ⭐️
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MÓDULO: PACIENTES
    Route::resource('pacientes', PacienteController::class);
    
    // MÓDULO: ESTOQUE (Visualização da Lista)
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');

    // Rota para Detalhes de Entradas por Lote
    Route::get('/estoque/{loteId}/entradas', [EntradaController::class, 'showEntradas'])
        ->name('estoque.showEntradas');
    
    // MÓDULO: ENTRADA (Nova Entrada de Lote)
    Route::get('/estoque/entrada/nova', [EntradaController::class, 'create'])->name('entradas.create');
    Route::post('/estoque/entrada', [EntradaController::class, 'store'])->name('entradas.store');
    
    // MÓDULO: DISPENSAÇÃO
    Route::get('/dispensacao/nova', [DispensacaoController::class, 'create'])->name('dispensacoes.create');
    Route::post('/dispensacao', [DispensacaoController::class, 'store'])->name('dispensacoes.store');
    
    // MÓDULO: FORNECEDORES
    Route::get('/fornecedores', [FornecedorController::class, 'index'])->name('fornecedores.index');
    Route::post('/fornecedores', [FornecedorController::class, 'store'])->name('fornecedores.store');
    
    // MÓDULO: CONFIGURAÇÕES
    Route::get('/configuracoes', [SettingsController::class, 'index'])->name('configuracoes.index');
    Route::get('/configuracoes/conta', [SettingsController::class, 'account'])->name('configuracoes.account');
    Route::get('/configuracoes/senha', [SettingsController::class, 'password'])->name('configuracoes.password');
    Route::post('/configuracoes/conta', [SettingsController::class, 'updateAccount'])
        ->middleware('perm:ver_minha_conta')->name('configuracoes.account.update');
    Route::post('/configuracoes/senha', [SettingsController::class, 'updatePassword'])
        ->middleware('perm:alterar_senha')->name('configuracoes.password.update');

    // Administração de Usuários (proteção por permissão)
    Route::get('/configuracoes/usuarios', [UserAdminController::class, 'index'])
        ->middleware('perm:gerenciar_usuarios')->name('usuarios.index');
    Route::post('/configuracoes/usuarios', [UserAdminController::class, 'store'])
        ->middleware('perm:gerenciar_usuarios')->name('usuarios.store');
    Route::put('/configuracoes/usuarios/{id}', [UserAdminController::class, 'update'])
        ->middleware('perm:gerenciar_usuarios')->name('usuarios.update');
    Route::delete('/configuracoes/usuarios/{id}', [UserAdminController::class, 'destroy'])
        ->middleware('perm:gerenciar_usuarios')->name('usuarios.destroy');

    // Administração de Permissões
    Route::get('/configuracoes/permissoes', [PermissionAdminController::class, 'index'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.index');
    Route::post('/configuracoes/permissoes/assign', [PermissionAdminController::class, 'assign'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.assign');
    Route::post('/configuracoes/permissoes/revoke', [PermissionAdminController::class, 'revoke'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.revoke');
    Route::post('/configuracoes/permissoes/roles', [PermissionAdminController::class, 'createRole'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.roles.create');
    Route::put('/configuracoes/permissoes/roles/{id}', [PermissionAdminController::class, 'updateRole'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.roles.update');
    Route::delete('/configuracoes/permissoes/roles/{id}', [PermissionAdminController::class, 'deleteRole'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.roles.delete');
    Route::get('/configuracoes/permissoes/export/csv', [PermissionAdminController::class, 'exportCsv'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.export.csv');
    Route::get('/configuracoes/permissoes/export/pdf', [PermissionAdminController::class, 'exportPdf'])
        ->middleware('perm:gerenciar_permissoes')->name('permissoes.export.pdf');
});

// Auxiliar para testes visuais em ambiente local
if (app()->environment('local')) {
    Route::get('/_test/login', function () {
        $user = Usuario::first();
        if (!$user) {
            $id = DB::table('usuarios')->insertGetId([
                'nome' => 'Dev',
                'email' => 'dev@example.com',
                'login' => 'dev',
                'senha_hash' => 'password',
                'ativo' => true,
                'datacadastro' => DB::raw('now()'),
            ]);
            $user = Usuario::find($id);
        }
        auth()->login($user);
        return redirect()->to('/fornecedores');
    });

    Route::get('/_test/seed-fornecedores', function () {
        $count = \App\Models\Fornecedor::count();
        if ($count < 12) {
            for ($i = $count; $i < 12; $i++) {
                \App\Models\Fornecedor::create([
                    'nome' => 'Fornecedor ' . ($i + 1),
                    'tipo' => $i % 2 === 0 ? 'doacao' : 'compra',
                    'contato' => '(11) 99999-000' . ($i % 10),
                ]);
            }
        }
        return response()->json(['seeded' => true, 'total' => \App\Models\Fornecedor::count()]);
    });
}


// Rotas de autenticação (sem alteração)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
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