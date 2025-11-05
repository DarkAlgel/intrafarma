<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário admin já existe
        if (!User::where('email', 'admin@intrafarma.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@intrafarma.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(), // Marca o email como verificado para o admin
            ]);
        }
    }
}