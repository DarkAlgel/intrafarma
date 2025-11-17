<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Dispensacao;
use App\Models\Lote;
use App\Http\Requests\StorePacienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necessário para buscar cidades distintas

class PacienteController extends Controller
{
    /**
     * Exibe a lista de pacientes, aplicando filtros de busca, cidade e ordenação.
     */
    public function index(Request $request)
    {
        $query = Paciente::query();
        
        $searchTerm = $request->input('search');
        $filterCidade = $request->input('cidade');
        $sortBy = $request->input('sort', 'nome'); // Ordena por nome por padrão
        $sortDirection = $request->input('dir', 'asc'); // Direção ascendente por padrão

        // 1. Aplica a Busca (por Nome, CPF ou Telefone)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'ILIKE', '%' . $searchTerm . '%') // ILIKE para PostgreSQL
                  ->orWhere('cpf', 'ILIKE', '%' . $searchTerm . '%')
                  ->orWhere('telefone', 'ILIKE', '%' . $searchTerm . '%');
            });
        }

        // 2. Aplica o Filtro de Cidade
        if ($filterCidade) {
            $query->where('cidade', $filterCidade);
        }
        
        // 3. Aplica a Ordenação
        $query->orderBy($sortBy, $sortDirection);

        // 4. Executa a query
        $pacientes = $query->get();
        
        // 5. Busca todas as cidades para o dropdown de filtro (disponível na view)
        $cidades = Paciente::select('cidade')
            ->distinct()
            ->pluck('cidade')
            ->filter() // Remove valores nulos ou vazios
            ->sort();

        // Passamos os pacientes e a lista de cidades para a view
        return view('pacientes.index', compact('pacientes', 'cidades'));
    }

    /**
     * Exibe o histórico detalhado (ficha) de um paciente.
     * Mapeado para pacientes.show.
     */
    public function show(string $id)
    {
        $paciente = Paciente::findOrFail($id);

        $movimentacoes = Dispensacao::where('paciente_id', $paciente->id)
            ->with(['lote.medicamento'])
            ->orderBy('data_dispensa', 'desc')
            ->get();

        return view('pacientes.show', [
            'paciente' => $paciente,
            'movimentacoes' => $movimentacoes,
        ]);
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(StorePacienteRequest $request)
    {
        try {
            Paciente::create($request->validated());

            return redirect()
                ->route('pacientes.index')
                ->with('success', 'Paciente cadastrado com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), 'ck_pacientes_cpf_valido') !== false) {
                return back()
                    ->withErrors(['cpf' => 'O CPF informado é inválido!'])
                    ->withInput();
            }

            throw $e;
        }
    }

    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(StorePacienteRequest $request, $id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            $paciente->update($request->validated());

            return redirect()
                ->route('pacientes.index')
                ->with('success', 'Paciente atualizado com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), 'ck_pacientes_cpf_valido') !== false) {
                return back()
                    ->withErrors(['cpf' => 'O CPF informado é inválido!'])
                    ->withInput();
            }

            throw $e;
        }
    }

    public function destroy($id)
    {
        Paciente::findOrFail($id)->delete();

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente removido com sucesso!');
    }
}