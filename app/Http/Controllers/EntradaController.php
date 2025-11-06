<?php
// app/Http/Controllers/EntradaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicamento; 
use App\Models\Fornecedor; 
use App\Models\Lote; 
use App\Models\Entrada; 
use App\Models\Laboratorio; // Para o método de teste
use App\Models\ClasseTerapeutica; // Para o método de teste
use Illuminate\Support\Carbon; 
use Exception;

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
            
            // Campos de Lote (agora obrigatórios e validados)
            'data_fabricacao' => 'required|date',
            'validade' => 'required|date|after:today', 
            'nome_comercial' => 'nullable|string|max:255',
            
            'numero_lote_fornecedor' => 'required|string|max:255',
            'quantidade_informada' => 'required|numeric|gt:0',
            // A validação de ENUM deve ser feita garantindo que a string exista na lista
            'unidade' => 'required|string|max:255', 
            'unidades_por_embalagem' => 'nullable|numeric|gt:0',
            'estado' => 'nullable|string|max:255', 
            'observacao' => 'nullable|string',
        ]);
        
        // 2. Lógica de Busca/Criação do Lote (Respeita a unicidade do DB)
        
        try {
            // Usa o mês inicial da validade para o agrupamento do Lote, respeitando a regra validade_mes
            $validadeMes = Carbon::parse($request->validade)->startOfMonth()->toDateString();
            
            // Tenta encontrar um Lote existente ou cria um novo
            $lote = Lote::firstOrCreate(
                [
                    'medicamento_id' => $request->medicamento_id,
                    'validade' => $validadeMes, // Busca pelo mês de validade agrupado
                ],
                [
                    'data_fabricacao' => $request->data_fabricacao,
                    'nome_comercial' => $request->nome_comercial,
                    'ativo' => true,
                    'observacao' => $request->observacao,
                ]
            );

            // 3. Registro da Entrada
            Entrada::create([
                'data_entrada' => $request->data_entrada,
                'fornecedor_id' => $request->fornecedor_id,
                'lote_id' => $lote->id, // ID do Lote recém-criado/encontrado
                'numero_lote_fornecedor' => $request->numero_lote_fornecedor,
                'quantidade_informada' => $request->quantidade_informada,
                'unidade' => $request->unidade,
                'unidades_por_embalagem' => $request->unidades_por_embalagem,
                'estado' => $request->estado,
                'observacao' => $request->observacao,
            ]);

            return redirect()->route('estoque.index')->with('success', 'Entrada de lote registrada com sucesso!');
            
        } catch (Exception $e) {
            // Em caso de erro de DB (como duplicidade no numero_lote_fornecedor), retorna com erro
            return back()->withInput()->withErrors(['erro_db' => 'Falha ao registrar entrada. Detalhes: ' . $e->getMessage()]);
        }
    }
    
    /**
     * MÉTODO TEMPORÁRIO PARA POPULAR DADOS DE TESTE (Acionamento via Tinker).
     * Este método insere um Laboratório, uma Classe Terapêutica, um Fornecedor e um Medicamento de teste.
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

            // 2. Fornecedor
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