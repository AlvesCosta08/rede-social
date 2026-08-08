<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ⭐ endereco - pode ser TEXT (não tem índice)
        if (Schema::hasColumn('filiado', 'endereco')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY endereco TEXT NULL");
                echo "✅ endereco alterado para TEXT\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar endereco: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ logradouro - pode ser TEXT (não tem índice)
        if (Schema::hasColumn('filiado', 'logradouro')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY logradouro TEXT NULL");
                echo "✅ logradouro alterado para TEXT\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar logradouro: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ bairro - VARCHAR(500) (não tem índice)
        if (Schema::hasColumn('filiado', 'bairro')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY bairro VARCHAR(500) NULL");
                echo "✅ bairro alterado para VARCHAR(500)\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar bairro: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ congregacao - NÃO ALTERAR! Tem índice (idx_filiado_congregacao)
        // Mantém como VARCHAR(255) ou VARCHAR(500) mas NUNCA TEXT
        if (Schema::hasColumn('filiado', 'congregacao')) {
            try {
                // Apenas aumenta um pouco se necessário, mas NÃO vira TEXT
                DB::statement("ALTER TABLE filiado MODIFY congregacao VARCHAR(500) NULL");
                echo "✅ congregacao alterado para VARCHAR(500)\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar congregacao: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ bio - pode ser TEXT (não tem índice)
        if (Schema::hasColumn('filiado', 'bio')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY bio TEXT NULL");
                echo "✅ bio alterado para TEXT\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar bio: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ mae - VARCHAR(255) (não tem índice)
        if (Schema::hasColumn('filiado', 'mae')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY mae VARCHAR(255) NULL");
                echo "✅ mae mantido como VARCHAR(255)\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar mae: " . $e->getMessage() . "\n";
            }
        }
        
        // ⭐ pai - VARCHAR(255) (não tem índice)
        if (Schema::hasColumn('filiado', 'pai')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY pai VARCHAR(255) NULL");
                echo "✅ pai mantido como VARCHAR(255)\n";
            } catch (\Exception $e) {
                echo "❌ Erro ao alterar pai: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter apenas as colunas que foram alteradas
        if (Schema::hasColumn('filiado', 'endereco')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY endereco VARCHAR(255) NULL");
            } catch (\Exception $e) {
                // Ignora erro
            }
        }
        
        if (Schema::hasColumn('filiado', 'logradouro')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY logradouro VARCHAR(255) NULL");
            } catch (\Exception $e) {
                // Ignora erro
            }
        }
        
        if (Schema::hasColumn('filiado', 'bairro')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY bairro VARCHAR(255) NULL");
            } catch (\Exception $e) {
                // Ignora erro
            }
        }
        
        if (Schema::hasColumn('filiado', 'bio')) {
            try {
                DB::statement("ALTER TABLE filiado MODIFY bio VARCHAR(255) NULL");
            } catch (\Exception $e) {
                // Ignora erro
            }
        }
    }
};