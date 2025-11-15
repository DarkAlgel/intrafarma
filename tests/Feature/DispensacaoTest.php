<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\Lote;
use App\Models\Entrada;

class DispensacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('pacientes', function($t){
            $t->increments('id');
            $t->string('nome');
            $t->string('cpf')->nullable();
        });
        Schema::create('medicamentos', function($t){
            $t->increments('id');
            $t->string('nome');
            $t->string('codigo')->nullable();
            $t->string('forma_retirada')->default('sem_prescricao');
            $t->string('unidade_base')->default('comprimido');
            $t->decimal('dosagem_valor',12,3)->default(0);
            $t->string('dosagem_unidade')->default('mg');
        });
        Schema::create('lotes', function($t){
            $t->increments('id');
            $t->unsignedInteger('medicamento_id');
            $t->date('data_fabricacao')->nullable();
            $t->date('validade');
            $t->string('nome_comercial')->nullable();
        });
        Schema::create('entradas', function($t){
            $t->increments('id');
            $t->unsignedInteger('lote_id');
            $t->decimal('quantidade_base',12,3)->default(0);
            $t->string('unidade');
            $t->decimal('unidades_por_embalagem',12,3)->nullable();
        });
        Schema::create('dispensacoes', function($t){
            $t->increments('id');
            $t->dateTime('data_dispensa')->nullable();
            $t->string('responsavel')->nullable();
            $t->unsignedInteger('paciente_id');
            $t->unsignedInteger('lote_id');
            $t->string('dosagem')->nullable();
            $t->string('nome_comercial')->nullable();
            $t->decimal('quantidade_informada',12,3);
            $t->decimal('quantidade_base',12,3);
            $t->string('unidade');
            $t->string('numero_receita')->nullable();
        });
    }

    public function test_cria_dispensacao_simples()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paciente = Paciente::create(['nome' => 'Fulano']);
        $med = Medicamento::create(['nome' => 'Med A', 'unidade_base' => 'comprimido']);
        $lote = Lote::create(['medicamento_id' => $med->id, 'validade' => now()->addMonth()->toDateString()]);
        Entrada::create(['lote_id' => $lote->id, 'quantidade_base' => 100, 'unidade' => 'comprimido']);

        $resp = $this->post(route('dispensacoes.store'), [
            'paciente_id' => $paciente->id,
            'lote_id' => $lote->id,
            'quantidade_informada' => 10,
            'unidade' => 'comprimido',
        ]);

        $resp->assertRedirect(route('estoque.index'));
        $this->assertDatabaseHas('dispensacoes', [
            'paciente_id' => $paciente->id,
            'lote_id' => $lote->id,
            'quantidade_informada' => 10,
            'quantidade_base' => 10,
            'unidade' => 'comprimido',
        ]);
    }

    public function test_bloqueia_quando_falta_numero_receita()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paciente = Paciente::create(['nome' => 'Fulano']);
        $med = Medicamento::create(['nome' => 'Med B', 'unidade_base' => 'comprimido', 'forma_retirada' => 'com_prescricao']);
        $lote = Lote::create(['medicamento_id' => $med->id, 'validade' => now()->addMonth()->toDateString()]);
        Entrada::create(['lote_id' => $lote->id, 'quantidade_base' => 100, 'unidade' => 'comprimido']);

        $resp = $this->post(route('dispensacoes.store'), [
            'paciente_id' => $paciente->id,
            'lote_id' => $lote->id,
            'quantidade_informada' => 1,
            'unidade' => 'comprimido',
        ]);

        $resp->assertSessionHasErrors(['numero_receita']);
    }

    public function test_bloqueia_por_saldo_insuficiente()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paciente = Paciente::create(['nome' => 'Fulano']);
        $med = Medicamento::create(['nome' => 'Med C', 'unidade_base' => 'comprimido']);
        $lote = Lote::create(['medicamento_id' => $med->id, 'validade' => now()->addMonth()->toDateString()]);
        Entrada::create(['lote_id' => $lote->id, 'quantidade_base' => 5, 'unidade' => 'comprimido']);

        $resp = $this->post(route('dispensacoes.store'), [
            'paciente_id' => $paciente->id,
            'lote_id' => $lote->id,
            'quantidade_informada' => 10,
            'unidade' => 'comprimido',
        ]);

        $resp->assertSessionHasErrors(['quantidade_informada']);
    }
}