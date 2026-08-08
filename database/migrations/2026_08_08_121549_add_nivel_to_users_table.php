<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ⭐ VERIFICA SE A COLUNA NIVEL EXISTE
            if (!Schema::hasColumn('users', 'nivel')) {
                $table->enum('nivel', ['usuario', 'secretario', 'admin'])
                      ->default('usuario')
                      ->after('password')
                      ->comment('Nível de permissão do usuário');
            }
            
            // ⭐ VERIFICA SE A COLUNA CONGREGACAO EXISTE
            if (!Schema::hasColumn('users', 'congregacao')) {
                $table->string('congregacao')
                      ->nullable()
                      ->after('nivel')
                      ->comment('Congregação que o secretário gerencia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nivel', 'congregacao']);
        });
    }
};