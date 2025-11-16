<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SettingsAccessTest extends TestCase
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

        DB::table('papeis')->insert([['nome' => 'Administradores'], ['nome' => 'Funcionários']]);
        $perms = [
            ['codigo' => 'gerenciar_usuarios', 'nome' => 'Gerenciar Usuários'],
            ['codigo' => 'gerenciar_permissoes', 'nome' => 'Gerenciar Permissões'],
            ['codigo' => 'ver_minha_conta', 'nome' => 'Ver Minha Conta'],
            ['codigo' => 'alterar_senha', 'nome' => 'Alterar Senha'],
        ];
        foreach ($perms as $p) { DB::table('permissoes')->insert($p); }
        $mapPerm = DB::table('permissoes')->pluck('id', 'codigo');
        $admId = DB::table('papeis')->where('nome', 'Administradores')->value('id');
        foreach ($mapPerm as $pid) { DB::table('papeis_permissoes')->insert(['papel_id' => $admId, 'permissao_id' => $pid]); }
        $adminId = DB::table('usuarios')->insertGetId([
            'nome' => 'Administrador',
            'email' => 'admin@intrafarma.com',
            'login' => 'admin',
            'senha_hash' => bcrypt('admin123'),
            'ativo' => true,
        ]);
        DB::table('usuarios_papeis')->insert(['usuario_id' => $adminId, 'papel_id' => $admId]);

        $staffId = DB::table('usuarios')->insertGetId([
            'nome' => 'Funcionário',
            'email' => 'staff@intrafarma.com',
            'login' => 'staff',
            'senha_hash' => bcrypt('staff123'),
            'ativo' => true,
        ]);
        $funcId = DB::table('papeis')->where('nome', 'Funcionários')->value('id');
        DB::table('usuarios_papeis')->insert(['usuario_id' => $staffId, 'papel_id' => $funcId]);
    }

    public function test_admin_ve_botoes_de_configuracoes()
    {
        $user = \App\Models\Usuario::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('configuracoes.index'));
        $resp->assertStatus(200);
        $resp->assertSee(route('usuarios.index'), false);
        $resp->assertSee(route('permissoes.index'), false);
    }

    public function test_funcionario_nao_ve_botoes_admin()
    {
        $user = \App\Models\Usuario::where('email', 'staff@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('configuracoes.index'));
        $resp->assertStatus(200);
        $resp->assertDontSee(route('usuarios.index'), false);
        $resp->assertDontSee(route('permissoes.index'), false);
    }
}