<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPasswordSeeder extends Seeder
{
    public function run(): void
    {
        // Verifica se o admin existe
        $admin = DB::table('filiado')->where('matricula', 1)->first();
        
        if ($admin) {
            DB::table('filiado')
                ->where('matricula', 1)
                ->update([
                    'password' => Hash::make('Lav8@471'),
                    'updated_at' => now(),
                    'admin' => 1,
                    'super_admin' => 1,
                ]);
            
            $this->command->info('✅ Senha do administrador atualizada!');
            $this->command->info('📧 Email: ' . $admin->email);
            $this->command->info('🔑 Nova Senha: Admin@2026#Seguro!');
        } else {
            $this->command->error('❌ Administrador não encontrado!');
        }
    }
}