<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            
            // Mesmo tipo da tabela filiado
            $table->integer('filiado_matricula');
            $table->unsignedBigInteger('publicacao_id');
            $table->text('conteudo');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('filiado_matricula')
                  ->references('matricula')
                  ->on('filiado')
                  ->onDelete('cascade');
                  
            $table->foreign('publicacao_id')
                  ->references('id')
                  ->on('publicacoes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};