<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ⭐ VERIFICA SE A COLUNA STATUS EXISTE ANTES DE ADICIONAR
        if (!Schema::hasColumn('filiado', 'status')) {
            Schema::table('filiado', function (Blueprint $table) {
                $table->string('status')->nullable()->default('Ativo');
            });
        }

        // ⭐ VERIFICA SE O ÍNDICE JÁ EXISTE ANTES DE CRIAR
        if (!Schema::hasIndex('filiado', 'idx_filiado_status')) {
            Schema::table('filiado', function (Blueprint $table) {
                $table->index('status', 'idx_filiado_status');
            });
        }

        // ⭐ VERIFICA SE A COLUNA SUPER_ADMIN EXISTE
        if (!Schema::hasColumn('filiado', 'super_admin')) {
            Schema::table('filiado', function (Blueprint $table) {
                $table->boolean('super_admin')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Só tenta dropar se existir
            if (Schema::hasIndex('filiado', 'idx_filiado_status')) {
                $table->dropIndex('idx_filiado_status');
            }
            
            if (Schema::hasColumn('filiado', 'super_admin')) {
                $table->dropColumn('super_admin');
            }
            
            // Cuidado ao dropar status - pode perder dados
            // if (Schema::hasColumn('filiado', 'status')) {
            //     $table->dropColumn('status');
            // }
        });
    }
};