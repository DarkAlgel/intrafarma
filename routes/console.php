<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('verificar:sistema', function () {
    $schemaPath = base_path('database/scripts/schema_farmacia.sql');
    if (!File::exists($schemaPath)) {
        $this->error('Arquivo de esquema não encontrado: '.$schemaPath);
        return 1;
    }
    $sql = File::get($schemaPath);

    $enumMatches = [];
    preg_match_all('/CREATE TYPE public\."([^"]+)" AS ENUM/s', $sql, $enumMatches);
    $tiposEnum = $enumMatches[1] ?? [];

    $tableMatches = [];
    preg_match_all('/CREATE TABLE\s+"?([a-zA-Z_]+)"?\s*\((.*?)\);/s', $sql, $tableMatches, PREG_SET_ORDER);
    $tabelasEsperadas = [];
    foreach ($tableMatches as $m) {
        $tabela = $m[1];
        $bloco = $m[2];
        $linhas = preg_split('/\r?\n/', $bloco);
        $cols = [];
        foreach ($linhas as $linha) {
            $l = trim($linha);
            if ($l === '' || str_starts_with($l, 'CONSTRAINT') || str_starts_with($l, 'CREATE INDEX') || str_starts_with($l, '--')) {
                continue;
            }
            $l = rtrim($l, ',');
            $parts = preg_split('/\s+/', $l);
            if (count($parts) < 2) continue;
            $col = trim($parts[0], '"');
            $tipo = $parts[1];
            if (isset($parts[2]) && !in_array(strtoupper($parts[2]), ['DEFAULT','NOT','NULL','GENERATED'])) {
                $tipo .= ' '.$parts[2];
            }
            $cols[$col] = $tipo;
        }
        $tabelasEsperadas[$tabela] = $cols;
    }

    $relatorio = [];

    try {
        $schema = 'public';
        $tabelasDb = DB::select("select table_name from information_schema.tables where table_schema=? order by table_name", [$schema]);
        $existentes = array_map(fn($r) => $r->table_name, $tabelasDb);
        $faltando = array_values(array_diff(array_keys($tabelasEsperadas), $existentes));
        $extras = array_values(array_diff($existentes, array_keys($tabelasEsperadas)));
        if ($faltando) $relatorio[] = 'Tabelas faltando: '.implode(', ', $faltando);
        if ($extras) $relatorio[] = 'Tabelas extras no banco: '.implode(', ', $extras);

        foreach ($tabelasEsperadas as $tabela => $colsEsperados) {
            $colsDb = DB::select("select column_name, data_type, udt_name from information_schema.columns where table_schema=? and table_name=?", [$schema, $tabela]);
            $mapDb = [];
            foreach ($colsDb as $c) {
                $tipo = $c->data_type === 'USER-DEFINED' ? $c->udt_name : $c->data_type;
                $mapDb[$c->column_name] = $tipo;
            }
            $faltamCols = array_values(array_diff(array_keys($colsEsperados), array_keys($mapDb)));
            $extrasCols = array_values(array_diff(array_keys($mapDb), array_keys($colsEsperados)));
            $incompat = [];
            foreach ($colsEsperados as $col => $tipoEsperadoRaw) {
                $tipoEsperado = strtolower(str_contains($tipoEsperadoRaw, 'public."') ? trim(str_replace(['public."','"'], '', $tipoEsperadoRaw)) : $tipoEsperadoRaw);
                if (isset($mapDb[$col])) {
                    $tipoDb = strtolower($mapDb[$col]);
                    if ($tipoEsperado !== $tipoDb) {
                        $incompat[] = $col.'='.$tipoDb.'<>'.$tipoEsperado;
                    }
                }
            }
            if ($faltamCols) $relatorio[] = 'Colunas faltando em "'.$tabela.'": '.implode(', ', $faltamCols);
            if ($extrasCols) $relatorio[] = 'Colunas extras em "'.$tabela.'": '.implode(', ', $extrasCols);
            if ($incompat) $relatorio[] = 'Tipos divergentes em "'.$tabela.'": '.implode('; ', $incompat);
        }

        $tiposDb = DB::select("select t.typname as nome from pg_type t join pg_namespace n on n.oid=t.typnamespace where n.nspname=? and t.typtype='e'", [$schema]);
        $tiposNomes = array_map(fn($r) => $r->nome, $tiposDb);
        $enumsFaltando = array_values(array_diff($tiposEnum, $tiposNomes));
        if ($enumsFaltando) $relatorio[] = 'Tipos ENUM faltando: '.implode(', ', $enumsFaltando);

        $pksDb = DB::select("select tc.table_name, tc.constraint_name from information_schema.table_constraints tc where tc.table_schema=? and tc.constraint_type='PRIMARY KEY'", [$schema]);
        $pksPorTabela = [];
        foreach ($pksDb as $r) { $pksPorTabela[$r->table_name] = $r->constraint_name; }
        foreach ($tabelasEsperadas as $tabela => $cols) {
            if (!isset($pksPorTabela[$tabela])) $relatorio[] = 'Chave primária ausente em "'.$tabela.'"';
        }

        $fksDb = DB::select("select tc.table_name, tc.constraint_name from information_schema.table_constraints tc where tc.table_schema=? and tc.constraint_type='FOREIGN KEY'", [$schema]);
        $fksPorTabela = [];
        foreach ($fksDb as $r) { $fksPorTabela[$r->table_name] = true; }
        foreach (['medicamentos','lotes','dispensacoes','entradas','papeis_permissoes','usuarios_papeis','usuarios_permissoes'] as $tb) {
            if (isset($tabelasEsperadas[$tb]) && !isset($fksPorTabela[$tb])) $relatorio[] = 'Chaves estrangeiras ausentes em "'.$tb.'"';
        }

        $viewsDb = DB::select("select table_name from information_schema.views where table_schema=?", [$schema]);
        $views = array_map(fn($r) => $r->table_name, $viewsDb);
        $viewsEsperadas = [];
        $viewMatches = [];
        preg_match_all('/CREATE OR REPLACE VIEW\s+([a-zA-Z_]+)\s+AS/s', $sql, $viewMatches);
        $viewsEsperadas = $viewMatches[1] ?? [];
        $viewsFaltando = array_values(array_diff($viewsEsperadas, $views));
        if ($viewsFaltando) $relatorio[] = 'Views faltando: '.implode(', ', $viewsFaltando);

        $triggersDb = DB::select("select t.tgname as nome, c.relname as tabela from pg_trigger t join pg_class c on c.oid=t.tgrelid join pg_namespace n on n.oid=c.relnamespace where n.nspname=? and not t.tgisinternal", [$schema]);
        $triggers = array_map(fn($r) => $r->nome, $triggersDb);
        $trigMatches = [];
        preg_match_all('/create trigger\s+([a-zA-Z_]+)\s+/i', $sql, $trigMatches);
        $triggersEsperados = $trigMatches[1] ?? [];
        $triggersFaltando = array_values(array_diff($triggersEsperados, $triggers));
        if ($triggersFaltando) $relatorio[] = 'Triggers faltando: '.implode(', ', $triggersFaltando);

        $inicio = microtime(true);
        DB::select('select * from vw_estoque_por_medicamento limit 100');
        $tempoMedic = (microtime(true) - $inicio) * 1000.0;
        $inicio = microtime(true);
        DB::select('select * from vw_estoque_por_lote limit 100');
        $tempoLote = (microtime(true) - $inicio) * 1000.0;

        $this->info('Tempo de resposta vw_estoque_por_medicamento: '.number_format($tempoMedic, 2).' ms');
        $this->info('Tempo de resposta vw_estoque_por_lote: '.number_format($tempoLote, 2).' ms');

        $indices = DB::select("select indexname, tablename from pg_indexes where schemaname=?", [$schema]);
        $idxPorTabela = [];
        foreach ($indices as $i) { $idxPorTabela[$i->tablename][] = $i->indexname; }
        $sugestoesIdx = [];
        $checar = [
            ['tabela'=>'dispensacoes','col'=>'lote_id'],
            ['tabela'=>'entradas','col'=>'lote_id'],
            ['tabela'=>'lotes','col'=>'medicamento_id'],
            ['tabela'=>'medicamentos','col'=>'classe_terapeutica_id']
        ];
        foreach ($checar as $c) {
            $tem = false;
            $lista = $idxPorTabela[$c['tabela']] ?? [];
            foreach ($lista as $nome) { if (str_contains($nome, $c['col'])) { $tem = true; break; } }
            if (!$tem) $sugestoesIdx[] = 'Criar índice em '.$c['tabela'].'('.$c['col'].')';
        }
        foreach ($sugestoesIdx as $s) { $relatorio[] = 'Sugestão de índice: '.$s; }

        DB::beginTransaction();
        $classeId = DB::table('classes_terapeuticas')->insertGetId(['codigo_classe'=>9999,'nome'=>'Teste Classe']);
        $labId = DB::table('laboratorios')->insertGetId(['nome'=>'Teste Lab']);
        $fornId = DB::table('fornecedores')->insertGetId(['nome'=>'Fornecedor Teste','tipo'=>'doacao','contato'=>null]);
        $medId = DB::table('medicamentos')->insertGetId([
            'codigo'=>'TESTE9999','nome'=>'Medicamento Teste','laboratorio_id'=>$labId,'classe_terapeutica_id'=>$classeId,
            'tarja'=>'sem_tarja','forma_retirada'=>'MIP','forma_fisica'=>'solida','apresentacao'=>'unidade','unidade_base'=>'unidade',
            'dosagem_valor'=>1,'dosagem_unidade'=>'mg','generico'=>false,'limite_minimo'=>0
        ]);
        $loteId = DB::table('lotes')->insertGetId([
            'medicamento_id'=>$medId,'data_fabricacao'=>DB::raw('CURRENT_DATE'),'validade'=>DB::raw('CURRENT_DATE + interval \'+90 days\''),
            'nome_comercial'=>null,'ativo'=>true,'observacao'=>null
        ]);
        DB::table('entradas')->insert([
            'fornecedor_id'=>$fornId,'lote_id'=>$loteId,'numero_lote_fornecedor'=>'L9999','quantidade_informada'=>10,'quantidade_base'=>10,
            'unidade'=>'unidade','unidades_por_embalagem'=>null,'estado'=>null,'observacao'=>null
        ]);
        $pacId = DB::table('pacientes')->insertGetId(['nome'=>'Paciente Teste','cpf'=>'11144477735','telefone'=>null,'cidade'=>null]);
        DB::table('dispensacoes')->insert([
            'responsavel'=>null,'paciente_id'=>$pacId,'lote_id'=>$loteId,'dosagem'=>null,'nome_comercial'=>null,
            'quantidade_informada'=>2,'quantidade_base'=>2,'unidade'=>'unidade','numero_receita'=>null
        ]);
        $erroSaldo = null;
        try {
            DB::table('dispensacoes')->insert([
                'responsavel'=>null,'paciente_id'=>$pacId,'lote_id'=>$loteId,'dosagem'=>null,'nome_comercial'=>null,
                'quantidade_informada'=>1000,'quantidade_base'=>1000,'unidade'=>'unidade','numero_receita'=>null
            ]);
        } catch (\Throwable $e) { $erroSaldo = $e->getMessage(); }
        $erroCpf = null;
        try { DB::table('pacientes')->insert(['nome'=>'CPF Inválido','cpf'=>'123','telefone'=>null,'cidade'=>null]); }
        catch (\Throwable $e) { $erroCpf = $e->getMessage(); }
        DB::rollBack();

        if ($erroSaldo === null) $relatorio[] = 'Trigger de saldo/validade em dispensações não bloqueou excesso.';
        if ($erroCpf === null) $relatorio[] = 'Constraint de CPF inválido não funcionou.';

        foreach ($relatorio as $item) { $this->error($item); }
        if (!$relatorio) $this->info('Estrutura e integridade estão consistentes com o esquema.');
        return 0;
    } catch (\Throwable $e) {
        $this->error('Falha na verificação: '.$e->getMessage());
        return 1;
    }
})->purpose('Verificar estrutura, integridade e desempenho do sistema');
