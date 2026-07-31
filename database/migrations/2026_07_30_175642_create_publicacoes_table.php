<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicacoes', function (Blueprint $table) {
            $table->id();
            
            // Usando o mesmo tipo da tabela filiado: int(11)
            $table->integer('filiado_matricula');
            
            $table->text('conteudo');
            $table->integer('curtidas_count')->default(0);
            $table->timestamps();
            
            // Foreign key com o tipo correto
            $table->foreign('filiado_matricula')
                  ->references('matricula')
                  ->on('filiado')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicacoes');
    }
};