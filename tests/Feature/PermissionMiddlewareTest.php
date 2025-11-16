<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('usuarios', function ($t) {
            $t->increments('id');
            $t->string('nome');
            $t->string('email')->unique();
            $t->string('login');
            $t->string('senha_hash');
            $t->boolean('ativo')->default(true);
        });
        Schema::create('papeis', function ($t) { $t->increments('id'); $t->string('nome')->unique(); });
        Schema::create('permissoes', function ($t) { $t->increments('id'); $t->string('codigo')->unique(); $t->string('nome'); });
        Schema::create('papeis_permissoes', function ($t) { $t->increments('id'); $t->integer('papel_id'); $t->integer('permissao_id'); });
        Schema::create('usuarios_papeis', function ($t) { $t->increments('id'); $t->integer('usuario_id'); $t->integer('papel_id'); });
        Schema::create('usuarios_permissoes', function ($t) { $t->increments('id'); $t->integer('usuario_id'); $t->integer('permissao_id'); });
        Schema::create('pacientes', function ($t) { $t->increments('id'); $t->string('nome'); $t->string('cpf')->unique(); $t->string('telefone')->nullable(); $t->string('cidade')->nullable(); });
        Schema::create('medicamentos', function ($t) { $t->increments('id'); $t->string('nome'); $t->string('codigo')->unique(); $t->string('tarja')->nullable(); $t->boolean('generico')->default(false); });
        Schema::create('lotes', function ($t) { $t->increments('id'); $t->integer('medicamento_id'); });
        Schema::create('dispensacoes', function ($t) { $t->increments('id'); $t->date('data_dispensa')->nullable(); $t->string('responsavel')->nullable(); $t->integer('paciente_id')->nullable(); $t->integer('lote_id')->nullable(); $t->string('dosagem')->nullable(); $t->string('nome_comercial')->nullable(); $t->decimal('quantidade_informada', 12, 3)->nullable(); $t->string('unidade')->nullable(); $t->string('numero_receita')->nullable(); });
        Schema::create('vw_estoque_por_medicamento', function ($t) { $t->increments('id'); $t->integer('medicamento_id')->nullable(); $t->string('nome')->nullable(); $t->string('codigo')->nullable(); $t->integer('quantidade_disponivel')->nullable(); $t->string('tarja')->nullable(); $t->boolean('generico')->default(false); });
        Schema::create('vw_alerta_estoque_baixo', function ($t) { $t->increments('id'); });
        Schema::create('vw_estoque_por_lote', function ($t) { $t->increments('id'); $t->integer('lote_id')->nullable(); $t->integer('medicamento_id')->nullable(); $t->string('medicamento')->nullable(); $t->string('codigo')->nullable(); $t->date('validade')->nullable(); $t->integer('quantidade_disponivel')->nullable(); $t->integer('dias_para_vencimento')->nullable(); $t->string('unidade_base')->nullable(); $t->string('forma_retirada')->nullable(); $t->string('status')->nullable(); });

        DB::table('vw_estoque_por_lote')->insert([
            'lote_id' => 1,
            'medicamento' => 'Paracetamol 500mg',
            'codigo' => 'PAR0500',
            'validade' => date('Y-m-d', strtotime('+6 months')),
            'quantidade_disponivel' => 100,
            'unidade_base' => 'comprimido',
            'dias_para_vencimento' => 180,
            'forma_retirada' => 'MIP',
            'status' => 'OK',
        ]);
        DB::table('vw_alerta_estoque_baixo')->insert(['id' => 1]);

        DB::table('papeis')->insert([['nome' => 'Administradores'], ['nome' => 'Pacientes']]);
        $perms = [
            ['codigo' => 'ver_estoque', 'nome' => 'Ver Estoque'],
            ['codigo' => 'ver_dispensacoes', 'nome' => 'Ver Dispensações'],
            ['codigo' => 'ver_minha_conta', 'nome' => 'Ver Minha Conta'],
            ['codigo' => 'alterar_senha', 'nome' => 'Alterar Senha'],
            ['codigo' => 'paciente_ver_medicamentos', 'nome' => 'Paciente: Ver Medicamentos'],
            ['codigo' => 'paciente_ver_historico', 'nome' => 'Paciente: Ver Histórico'],
        ];
        foreach ($perms as $p) { DB::table('permissoes')->insert($p); }
        $mapPerm = DB::table('permissoes')->pluck('id', 'codigo');
        $admId = DB::table('papeis')->where('nome', 'Administradores')->value('id');
        $pacId = DB::table('papeis')->where('nome', 'Pacientes')->value('id');
        foreach ($mapPerm as $pid) { DB::table('papeis_permissoes')->insert(['papel_id' => $admId, 'permissao_id' => $pid]); }
        foreach (['ver_minha_conta', 'alterar_senha', 'paciente_ver_medicamentos', 'paciente_ver_historico'] as $c) {
            DB::table('papeis_permissoes')->insert(['papel_id' => $pacId, 'permissao_id' => $mapPerm[$c]]);
        }
        $adminId = DB::table('usuarios')->insertGetId([
            'nome' => 'Administrador',
            'email' => 'admin@intrafarma.com',
            'login' => 'admin',
            'senha_hash' => bcrypt('admin123'),
            'ativo' => true,
        ]);
        $patientId = DB::table('usuarios')->insertGetId([
            'nome' => 'Paciente Demo',
            'email' => 'paciente@intrafarma.com',
            'login' => 'paciente',
            'senha_hash' => bcrypt('paciente123'),
            'ativo' => true,
        ]);
        DB::table('usuarios_papeis')->insert(['usuario_id' => $adminId, 'papel_id' => $admId]);
        DB::table('usuarios_papeis')->insert(['usuario_id' => $patientId, 'papel_id' => $pacId]);
    }

    public function test_paciente_ve_links_de_portal()
    {
        $user = Usuario::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('configuracoes.index'));
        $resp->assertStatus(200);
        $resp->assertSee(route('paciente.medicamentos'), false);
        $resp->assertSee(route('paciente.historico'), false);
        $resp->assertSee(route('paciente.configuracoes'), false);
        $resp->assertDontSee(route('estoque.index'), false);
        $resp->assertDontSee(route('dispensacoes.create'), false);
    }

    public function test_paciente_bloqueado_em_estoque()
    {
        $user = Usuario::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $this->get(route('estoque.index'))->assertStatus(403);
    }

    public function test_admin_tem_acesso_total()
    {
        $user = Usuario::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        $this->get(route('estoque.index'))->assertStatus(200);
        $this->get(route('dispensacoes.create'))->assertStatus(200);
        $this->get(route('paciente.medicamentos'))->assertStatus(200);
        $this->get(route('paciente.historico'))->assertStatus(200);
    }
}