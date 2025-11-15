<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => \Database\Seeders\AdminUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\PatientUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesPermissionsSeeder::class]);
    }

    public function test_menu_condicional_para_paciente()
    {
        $user = User::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('configuracoes.index'));
        $resp->assertSee('Minha Conta');
        $resp->assertSee('Alterar Senha');
        $resp->assertDontSee('Usuários');
        $resp->assertDontSee('Permissões');
    }

    public function test_protecao_backend_paciente_bloqueado()
    {
        $user = User::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $this->get(route('usuarios.index'))->assertStatus(403);
    }

    public function test_menu_condicional_para_admin()
    {
        $user = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('configuracoes.index'));
        $resp->assertSee('Usuários');
        $resp->assertSee('Permissões');
    }

    public function test_persistencia_configuracoes_conta()
    {
        $user = User::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $this->post(route('configuracoes.account.update'), [
            'name' => 'Paciente Alterado',
            'email' => 'paciente@intrafarma.com',
        ])->assertRedirect(route('configuracoes.account'));
        $this->assertEquals('Paciente Alterado', $user->fresh()->name);
    }

    public function test_alteracao_senha_segura()
    {
        $user = User::where('email', 'paciente@intrafarma.com')->first();
        $this->actingAs($user);
        $this->post(route('configuracoes.password.update'), [
            'current_password' => 'paciente123',
            'password' => 'NovaSenha123',
            'password_confirmation' => 'NovaSenha123',
        ])->assertRedirect(route('configuracoes.password'));
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NovaSenha123', $user->fresh()->password));
    }
}