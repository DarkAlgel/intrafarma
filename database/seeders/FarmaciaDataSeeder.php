<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FarmaciaDataSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('laboratorios') || !Schema::hasTable('classes_terapeuticas') || !Schema::hasTable('fornecedores') || !Schema::hasTable('pacientes') || !Schema::hasTable('medicamentos') || !Schema::hasTable('lotes') || !Schema::hasTable('entradas') || !Schema::hasTable('dispensacoes')) {
            return;
        }

        $laboratorios = ['EuroPharma', 'BioSaúde', 'Farmacorp', 'VitaLab', 'SaúdeMax'];
        foreach ($laboratorios as $nome) {
            DB::table('laboratorios')->updateOrInsert(['nome' => $nome], []);
        }
        $labs = DB::table('laboratorios')->pluck('id', 'nome');

        $classes = [
            ['codigo_classe' => 100, 'nome' => 'Analgésicos'],
            ['codigo_classe' => 110, 'nome' => 'Antitérmicos'],
            ['codigo_classe' => 120, 'nome' => 'Antibióticos'],
            ['codigo_classe' => 130, 'nome' => 'Anti-inflamatórios'],
            ['codigo_classe' => 140, 'nome' => 'Antialérgicos'],
        ];
        foreach ($classes as $c) {
            DB::table('classes_terapeuticas')->updateOrInsert(['codigo_classe' => $c['codigo_classe']], ['nome' => $c['nome']]);
        }
        $cls = DB::table('classes_terapeuticas')->pluck('id', 'codigo_classe');

        $fornecedores = [
            ['nome' => 'Distribuidora Norte', 'tipo' => 'compra', 'contato' => '(11) 90000-0001'],
            ['nome' => 'Doações Solidárias', 'tipo' => 'doacao', 'contato' => '(11) 90000-0002'],
            ['nome' => 'Supply Farma', 'tipo' => 'compra', 'contato' => '(11) 90000-0003'],
            ['nome' => 'Saúde Viva', 'tipo' => 'compra', 'contato' => '(11) 90000-0004'],
            ['nome' => 'Bem Cuidar', 'tipo' => 'doacao', 'contato' => '(11) 90000-0005'],
        ];
        foreach ($fornecedores as $f) {
            DB::table('fornecedores')->updateOrInsert(['nome' => $f['nome']], ['tipo' => $f['tipo'], 'contato' => $f['contato']]);
        }
        $forns = DB::table('fornecedores')->pluck('id', 'nome');

        $cpfBases = [123456789, 987654321, 111222333, 222333444, 333444555];
        $pacientes = [
            ['nome' => 'Alice Santos', 'telefone' => '(11) 91111-1111', 'cidade' => 'São Paulo'],
            ['nome' => 'Bruno Lima', 'telefone' => '(11) 92222-2222', 'cidade' => 'Campinas'],
            ['nome' => 'Carla Souza', 'telefone' => '(11) 93333-3333', 'cidade' => 'Santos'],
            ['nome' => 'Diego Costa', 'telefone' => '(11) 94444-4444', 'cidade' => 'Osasco'],
            ['nome' => 'Eva Martins', 'telefone' => '(11) 95555-5555', 'cidade' => 'Guarulhos'],
        ];
        $cpfs = [];
        foreach ($cpfBases as $base) {
            $digits = str_split((string)$base);
            while (count($digits) < 9) { array_unshift($digits, 0); }
            $sum1 = 0;
            for ($i = 0; $i < 9; $i++) { $sum1 += $digits[$i] * (10 - $i); }
            $dv1 = ($sum1 * 10) % 11; $dv1 = $dv1 === 10 ? 0 : $dv1;
            $sum2 = 0;
            for ($i = 0; $i < 9; $i++) { $sum2 += $digits[$i] * (11 - $i); }
            $sum2 += $dv1 * 2;
            $dv2 = ($sum2 * 10) % 11; $dv2 = $dv2 === 10 ? 0 : $dv2;
            $cpfs[] = implode('', $digits) . $dv1 . $dv2;
        }
        for ($i = 0; $i < 5; $i++) {
            DB::table('pacientes')->updateOrInsert(['cpf' => $cpfs[$i]], ['nome' => $pacientes[$i]['nome'], 'telefone' => $pacientes[$i]['telefone'], 'cidade' => $pacientes[$i]['cidade']]);
        }
        $pacs = DB::table('pacientes')->pluck('id', 'cpf');

        $meds = [
            ['codigo' => 'PAR0500', 'nome' => 'Paracetamol 500mg', 'laboratorio' => 'EuroPharma', 'classe' => 100, 'tarja' => 'sem_tarja', 'forma_retirada' => 'MIP', 'forma_fisica' => 'solida', 'apresentacao' => 'caixa', 'unidade_base' => 'comprimido', 'dosagem_valor' => 500.000, 'dosagem_unidade' => 'mg', 'generico' => false, 'limite_minimo' => 100, 'ativo' => true],
            ['codigo' => 'IBU0200', 'nome' => 'Ibuprofeno 200mg', 'laboratorio' => 'BioSaúde', 'classe' => 130, 'tarja' => 'sem_tarja', 'forma_retirada' => 'MIP', 'forma_fisica' => 'solida', 'apresentacao' => 'caixa', 'unidade_base' => 'comprimido', 'dosagem_valor' => 200.000, 'dosagem_unidade' => 'mg', 'generico' => true, 'limite_minimo' => 50, 'ativo' => true],
            ['codigo' => 'AMX0500', 'nome' => 'Amoxicilina 500mg', 'laboratorio' => 'Farmacorp', 'classe' => 120, 'tarja' => 'tarja_vermelha', 'forma_retirada' => 'com_prescricao', 'forma_fisica' => 'solida', 'apresentacao' => 'caixa', 'unidade_base' => 'capsula', 'dosagem_valor' => 500.000, 'dosagem_unidade' => 'mg', 'generico' => true, 'limite_minimo' => 30, 'ativo' => true],
            ['codigo' => 'LOR0010', 'nome' => 'Lorazepam 1mg', 'laboratorio' => 'VitaLab', 'classe' => 140, 'tarja' => 'tarja_preta', 'forma_retirada' => 'com_prescricao', 'forma_fisica' => 'solida', 'apresentacao' => 'caixa', 'unidade_base' => 'comprimido', 'dosagem_valor' => 1.000, 'dosagem_unidade' => 'mg', 'generico' => false, 'limite_minimo' => 20, 'ativo' => true],
            ['codigo' => 'DIP0010', 'nome' => 'Dipirona 1g', 'laboratorio' => 'SaúdeMax', 'classe' => 110, 'tarja' => 'sem_tarja', 'forma_retirada' => 'MIP', 'forma_fisica' => 'liquida', 'apresentacao' => 'frasco', 'unidade_base' => 'ml', 'dosagem_valor' => 1000.000, 'dosagem_unidade' => 'mg', 'generico' => true, 'limite_minimo' => 80, 'ativo' => true],
        ];
        foreach ($meds as $m) {
            $labId = $labs[$m['laboratorio']] ?? null;
            $classeId = $cls[$m['classe']] ?? null;
            DB::table('medicamentos')->updateOrInsert(
                ['codigo' => $m['codigo']],
                [
                    'nome' => $m['nome'],
                    'laboratorio_id' => $labId,
                    'classe_terapeutica_id' => $classeId,
                    'tarja' => $m['tarja'],
                    'forma_retirada' => $m['forma_retirada'],
                    'forma_fisica' => $m['forma_fisica'],
                    'apresentacao' => $m['apresentacao'],
                    'unidade_base' => $m['unidade_base'],
                    'dosagem_valor' => $m['dosagem_valor'],
                    'dosagem_unidade' => $m['dosagem_unidade'],
                    'generico' => $m['generico'],
                    'limite_minimo' => $m['limite_minimo'],
                    'ativo' => $m['ativo'],
                ]
            );
        }
        $medsMap = DB::table('medicamentos')->pluck('id', 'codigo');

        $lotesData = [
            ['codigo' => 'PAR0500', 'validade' => '2026-01-01', 'data_fabricacao' => '2024-01-15', 'nome_comercial' => 'Paracetamol 500mg EuroPharma'],
            ['codigo' => 'IBU0200', 'validade' => '2026-02-01', 'data_fabricacao' => '2024-02-15', 'nome_comercial' => 'Ibuprofeno 200mg BioSaúde'],
            ['codigo' => 'AMX0500', 'validade' => '2026-03-01', 'data_fabricacao' => '2024-03-10', 'nome_comercial' => 'Amoxicilina 500mg Farmacorp'],
            ['codigo' => 'LOR0010', 'validade' => '2026-04-01', 'data_fabricacao' => '2024-04-20', 'nome_comercial' => 'Lorazepam 1mg VitaLab'],
            ['codigo' => 'DIP0010', 'validade' => '2026-05-01', 'data_fabricacao' => '2024-05-12', 'nome_comercial' => 'Dipirona 1g SaúdeMax'],
        ];
        $loteIds = [];
        foreach ($lotesData as $ld) {
            $mid = $medsMap[$ld['codigo']] ?? null;
            if ($mid) {
                $mes = date('Y-m-01', strtotime($ld['validade']));
                $existingId = DB::table('lotes')
                    ->where('medicamento_id', $mid)
                    ->where('validade_mes', $mes)
                    ->value('id');
                if ($existingId) {
                    DB::table('lotes')->where('id', $existingId)->update([
                        'data_fabricacao' => $ld['data_fabricacao'],
                        'validade' => $ld['validade'],
                        'nome_comercial' => $ld['nome_comercial'],
                        'ativo' => true,
                        'observacao' => null,
                    ]);
                    $loteIds[$ld['codigo']] = $existingId;
                } else {
                    $loteId = DB::table('lotes')->insertGetId([
                        'medicamento_id' => $mid,
                        'data_fabricacao' => $ld['data_fabricacao'],
                        'validade' => $ld['validade'],
                        'nome_comercial' => $ld['nome_comercial'],
                        'ativo' => true,
                        'observacao' => null,
                    ]);
                    $loteIds[$ld['codigo']] = $loteId;
                }
            }
        }

        $entradas = [
            ['codigo' => 'PAR0500', 'fornecedor' => 'Distribuidora Norte', 'data' => '2025-01-05', 'numero' => 'DN-001', 'quant' => 1000, 'unidade' => 'comprimido', 'emb' => 20, 'estado' => 'novo'],
            ['codigo' => 'IBU0200', 'fornecedor' => 'Supply Farma', 'data' => '2025-02-06', 'numero' => 'SF-002', 'quant' => 800, 'unidade' => 'comprimido', 'emb' => 20, 'estado' => 'novo'],
            ['codigo' => 'AMX0500', 'fornecedor' => 'Saúde Viva', 'data' => '2025-03-07', 'numero' => 'SV-003', 'quant' => 500, 'unidade' => 'capsula', 'emb' => 10, 'estado' => 'lacrado'],
            ['codigo' => 'LOR0010', 'fornecedor' => 'Doações Solidárias', 'data' => '2025-04-08', 'numero' => 'DS-004', 'quant' => 200, 'unidade' => 'comprimido', 'emb' => 10, 'estado' => 'lacrado'],
            ['codigo' => 'DIP0010', 'fornecedor' => 'Bem Cuidar', 'data' => '2025-05-09', 'numero' => 'BC-005', 'quant' => 600, 'unidade' => 'ml', 'emb' => 1, 'estado' => 'novo'],
        ];
        foreach ($entradas as $e) {
            $fid = $forns[$e['fornecedor']] ?? null;
            $lid = $loteIds[$e['codigo']] ?? null;
            if ($fid && $lid) {
                DB::table('entradas')->updateOrInsert(
                    ['lote_id' => $lid, 'numero_lote_fornecedor' => $e['numero']],
                    [
                        'data_entrada' => $e['data'],
                        'fornecedor_id' => $fid,
                        'quantidade_informada' => $e['quant'],
                        'quantidade_base' => $e['quant'],
                        'unidade' => $e['unidade'],
                        'unidades_por_embalagem' => $e['emb'],
                        'estado' => $e['estado'],
                        'observacao' => null,
                    ]
                );
            }
        }

        $disp = [
            ['cpf' => '11111111111', 'codigo' => 'PAR0500', 'data' => '2025-06-01', 'resp' => 'Farmacêutico A', 'dosagem' => '500mg', 'nome_comercial' => 'Paracetamol 500mg EuroPharma', 'quant' => 20, 'unidade' => 'comprimido', 'receita' => 'R-1001'],
            ['cpf' => '22222222222', 'codigo' => 'IBU0200', 'data' => '2025-06-02', 'resp' => 'Farmacêutico B', 'dosagem' => '200mg', 'nome_comercial' => 'Ibuprofeno 200mg BioSaúde', 'quant' => 10, 'unidade' => 'comprimido', 'receita' => 'R-1002'],
            ['cpf' => '33333333333', 'codigo' => 'AMX0500', 'data' => '2025-06-03', 'resp' => 'Farmacêutico C', 'dosagem' => '500mg', 'nome_comercial' => 'Amoxicilina 500mg Farmacorp', 'quant' => 30, 'unidade' => 'capsula', 'receita' => 'R-1003'],
            ['cpf' => '44444444444', 'codigo' => 'LOR0010', 'data' => '2025-06-04', 'resp' => 'Farmacêutico D', 'dosagem' => '1mg', 'nome_comercial' => 'Lorazepam 1mg VitaLab', 'quant' => 10, 'unidade' => 'comprimido', 'receita' => 'R-1004'],
            ['cpf' => '55555555555', 'codigo' => 'DIP0010', 'data' => '2025-06-05', 'resp' => 'Farmacêutico E', 'dosagem' => '1g', 'nome_comercial' => 'Dipirona 1g SaúdeMax', 'quant' => 250, 'unidade' => 'ml', 'receita' => 'R-1005'],
        ];
        foreach ($disp as $d) {
            $pid = $pacs[$d['cpf']] ?? null;
            $lid = $loteIds[$d['codigo']] ?? null;
            if ($pid && $lid) {
                DB::table('dispensacoes')->insert([
                    'data_dispensa' => $d['data'],
                    'responsavel' => $d['resp'],
                    'paciente_id' => $pid,
                    'lote_id' => $lid,
                    'dosagem' => $d['dosagem'],
                    'nome_comercial' => $d['nome_comercial'],
                    'quantidade_informada' => $d['quant'],
                    'quantidade_base' => $d['quant'],
                    'unidade' => $d['unidade'],
                    'numero_receita' => $d['receita'],
                ]);
            }
        }
    }
}