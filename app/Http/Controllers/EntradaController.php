<?php
// app/Http/Controllers/EntradaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicamento; 
use App\Models\Fornecedor; 
use App\Models\Lote; 
use App\Models\Entrada; 
use App\Models\Laboratorio; 
use App\Models\ClasseTerapeutica; 
use Illuminate\Support\Carbon; 
use Exception;
// Não incluímos o DB aqui, pois ele é mais usado no EstoqueController

class EntradaController extends Controller
{
    /**
     * Exibe o formulário para registrar uma nova entrada no estoque.
     */
    public function create()
    {
        // 1. Busca dados para os Dropdowns
        $medicamentos = Medicamento::where('ativo', true)->pluck('nome', 'id');
        $fornecedores = Fornecedor::pluck('nome', 'id');
        
        // 2. Tipos Enumerados do seu Dicionário de Dados
        $unidades = ['comprimido', 'capsula', 'dragea', 'sache', 'ampola', 'frasco', 'caixa', 'ml', 'g', 'unidade', 'aerosol', 'xarope', 'solucao'];
        $estados = ['novo', 'lacrado', 'aberto', 'avariado'];
        
        return view('entradas.create', [
            'medicamentos' => $medicamentos,
            'fornecedores' => $fornecedores,
            'unidades' => $unidades,
            'estados' => $estados,
        ]);
    }

    /**
     * Armazena uma nova entrada no banco de dados, incluindo a lógica de Lote.
     */
    public function store(Request $request)
    {
        // 1. Validação dos Dados
        $request->validate([
            'medicamento_id' => 'required|exists:medicamentos,id',
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'data_entrada' => 'required|date',
            
            'data_fabricacao' => 'required|date',
            'validade' => 'required|date|after:today', 
            'nome_comercial' => 'nullable|string|max:255',
            
            'numero_lote_fornecedor' => 'required|string|max:255',
            'quantidade_informada' => 'required|numeric|gt:0',
            'unidade' => 'required|string|max:255', 
            'unidades_por_embalagem' => 'nullable|numeric|gt:0',
            'estado' => 'nullable|string|max:255', 
            'observacao' => 'nullable|string',
        ]);
        
        try {
            $validadeMes = Carbon::parse($request->validade)->startOfMonth()->toDateString();
            
            $lote = Lote::firstOrCreate(
                [
                    'medicamento_id' => $request->medicamento_id,
                    'validade' => $validadeMes, 
                ],
                [
                    'data_fabricacao' => $request->data_fabricacao,
                    'nome_comercial' => $request->nome_comercial,
                    'ativo' => true,
                    'observacao' => $request->observacao,
                ]
            );

            Entrada::create([
                'data_entrada' => $request->data_entrada,
                'fornecedor_id' => $request->fornecedor_id,
                'lote_id' => $lote->id, 
                'numero_lote_fornecedor' => $request->numero_lote_fornecedor,
                'quantidade_informada' => $request->quantidade_informada,
                'unidade' => $request->unidade,
                'unidades_por_embalagem' => $request->unidades_por_embalagem,
                'estado' => $request->estado,
                'observacao' => $request->observacao,
            ]);

            // Redirecionamento com mensagem de sucesso (Para o Toast!)
            return redirect()->route('estoque.index')->with('success', 'Entrada de lote registrada com sucesso!');
            
        } catch (Exception $e) {
            // Redirecionamento com mensagem de erro (Para o Toast!)
            return back()->withInput()->with('error', 'Falha ao registrar entrada. Detalhe: ' . $e->getMessage()); 
        }
    }
    
    // ----------------------------------------------------------------------
    // 🚀 NOVO MÉTODO: Exibe Detalhes de Entradas de um Lote Específico
    // ----------------------------------------------------------------------

    /**
     * Busca e exibe todos os registros da tabela 'Entrada' relacionados a um Lote.
     * @param int $loteId O ID do Lote (Primary Key da tabela 'lotes').
     */
    public function showEntradas($loteId)
    {
        // Garante que o Lote exista e carrega o nome do Medicamento (Eager Loading)
        $lote = Lote::with('medicamento')->findOrFail($loteId);

        // Busca todas as transações de entrada para este lote
        $entradas = Entrada::where('lote_id', $loteId)
                            ->with('fornecedor') // Carrega os dados do fornecedor
                            ->orderBy('data_entrada', 'desc')
                            ->get();

        return view('entradas.show_lote_entradas', [
            'lote' => $lote,
            'entradas' => $entradas,
        ]);
    }

    /**
     * MÉTODO TEMPORÁRIO PARA POPULAR DADOS DE TESTE (omitido para brevidade)
     */

    public static function popularDadosTeste()
    {
        try {
            // 1. Dependências: Laboratório e Classe Terapêutica
            $lab = Laboratorio::firstOrCreate(['nome' => 'EuroPharma Teste']);
            $classe = ClasseTerapeutica::firstOrCreate(
                ['codigo_classe' => 100],
                ['nome' => 'Analgésicos e Antitérmicos']
            );
            
            // 2. Fornecedor]
            Fornecedor::firstOrCreate(
                ['nome' => 'Distribuidora Teste Rápido'],
                ['tipo' => 'compra', 'contato' => 'teste@distribuidora.com']
            );

            // 3. Medicamento
            Medicamento::firstOrCreate(
                ['codigo' => 'PAR0500'],
                [
                    'nome' => 'Paracetamol 500mg Comprimido',
                    'laboratorio_id' => $lab->id, 
                    'classe_terapeutica_id' => $classe->id, 
                    'tarja' => 'sem_tarja', 
                    'forma_retirada' => 'MIP', 
                    'forma_fisica' => 'solida', 
                    'apresentacao' => 'caixa', 
                    'unidade_base' => 'comprimido', 
                    'dosagem_valor' => 500.000,
                    'dosagem_unidade' => 'mg',
                    'generico' => false,
                    'limite_minimo' => 100,
                    'ativo' => true
                ]
            );
            
            return "Dados de teste inseridos/atualizados com sucesso! (Verifique os dropdowns)";

        } catch (Exception $e) {
            return "Erro ao inserir dados de teste: " . $e->getMessage();
        }
    }
}