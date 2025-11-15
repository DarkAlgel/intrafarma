<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;

class FornecedorController extends Controller
{
    // A lista de tipos de fornecedor (de acordo com o dicionário de dados)
    private $fornecedorTipos = ['doacao', 'compra', 'parceria', 'outros'];

    public function index(Request $request)
    {
        $sort = in_array($request->get('sort'), ['nome', 'tipo', 'contato']) ? $request->get('sort') : 'nome';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        // Lógica de ordenação (já inclusa)
        $fornecedores = Fornecedor::orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('fornecedores.index', [
            'fornecedores' => $fornecedores,
            'sort' => $sort,
            'direction' => $direction,
            'fornecedorTipos' => $this->fornecedorTipos, // Passamos os tipos para a View
        ]);
    }

    /**
     * Armazena um novo fornecedor no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Validação dos Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            // O tipo deve ser um dos valores definidos no ENUM 'fornecedor_tipo'
            'tipo' => 'required|in:' . implode(',', $this->fornecedorTipos), 
            'contato' => 'nullable|string|max:255',
        ]);

        try {
            // 2. Criação do Fornecedor
            Fornecedor::create($request->only(['nome', 'tipo', 'contato']));

            // 3. Redirecionamento com sucesso
            return redirect()->route('fornecedores.index')->with('success', 'Fornecedor cadastrado com sucesso!');
        } catch (\Exception $e) {
            // 4. Retorna erro
            return back()->withInput()->with('error', 'Falha ao cadastrar o fornecedor. Detalhe: ' . $e->getMessage());
        }
    }
}