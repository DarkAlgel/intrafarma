<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class LayoutNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => \Database\Seeders\AdminUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\PatientUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesPermissionsSeeder::class]);
    }

    public function test_sidebar_presente_em_todas_as_telas()
    {
        $user = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);

        foreach ([
            route('dashboard'),
            route('estoque.index'),
            route('pacientes.index'),
            route('dispensacoes.create'),
            route('fornecedores.index'),
            route('configuracoes.index'),
        ] as $url) {
            $resp = $this->get($url);
            $resp->assertSee('INTRAFARMA');
            $resp->assertSee('Configurações');
        }
    }

    public function test_conteudo_carrega_no_container_principal()
    {
        $user = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        $this->get(route('estoque.index'))
            ->assertSee('Controle de Estoque por Lote')
            ->assertSee('class="flex-1 flex flex-col');
    }

    public function test_responsividade_classes_presentes()
    {
        $user = User::where('email', 'admin@intrafarma.com')->first();
        $this->actingAs($user);
        $resp = $this->get(route('dashboard'));
        $resp->assertSee('INTRAFARMA');
        $resp->assertSee('md:ml-64');
    }
}