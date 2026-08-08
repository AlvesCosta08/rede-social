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
        Schema::table('filiado', function (Blueprint $table) {
            // ⭐ AUMENTA O CAMPO bio PARA TEXT (até 65.535 caracteres)
            $table->text('bio')->nullable()->change();
            
            // OU se quiser um tamanho específico (ex: 5000)
            // $table->string('bio', 5000)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Volta para o tamanho original (255)
            $table->string('bio', 255)->nullable()->change();
        });
    }
};