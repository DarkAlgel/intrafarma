<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicamento;
use App\Models\Laboratorio; // Para o formulário
use App\Models\ClasseTerapeutica; // Para o formulário
use Illuminate\Validation\Rule;
use Exception;

class MedicamentoController extends Controller
{
    // Tipos ENUM baseados no Dicionário de Dados
    private $enums = [
        'tarjas' => ['sem_tarja', 'tarja_amarela', 'tarja_vermelha', 'tarja_preta'],
        'formas_retirada' => ['MIP', 'com_prescricao'],
        'formas_fisica' => ['solida', 'pastosa', 'liquida', 'gasosa'],
        'unidades_contagem' => ['comprimido', 'capsula', 'dragea', 'sache', 'ampola', 'frasco', 'caixa', 'ml', 'g', 'unidade', 'aerosol', 'xarope', 'solucao'],
    ];

    /**
     * Exibe a lista de medicamentos.
     */
    public function index()
    {
        $medicamentos = Medicamento::with(['laboratorio', 'classeTerapeutica'])
            ->orderBy('nome')
            ->paginate(15);

        return view('medicamentos.index', compact('medicamentos'));
    }

    /**
     * Exibe o formulário de criação de um novo medicamento, carregando as dependências.
     */
    public function create()
    {
        // ⭐️ Carrega dependências necessárias para o formulário ⭐️
        $laboratorios = Laboratorio::pluck('nome', 'id');
        $classes = ClasseTerapeutica::pluck('nome', 'id');
        
        return view('medicamentos.create', array_merge(
            compact('laboratorios', 'classes'),
            $this->enums
        ));
    }

    /**
     * Salva um novo medicamento no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Definição das regras de validação (usando os ENUMs)
        $rules = [
            'codigo' => 'required|string|unique:medicamentos,codigo',
            'nome' => 'required|string',
            'laboratorio_id' => 'required|exists:laboratorios,id',
            'classe_terapeutica_id' => 'required|exists:classes_terapeuticas,id',
            'tarja' => ['required', Rule::in($this->enums['tarjas'])],
            'forma_retirada' => ['required', Rule::in($this->enums['formas_retirada'])],
            'forma_fisica' => ['required', Rule::in($this->enums['formas_fisica'])],
            'apresentacao' => ['required', Rule::in($this->enums['unidades_contagem'])],
            'unidade_base' => ['required', Rule::in($this->enums['unidades_contagem'])],
            'dosagem_valor' => 'required|numeric|min:0',
            'dosagem_unidade' => 'required|string|max:10',
            'generico' => 'boolean',
            'limite_minimo' => 'nullable|numeric|min:0',
        ];

        $validatedData = $request->validate($rules);

        try {
            // Nota: O campo 'serial_por_classe' deve ser preenchido por trigger ou lógica customizada.
            Medicamento::create($validatedData);

            return redirect()->route('medicamentos.index')->with('success', 'Medicamento cadastrado com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Falha ao cadastrar. Detalhe: ' . $e->getMessage());
        }
    }
}