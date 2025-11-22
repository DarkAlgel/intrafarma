<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicamentoRequest;
use App\Models\Medicamento;
use App\Models\Laboratorio;
use App\Models\ClasseTerapeutica;
use Illuminate\Database\QueryException;

class MedicamentoController extends Controller
{
    private function getFormOptions(): array
    {
        return [
            'laboratorios' => Laboratorio::orderBy('nome')->get(),
            'classes' => ClasseTerapeutica::orderBy('nome')->get(),
            'tarjaTipos' => ['sem_tarja', 'tarja_amarela', 'tarja_vermelha', 'tarja_preta'],
            'formaRetiradaTipos' => ['MIP', 'com_prescricao'],
            'formaFisicaTipos' => ['solida', 'pastosa', 'liquida', 'gasosa'],
            'unidadeContagemTipos' => [
                'comprimido', 'capsula', 'dragea', 'sache', 'ampola', 'frasco', 
                'caixa', 'ml', 'g', 'unidade', 'aerosol', 'xarope', 'solucao'
            ],
            'dosagemUnidadeSugestoes' => ['mg', 'ml', 'g', 'mcg', 'UI']
        ];
    }

    public function index()
    {
        $medicamentos = Medicamento::with(['laboratorio', 'classeTerapeutica'])
            ->orderBy('nome')
            ->paginate(15);
            
        return view('medicamentos.index', compact('medicamentos'));
    }

    public function create()
    {
        return view('medicamentos.create', $this->getFormOptions());
    }

    public function store(StoreMedicamentoRequest $request)
    {
        try {
            $data = $request->validated();
            $data['generico'] = $request->boolean('generico');
            
            Medicamento::create($data);

            return redirect()->route('medicamentos.index')->with('success', 'Medicamento criado!');
        } catch (QueryException $e) {
            return $this->handleError($e);
        }
    }

    public function edit($id)
    {
        $medicamento = Medicamento::findOrFail($id);

        return view('medicamentos.edit', array_merge(
            ['medicamento' => $medicamento],
            $this->getFormOptions()
        ));
    }

    public function update(StoreMedicamentoRequest $request, $id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);

            $data = $request->validated();
            $data['generico'] = $request->boolean('generico');

            $medicamento->update($data);

            return redirect()->route('medicamentos.index')->with('success', 'Medicamento atualizado!');
        } catch (QueryException $e) {
            return $this->handleError($e);
        }
    }

    public function destroy($id)
    {
        try {
            Medicamento::findOrFail($id)->delete();
            return redirect()->route('medicamentos.index')->with('success', 'Medicamento excluído!');
        } catch (QueryException $e) {
            return redirect()->route('medicamentos.index')->with('error', 'Não é possível excluir este item.');
        }
    }

    private function handleError($e)
    {
        if (str_contains($e->getMessage(), 'medicamentos_codigo_key')) {
            return back()->withErrors(['codigo' => 'Código já existente!'])->withInput();
        }

        return back()->with('error', 'Erro no banco: ' . $e->getMessage())->withInput();
    }
}
