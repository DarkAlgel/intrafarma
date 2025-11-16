<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PacientePortalController extends Controller
{
    public function medicamentos(Request $request)
    {
        $search = $request->query('search');
        $tarja = $request->query('tarja');
        $generico = $request->query('generico');
        $query = DB::table('vw_estoque_por_medicamento');
        if ($search) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('nome', 'ILIKE', $like)->orWhere('codigo', 'ILIKE', $like);
            });
        }
        if ($tarja) {
            $query->where('tarja', $tarja);
        }
        if ($generico === 'sim') {
            $query->where('generico', true);
        }
        if ($generico === 'nao') {
            $query->where('generico', false);
        }
        $medicamentos = $query->orderBy('nome')->get();
        return view('paciente.medicamentos', [
            'medicamentos' => $medicamentos,
            'search' => $search,
            'tarja' => $tarja,
            'generico' => $generico,
        ]);
    }

    public function historico()
    {
        $user = Auth::user();
        $pacienteId = DB::table('pacientes')->where('nome', $user->nome)->value('id');
        $historico = collect();
        if ($pacienteId) {
            $historico = DB::table('dispensacoes')
                ->join('lotes', 'lotes.id', '=', 'dispensacoes.lote_id')
                ->join('medicamentos', 'medicamentos.id', '=', 'lotes.medicamento_id')
                ->where('dispensacoes.paciente_id', $pacienteId)
                ->orderBy('data_dispensa', 'desc')
                ->select([
                    'dispensacoes.data_dispensa',
                    'dispensacoes.responsavel',
                    'dispensacoes.dosagem',
                    'dispensacoes.nome_comercial',
                    'dispensacoes.quantidade_informada',
                    'dispensacoes.unidade',
                    'dispensacoes.numero_receita',
                    'medicamentos.nome as medicamento',
                    'medicamentos.codigo as codigo',
                ])
                ->get();
        }
        return view('paciente.historico', [
            'historico' => $historico,
        ]);
    }

    public function configuracoes()
    {
        return view('paciente.configuracoes');
    }
}