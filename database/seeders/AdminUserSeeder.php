<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Verifica se o admin já existe
        $admin = DB::table('filiado')->where('matricula', 1)->first();
        
        if (!$admin) {
            // Pega a lista de colunas da tabela
            $colunas = DB::getSchemaBuilder()->getColumnListing('filiado');
            
            // Dados base do admin (todos os campos possíveis)
            $dadosCompletos = [
                'matricula' => 1,
                'nome' => 'Administrador ADTC2',
                'nome_carteira' => 'Administrador ADTC2',
                'email' => 'admin@gmail.com',
                'telefone' => '(99) 99999-9999',
                'telefone2' => null,
                'documento' => '111.111.111-00',
                'estadoCivil' => 'Solteiro(a)',
                'dataNascimento' => '1990-01-01',
                'mae' => 'Administradora',
                'pai' => 'Administrador',
                'logradouro' => 'Rua Principal',
                'endereco' => 'Rua Principal, 100',
                'numero' => 100,
                'bairro' => 'Centro',
                'cep' => '61900-000',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Administrador',
                'funcao_formatada' => 'Administrador',
                'status' => 'ativo',
                'status_icon' => '#4caf50',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1990-01-01',
                'data_Consagracao' => null,
                'data_saida' => null,
                'password' => Hash::make('admin123'),
                'remember_token' => null,
                'foto' => null,
                'bio' => 'Administrador do Sistema ADTC2',
                'privacidade' => false,
                'arquivo' => null,
                'cartas' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            
            // Filtra apenas as colunas que existem na tabela
            $dadosFiltrados = [];
            foreach ($dadosCompletos as $campo => $valor) {
                if (in_array($campo, $colunas)) {
                    $dadosFiltrados[$campo] = $valor;
                }
            }
            
            // Insere o admin
            DB::table('filiado')->insert($dadosFiltrados);
            
            $this->command->info('✅ Usuário Administrador criado com sucesso!');
            $this->command->info('📧 Email: admin@gmail.com');
            $this->command->info('🔑 Senha: admin123');
            $this->command->info('📋 Colunas usadas: ' . implode(', ', array_keys($dadosFiltrados)));
        } else {
            $this->command->info('⚠️ Usuário Administrador já existe!');
        }
    }
}