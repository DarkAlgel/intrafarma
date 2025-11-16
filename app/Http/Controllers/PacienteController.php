<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Dispensacao; // ⭐️ NOVO
use App\Models\Lote; // ⭐️ NOVO
use App\Http\Requests\StorePacienteRequest;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Exibe o histórico detalhado (ficha) de um paciente.
     * Mapeado para pacientes.show.
     */
    public function show(string $id)
    {
        // 1. Busca o Paciente
        $paciente = Paciente::findOrFail($id);

        // 2. Busca todas as dispensações (movimentações de saída) deste paciente.
        // Usa Eager Loading: Dispensacao -> Lote -> Medicamento
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