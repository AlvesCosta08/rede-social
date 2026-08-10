<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ⭐ PRIMEIRO: VERIFICA SE O ADMIN JÁ EXISTE
        $admin = DB::table('filiado')->where('matricula', 1)->first();
        
        if ($admin) {
            $this->command->info('⚠️ Admin já existe! Atualizando...');
            $this->atualizarAdmin($admin);
            return;
        }

        // ⭐ CRIA UM ADMIN NOVO
        $this->criarAdmin();
    }

    private function criarAdmin(): void
    {
        // ⭐ DADOS DO ADMIN - SOMENTE CAMPOS QUE EXISTEM NA TABELA
        $dados = [
            'matricula' => 1,
            'nome' => 'Administrador ADTC2',
            'nome_carteira' => 'Administrador ADTC2',
            'email' => 'admin@gmail.com',
            'telefone' => '(99) 99999-9999',
            'documento' => '111.111.111-00',
            'estadoCivil' => 'Solteiro(a)',
            'dataNascimento' => '1990-01-01',
            'mae' => 'Administradora',
            'pai' => 'Administrador',
            'endereco' => 'Rua Principal, 100',
            'bairro' => 'Centro',
            'cep' => '61900-000',
            'cidade' => 'Maranguape',
            'uf' => 'CE',
            'congregacao' => 'SEDE',
            'funcao' => 'Administrador',
            'status' => 'ativo',
            'datCadastro' => Carbon::now()->format('Y-m-d H:i:s'),
            'password' => Hash::make('admin123'),
            'nivel' => 'admin', // ⭐ IMPORTANTE: NOVO SISTEMA
            'admin' => 1,        // ⭐ IMPORTANTE: LEGADO
            'remember_token' => null,
        ];

        // ⭐ ADICIONA CAMPOS OPCIONAIS (SE EXISTIREM)
        $colunas = DB::getSchemaBuilder()->getColumnListing('filiado');
        
        $camposOpcionais = [
            'dataBatismo' => '1990-01-01',
            'data_Consagracao' => null,
            'data_saida' => null,
            'telefone2' => null,
            'logradouro' => 'Rua Principal',
            'numero' => 100,
            'foto' => null,
            'bio' => 'Administrador do Sistema ADTC2',
            'privacidade' => 0,
            'arquivo' => null,
            'cartas' => null,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        // ⭐ FILTRA APENAS OS CAMPOS QUE EXISTEM
        foreach ($camposOpcionais as $campo => $valor) {
            if (in_array($campo, $colunas)) {
                $dados[$campo] = $valor;
            }
        }

        // ⭐ INSERE O ADMIN
        DB::table('filiado')->insert($dados);

        $this->command->info('✅ Admin criado com sucesso!');
        $this->command->info('📧 Email: admin@gmail.com');
        $this->command->info('🔑 Senha: Lav8@471');
        $this->command->info('📋 Campos usados: ' . implode(', ', array_keys($dados)));
    }

    private function atualizarAdmin($admin): void
    {
        // ⭐ GARANTE QUE O ADMIN TEM OS CAMPOS CORRETOS
        $update = [
            'nivel' => 'admin',
            'admin' => 1,
            'status' => 'ativo',
            'password' => Hash::make('Lav8@471'),
        ];

        // ⭐ VERIFICA SE O CAMPO EXISTE ANTES DE ATUALIZAR
        $colunas = DB::getSchemaBuilder()->getColumnListing('filiado');
        foreach ($update as $campo => $valor) {
            if (in_array($campo, $colunas)) {
                DB::table('filiado')->where('matricula', 1)->update([$campo => $valor]);
            }
        }

        $this->command->info('✅ Admin atualizado com sucesso!');
        $this->command->info('📧 Email: admin@gmail.com');
        $this->command->info('🔑 Senha: Lav8@471');
    }
}