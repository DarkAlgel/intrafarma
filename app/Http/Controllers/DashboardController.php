<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Medicamento;
use App\Models\Dispensacao;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Contagens para os Cards
        $totalMedicamentos = Medicamento::where('ativo', true)->count();
        $dispensacoesHoje = DB::table('dispensacoes')
            ->whereDate('data_dispensa', Carbon::today())
            ->count();
        $alertasBaixo = DB::table('vw_alerta_estoque_baixo')->count(); // Assumindo a View existe

        // Itens Críticos/Próximos de Vencer (Próximos 30 dias ou Vencidos/Bloqueados)
        $dataLimite = Carbon::now()->addDays(30)->toDateString();
        
        $proximosAVencer = DB::table('vw_estoque_por_lote')
            ->where(function ($query) use ($dataLimite) {
                // Filtra por status definidos na View (Próximo ou Bloqueado)
                $query->where('status', 'ILIKE', '%PRÓXIMO DE VENCER%')
                      ->orWhere('status', 'ILIKE', '%BLOQUEAR DISPENSAÇÃO%')
                      
                      // Inclui itens que vencem nos próximos 30 dias (o orWhere é aninhado corretamente)
                      ->orWhere('validade', '<=', $dataLimite); 
            })
            ->orderBy('validade', 'asc')
            ->limit(10)
            ->get();


        return view('dashboard', [
            'totalMedicamentos' => $totalMedicamentos,
            'dispensacoesHoje' => $dispensacoesHoje,
            'alertasBaixo' => $alertasBaixo,
            'proximosAVencer' => $proximosAVencer,
        ]);
    }
}