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
        // ⭐ VERIFICA SE A COLUNA NÃO EXISTE ANTES DE ADICIONAR
        if (!Schema::hasColumn('filiado', 'nivel')) {
            Schema::table('filiado', function (Blueprint $table) {
                $table->string('nivel', 255)->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('filiado', 'nivel')) {
            Schema::table('filiado', function (Blueprint $table) {
                $table->dropColumn('nivel');
            });
        }
    }
};