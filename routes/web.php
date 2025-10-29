<?php

// Adicione esta linha no topo do seu arquivo de rotas
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
