<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\StorePacienteRequest;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return view('pacientes.index', compact('pacientes'));
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
