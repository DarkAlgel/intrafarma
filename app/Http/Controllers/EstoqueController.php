<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Carbon;

class EstoqueController extends Controller
{
    /**
     * Exibe o controle de estoque por lote, com funcionalidade de pesquisa, filtros e ordenação.
     */
    public function index(Request $request) 
    {
        // 1. Captura os parâmetros de Filtro e Pesquisa
        $searchTerm = $request->query('search'); 
        $statusFilter = $request->query('status_filter');
        $validadeMin = $request->query('validade_min');
        
        // ⭐️ 2. Captura os parâmetros de Ordenação
        $sortColumn = $request->query('sort', 'validade'); // Padrão: validade
        $sortDirection = $request->query('direction', 'asc'); // Padrão: asc

        // 3. Inicia a query
        $query = DB::table('vw_estoque_por_lote');

        // 4. Lógica da Pesquisa
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $searchWildcard = '%' . $searchTerm . '%'; 
                $q->where('medicamento', 'ILIKE', $searchWildcard)
                  ->orWhere('codigo', 'ILIKE', $searchWildcard);
            });
        }
        
        // 5. Lógica do Filtro por Status - ENFORÇANDO FILTRO EXATO
        if ($statusFilter) {
            if ($statusFilter === 'BLOQUEAR DISPENSAÇÃO') {
                $query->where(function ($q) { // Usa a função para agrupar as condições OR
                    $q->where('status', 'ILIKE', '%BLOQUEAR DISPENSAÇÃO%') 
                      ->orWhere('status', 'ILIKE', '%VENCIDO%'); 
                });
            } elseif ($statusFilter === 'PRÓXIMO DE VENCER') {
                $query->where('status', 'ILIKE', '%PRÓXIMO DE VENCER%');
            } else {
                $query->where('status', 'ILIKE', '%' . $statusFilter . '%');
            }
        }
        
        // 6. Lógica do Filtro por Validade Mínima
        if ($validadeMin) {
            try {
                $query->where('validade', '>', $validadeMin);
            } catch (\Exception $e) {}
        }
        
        // ⭐️ 7. Aplica a Ordenação Dinâmica
        $allowedSorts = ['medicamento', 'validade', 'quantidade_disponivel'];
        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            // Ordenação padrão se os parâmetros forem inválidos
            $query->orderBy('validade', 'asc');
        }
        
        // 8. Executa a consulta
        $estoque = $query->get(); 
        
        // 9. Passa os dados para a View, incluindo os parâmetros de ordenação
        return view('estoque.index', [
            'estoques' => $estoque,
            'searchTerm' => $searchTerm,
            'statusFilter' => $statusFilter,
            'validadeMin' => $validadeMin,
            'statusOptions' => ['BLOQUEAR DISPENSAÇÃO', 'PRÓXIMO DE VENCER', 'OK'],
            // ⭐️ Parâmetros de Ordenação
            'currentSort' => $sortColumn,
            'currentDirection' => $sortDirection,
        ]);
    }
}