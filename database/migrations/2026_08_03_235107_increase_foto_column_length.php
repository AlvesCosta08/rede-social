<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Verificar se a coluna existe, se não, criar
            if (!Schema::hasColumn('filiado', 'foto')) {
                $table->string('foto')->nullable()->after('nome');
            } else {
                // Se existir, modificar o tamanho
                $table->string('foto', 255)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            if (Schema::hasColumn('filiado', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};
