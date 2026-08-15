<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Adicionar colunas apenas se não existirem
            if (!Schema::hasColumn('filiado', 'foto')) {
                $table->string('foto')->nullable()->after('nome');
            }
            
            if (!Schema::hasColumn('filiado', 'telefone2')) {
                $table->string('telefone2')->nullable()->after('telefone');
            }
            
            if (!Schema::hasColumn('filiado', 'data_saida')) {
                $table->date('data_saida')->nullable()->after('data_Consagracao');
            }
            
            if (!Schema::hasColumn('filiado', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('filiado', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent()->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->after('created_at');
            }
            
            // NOTA: Os índices já foram criados na migração create_filiado_table
            // Não precisamos recriá-los aqui
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $columns = ['foto', 'telefone2', 'data_saida', 'remember_token', 'created_at', 'updated_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('filiado', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};