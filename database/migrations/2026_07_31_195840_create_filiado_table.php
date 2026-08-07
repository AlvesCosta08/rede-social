<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ========================================
        // 1. ALTERAR CHARSET DA TABELA
        // ========================================
        DB::statement('ALTER TABLE filiado CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        // ========================================
        // 2. MODIFICAR CAMPOS EXISTENTES
        // ========================================
        Schema::table('filiado', function (Blueprint $table) {
            // Matrícula como AUTO_INCREMENT (mantendo dados)
            $table->integer('matricula', true)->change();
            
            // Campos NOT NULL com valores padrão
            $table->string('congregacao')->nullable()->change();
            $table->string('logradouro')->nullable()->change();
            $table->string('endereco')->nullable()->change();
            $table->string('bairro')->nullable()->change();
            $table->string('cep')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('cidade')->nullable()->change();
            $table->string('uf', 2)->nullable()->change();
            $table->string('documento')->nullable()->change();
            $table->string('telefone')->nullable()->change();
            $table->string('estadoCivil')->nullable()->change();
            $table->date('dataNascimento')->nullable()->change();
            $table->string('mae')->nullable()->change();
            $table->string('pai')->nullable()->change();
            $table->date('datCadastro')->nullable()->change();
            $table->date('dataBatismo')->nullable()->change();
            $table->date('data_Consagracao')->nullable()->change();
            $table->string('arquivo')->nullable()->change();
            $table->string('cartas', 200)->nullable()->change();
            $table->string('funcao')->nullable()->change();
            $table->string('status', 50)->default('ativo')->change();
            $table->integer('numero')->nullable()->change();
            $table->string('nome_carteira')->nullable()->change();
        });
        
        // ========================================
        // 3. ADICIONAR CAMPOS FALTANTES
        // ========================================
        Schema::table('filiado', function (Blueprint $table) {
            // Campos que o model usa mas não existem
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
        });
        
        // ========================================
        // 4. ADICIONAR ÍNDICES
        // ========================================
        Schema::table('filiado', function (Blueprint $table) {
            $table->unique('matricula');
            $table->unique('email')->nullable();
            $table->unique('documento')->nullable();
            $table->index('status');
            $table->index('funcao');
            $table->index('congregacao');
            $table->index('nome');
            $table->index('cidade');
            $table->index('uf');
            $table->index('dataNascimento');
            $table->index('datCadastro');
        });
        
        // ========================================
        // 5. AJUSTAR DADOS EXISTENTES
        // ========================================
        // Corrigir status vazios ou nulos
        DB::table('filiado')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'ativo']);
        
        // Corrigir funcao vazia
        DB::table('filiado')
            ->whereNull('funcao')
            ->orWhere('funcao', '')
            ->update(['funcao' => 'Membro']);
        
        // Preencher datCadastro se vazio
        DB::table('filiado')
            ->whereNull('datCadastro')
            ->update(['datCadastro' => now()]);
        
        // Preencher created_at com datCadastro
        DB::table('filiado')
            ->whereNull('created_at')
            ->update(['created_at' => DB::raw('datCadastro')]);
        
        // Preencher updated_at com created_at
        DB::table('filiado')
            ->whereNull('updated_at')
            ->update(['updated_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Rollback seguro (remover apenas o que adicionamos)
        Schema::table('filiado', function (Blueprint $table) {
            $table->dropColumn(['telefone2', 'data_saida', 'remember_token', 'created_at', 'updated_at']);
            $table->dropUnique(['email']);
            $table->dropUnique(['documento']);
            $table->dropIndex(['status']);
            $table->dropIndex(['funcao']);
            $table->dropIndex(['congregacao']);
            $table->dropIndex(['nome']);
            $table->dropIndex(['cidade']);
            $table->dropIndex(['uf']);
            $table->dropIndex(['dataNascimento']);
            $table->dropIndex(['datCadastro']);
        });
    }
};