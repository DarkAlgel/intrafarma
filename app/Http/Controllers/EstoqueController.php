<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necessário para consultar a View do banco

class EstoqueController extends Controller
{
    
    public function index()
    {
        // 1. Buscando os dados da View 'vw_estoque_por_lote'
        $estoque = DB::table('vw_estoque_por_lote')
                     // Ordenamos para que os itens mais próximos do vencimento apareçam primeiro
                     ->orderBy('validade', 'asc') 
                     ->get(); // Executa a consulta e retorna todos os resultados
        
        // 2. Passando os dados para a View
        // 'estoque.index' referencia o caminho 'resources/views/estoque/index.blade.php'
        // 'estoques' será o nome da variável que usaremos dentro da View
        return view('estoque.index', [
            'estoques' => $estoque
        ]);
    }
}