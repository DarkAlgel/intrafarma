<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Schema::hasTable('users')) {
            if (!User::where('email', 'admin@intrafarma.com')->exists()) {
                User::create([
                    'name' => 'Administrador',
                    'email' => 'admin@intrafarma.com',
                    'password' => Hash::make('admin123'),
                    'email_verified_at' => now(),
                ]);
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('usuarios')) {
            $existe = DB::table('usuarios')->where('email', 'admin@intrafarma.com')->exists();
            if (!$existe) {
                DB::table('usuarios')->insert([
                    'nome' => 'Administrador',
                    'celular' => null,
                    'email' => 'admin@intrafarma.com',
                    'login' => 'admin',
                    'senha_hash' => 'admin123',
                    'datacadastro' => DB::raw('now()'),
                    'ultimoacesso' => null,
                    'ativo' => true,
                ]);
            }
        }
    }
}