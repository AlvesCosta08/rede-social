<?php
// database/migrations/YYYY_MM_DD_create_seguidores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguidores', function (Blueprint $table) {
            $table->id();
            $table->integer('seguidor_matricula');
            $table->integer('seguido_matricula');
            $table->timestamps();
            
            $table->unique(['seguidor_matricula', 'seguido_matricula'], 'unique_seguidor');
            $table->index('seguidor_matricula');
            $table->index('seguido_matricula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguidores');
    }
};