<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class PatientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria um usuário para paciente, se não existir
        $email = 'paciente@intrafarma.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Paciente Demo',
                'email' => $email,
                'password' => Hash::make('paciente123'),
                'email_verified_at' => now(),
            ]);
        }

        // Cria um registro na tabela pacientes para uso nas telas
        // CPF válido para testes: 52998224725
        if (!Paciente::where('cpf', '52998224725')->exists()) {
            Paciente::create([
                'nome' => 'Paciente Demo',
                'cpf' => '52998224725',
                'telefone' => '(11) 99999-0000',
                'cidade' => 'São Paulo',
            ]);
        }
    }
}