<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Verifica se a coluna existe antes de adicionar
            if (!Schema::hasColumn('filiado', 'telefone2')) {
                $table->string('telefone2')->nullable()->after('telefone');
            }
            
            if (!Schema::hasColumn('filiado', 'nome_carteira')) {
                $table->string('nome_carteira')->nullable()->after('funcao');
            }
            
            if (!Schema::hasColumn('filiado', 'numero')) {
                $table->string('numero')->nullable()->after('endereco');
            }
            
            if (!Schema::hasColumn('filiado', 'bairro')) {
                $table->string('bairro')->nullable()->after('numero');
            }
            
            if (!Schema::hasColumn('filiado', 'cep')) {
                $table->string('cep')->nullable()->after('bairro');
            }
            
            if (!Schema::hasColumn('filiado', 'mae')) {
                $table->string('mae')->nullable()->after('cep');
            }
            
            if (!Schema::hasColumn('filiado', 'pai')) {
                $table->string('pai')->nullable()->after('mae');
            }
            
            if (!Schema::hasColumn('filiado', 'bio')) {
                $table->text('bio')->nullable()->after('pai');
            }
            
            if (!Schema::hasColumn('filiado', 'privacidade')) {
                $table->boolean('privacidade')->default(false)->after('bio');
            }
            
            if (!Schema::hasColumn('filiado', 'dataBatismo')) {
                $table->date('dataBatismo')->nullable()->after('dataNascimento');
            }
            
            if (!Schema::hasColumn('filiado', 'data_Consagracao')) {
                $table->date('data_Consagracao')->nullable()->after('dataBatismo');
            }
            
            if (!Schema::hasColumn('filiado', 'logradouro')) {
                $table->string('logradouro')->nullable()->after('endereco');
            }
            
            if (!Schema::hasColumn('filiado', 'estadoCivil')) {
                $table->string('estadoCivil')->nullable()->after('uf');
            }
            
            if (!Schema::hasColumn('filiado', 'arquivo')) {
                $table->string('arquivo')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'cartas')) {
                $table->string('cartas')->nullable()->after('arquivo');
            }
            
            if (!Schema::hasColumn('filiado', 'foto')) {
                $table->string('foto')->nullable()->after('cartas');
            }
            
            if (!Schema::hasColumn('filiado', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('foto');
            }
            
            if (!Schema::hasColumn('filiado', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            
            if (!Schema::hasColumn('filiado', 'datCadastro')) {
                $table->timestamp('datCadastro')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $columns = [
                'telefone2',
                'nome_carteira',
                'numero',
                'bairro',
                'cep',
                'mae',
                'pai',
                'bio',
                'privacidade',
                'dataBatismo',
                'data_Consagracao',
                'logradouro',
                'estadoCivil',
                'arquivo',
                'cartas',
                'foto',
                'latitude',
                'longitude',
                'datCadastro'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('filiado', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};