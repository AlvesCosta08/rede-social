<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curtidas', function (Blueprint $table) {
            $table->id();
            $table->integer('filiado_matricula');
            $table->unsignedBigInteger('publicacao_id');
            $table->timestamps();
            
            // Unique para evitar curtidas duplicadas
            $table->unique(['filiado_matricula', 'publicacao_id'], 'unique_curtida');
            
            // Foreign keys (se a tabela filiado existir)
            // $table->foreign('filiado_matricula')
            //       ->references('matricula')
            //       ->on('filiado')
            //       ->onDelete('cascade');
                  
            // $table->foreign('publicacao_id')
            //       ->references('id')
            //       ->on('publicacoes')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curtidas');
    }
};