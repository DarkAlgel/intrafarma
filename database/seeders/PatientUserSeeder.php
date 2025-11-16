<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria um usuário para paciente, se não existir
        $email = 'paciente@intrafarma.com';
        if (Schema::hasTable('users')) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Paciente Demo',
                    'email' => $email,
                    'password' => Hash::make('paciente123'),
                    'email_verified_at' => now(),
                ]);
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('pacientes')) {
            if (!Paciente::where('cpf', '52998224725')->exists()) {
                Paciente::create([
                    'nome' => 'Paciente Demo',
                    'cpf' => '52998224725',
                    'telefone' => '(11) 99999-0000',
                    'cidade' => 'São Paulo',
                ]);
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('usuarios')) {
            $existe = DB::table('usuarios')->where('email', 'paciente@intrafarma.com')->exists();
            if (!$existe) {
                DB::table('usuarios')->insert([
                    'nome' => 'Paciente Demo',
                    'celular' => '(11) 99999-0000',
                    'email' => 'paciente@intrafarma.com',
                    'login' => 'paciente',
                    'senha_hash' => 'paciente123',
                    'datacadastro' => DB::raw('now()'),
                    'ultimoacesso' => null,
                    'ativo' => true,
                ]);
            }
        }
    }
}