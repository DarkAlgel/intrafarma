<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicamentoRequest;
use App\Models\Medicamento;
use App\Models\Laboratorio;
use App\Models\ClasseTerapeutica;
use Illuminate\Database\QueryException;

class MedicamentoController extends Controller
{
    /**
     * Retorna os dados necessários para os formulários (dropdowns de ENUMs).
     */
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
            ]
        ];
    }

    public function index()
    {
        // Eager loading para evitar N+1 queries na view
        
        // =================================================================
        // A CORREÇÃO ESTÁ AQUI
        // Você estava usando ->get(), que busca TODOS os registros de uma vez.
        // Para a paginação (->links()) funcionar, você DEVE usar ->paginate().
        // =================================================================
        $medicamentos = Medicamento::with(['laboratorio', 'classeTerapeutica'])
            ->orderBy('nome')
            ->paginate(15); // <-- TROCADO DE 'get()' PARA 'paginate(15)'
            
        return view('medicamentos.index', compact('medicamentos'));
    }

    public function create()
    {
        $options = $this->getFormOptions();
        
        return view('medicamentos.create', [
            'laboratorios' => $options['laboratorios'],
            'classes' => $options['classes'],
            'tarjaTipos' => $options['tarjaTipos'],
            'formaRetiradaTipos' => $options['formaRetiradaTipos'],
            'formaFisicaTipos' => $options['formaFisicaTipos'],
            'unidadeContagemTipos' => $options['unidadeContagemTipos'],
        ]);
    }

    public function store(StoreMedicamentoRequest $request)
    {
        try {
            Medicamento::create($request->validated());

            return redirect()
                ->route('medicamentos.index')
                ->with('success', 'Medicamento cadastrado com sucesso!');
                
        } catch (QueryException $e) {
            // Erro de violação de constraint UNIQUE (código duplicado)
            if (strpos($e->getMessage(), 'medicamentos_codigo_key') !== false) {
                return back()
                    ->withErrors(['codigo' => 'Este código já está em uso!'])
                    ->withInput();
            }

            // Para outros erros de banco
            return back()
                ->with('error', 'Erro ao salvar o medicamento: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $medicamento = Medicamento::findOrFail($id);
        $options = $this->getFormOptions();
        
        return view('medicamentos.edit', [
            'medicamento' => $medicamento,
            'laboratorios' => $options['laboratorios'],
            'classes' => $options['classes'],
            'tarjaTipos' => $options['tarjaTipos'],
            'formaRetiradaTipos' => $options['formaRetiradaTipos'],
            'formaFisicaTipos' => $options['formaFisicaTipos'],
            'unidadeContagemTipos' => $options['unidadeContagemTipos'],
        ]);
    }

    public function update(StoreMedicamentoRequest $request, $id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            $medicamento->update($request->validated());

            return redirect()
                ->route('medicamentos.index')
                ->with('success', 'Medicamento atualizado com sucesso!');

        } catch (QueryException $e) {
            if (strpos($e->getMessage(), 'medicamentos_codigo_key') !== false) {
                return back()
                    ->withErrors(['codigo' => 'Este código já está em uso!'])
                    ->withInput();
            }

            return back()
                ->with('error', 'Erro ao atualizar o medicamento: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $medicamento = Medicamento::findOrFail($id);
            $medicamento->delete();

            return redirect()
                ->route('medicamentos.index')
                ->with('success', 'Medicamento removido com sucesso!');

        } catch (QueryException $e) {
            // Erro de violação de FK (ON DELETE RESTRICT)
            // ex: lotes_medicamento_id_fkey
            if (strpos($e->getMessage(), 'lotes_medicamento_id_fkey') !== false) {
                return redirect()
                    ->route('medicamentos.index')
                    ->with('error', 'Não é possível excluir: este medicamento possui lotes cadastrados.');
            }

            return redirect()
                ->route('medicamentos.index')
                ->with('error', 'Erro ao remover o medicamento: ' . $e->getMessage());
        }
    }
}