<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Colunas que estão faltando
            $table->string('telefone2')->nullable()->after('telefone');
            $table->string('nome_carteira')->nullable()->after('funcao');
            $table->string('numero')->nullable()->after('endereco');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cep')->nullable()->after('bairro');
            $table->string('mae')->nullable()->after('cep');
            $table->string('pai')->nullable()->after('mae');
            $table->text('bio')->nullable()->after('pai');
            $table->boolean('privacidade')->default(false)->after('bio');
            $table->date('dataBatismo')->nullable()->after('dataNascimento');
            $table->date('data_Consagracao')->nullable()->after('dataBatismo');
            $table->string('logradouro')->nullable()->after('endereco');
            $table->string('estadoCivil')->nullable()->after('uf');
            $table->string('arquivo')->nullable()->after('status');
            $table->string('cartas')->nullable()->after('arquivo');
            $table->string('foto')->nullable()->after('cartas');
            $table->decimal('latitude', 10, 8)->nullable()->after('foto');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamp('datCadastro')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};