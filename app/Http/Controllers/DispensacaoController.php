<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\Lote;
use App\Models\Entrada;
use App\Models\Dispensacao;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Importar Carbon se necessário para validação/data

class DispensacaoController extends Controller
{
    /**
     * Exibe a lista (historico) de todas as dispensações.
     * Mapeado para a rota dispensacoes.index.
     */
    public function index(Request $request)
    {
        // Usa Eager Loading para carregar o Paciente e o Medicamento (via Lote) de forma eficiente.
        // Assume que os Models Dispensacao e Lote têm as relações paciente() e medicamento()
        $dispensacoes = Dispensacao::with(['paciente', 'lote.medicamento'])
            ->orderBy('data_dispensa', 'desc')
            ->paginate(15); 

        // Retorna a view 'historico' dentro da pasta 'dispensacoes'
        return view('dispensacoes.historico', [
            'dispensacoes' => $dispensacoes,
        ]);
    }

    /**
     * Exibe o formulário para registrar uma nova dispensação.
     * Mapeado para a rota dispensacoes.create.
     */
    public function create()
    {
        $pacientes = Paciente::select('id', 'nome', 'cpf')->orderBy('nome')->get();

        // Busca lotes disponíveis e não bloqueados
        $lotes = DB::table('vw_estoque_por_lote')
            ->select('lote_id', 'medicamento', 'validade', 'quantidade_disponivel', 'unidade_base', 'forma_retirada')
            ->where('quantidade_disponivel', '>', 0)
            ->where('status', '!=', 'Bloquear dispensação')
            ->orderBy('medicamento')
            ->orderBy('validade')
            ->get();

        $unidades = ['comprimido','capsula','dragea','sache','ampola','frasco','caixa','ml','g','unidade','aerosol','xarope','solucao'];

        return view('dispensacoes.create', compact('pacientes', 'lotes', 'unidades'));
    }

    /**
     * Armazena uma nova dispensação no banco de dados.
     * Mapeado para a rota dispensacoes.store.
     */
    public function store(Request $request)
    {
        $unidades = ['comprimido','capsula','dragea','sache','ampola','frasco','caixa','ml','g','unidade','aerosol','xarope','solucao'];

        $data = $request->validate([
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'lote_id' => ['required', 'integer'],
            'quantidade_informada' => ['required', 'numeric', 'gt:0'],
            'unidade' => ['required', Rule::in($unidades)],
            'numero_receita' => ['nullable', 'string'],
        ]);

        $lote = Lote::with('medicamento')->findOrFail($data['lote_id']);
        $med = $lote->medicamento;

        // VALIDAÇÃO 1: Lote vencido
        if (strtotime($lote->validade) < strtotime(date('Y-m-d'))) {
            return back()->withErrors(['lote_id' => 'Lote vencido. Dispensação bloqueada.'])->withInput();
        }

        // VALIDAÇÃO 2: Receita obrigatória (se aplicável)
        if ($med && $med->forma_retirada === 'com_prescricao') {
            if (empty(trim($data['numero_receita'] ?? ''))) {
                return back()->withErrors(['numero_receita' => 'Número de receita obrigatório para medicamento com prescrição.'])->withInput();
            }
        }

        $unidadeBase = $med ? $med->unidade_base : null;
        if (!$unidadeBase) {
            return back()->withErrors(['lote_id' => 'Unidade base não definida para o medicamento do lote.'])->withInput();
        }

        // CÁLCULO DE QUANTIDADE BASE
        $quantBase = null;
        if ($data['unidade'] === $unidadeBase) {
            $quantBase = $data['quantidade_informada'];
        } else {
            // Busca o fator de conversão da última entrada para este lote/unidade
            $entrada = Entrada::where('lote_id', $data['lote_id'])
                ->where('unidade', $data['unidade'])
                ->orderByDesc('id')
                ->first();
            $fator = $entrada?->unidades_por_embalagem;
            if (!$fator) {
                return back()->withErrors(['unidade' => 'Não foi possível converter a unidade selecionada para a unidade base.'])->withInput();
            }
            $quantBase = $data['quantidade_informada'] * (float) $fator;
        }

        // VALIDAÇÃO 3: Saldo insuficiente
        $saldoEntrada = Entrada::where('lote_id', $data['lote_id'])->sum('quantidade_base');
        $saidaDisp = Dispensacao::where('lote_id', $data['lote_id'])->sum('quantidade_base');
        $saldoAtual = ($saldoEntrada ?? 0) - ($saidaDisp ?? 0);
        if ($quantBase > max($saldoAtual, 0)) {
            return back()->withErrors(['quantidade_informada' => 'Saldo insuficiente para o lote selecionado.'])->withInput();
        }

        // Criação da Dispensação
        $disp = Dispensacao::create([
            'data_dispensa' => now(),
            'responsavel' => Auth::user()?->name,
            'paciente_id' => $data['paciente_id'],
            'lote_id' => $data['lote_id'],
            'dosagem' => $med ? ($med->dosagem_valor.' '.$med->dosagem_unidade) : null,
            'nome_comercial' => $lote->nome_comercial,
            'quantidade_informada' => $data['quantidade_informada'],
            'quantidade_base' => $quantBase,
            'unidade' => $data['unidade'],
            'numero_receita' => $data['numero_receita'] ?? null,
        ]);

        // ⭐️ CORRIGIDO: Redireciona para o Histórico após o sucesso ⭐️
        return redirect()->route('dispensacoes.index')->with('success', 'Dispensação registrada com sucesso.');
    }
}