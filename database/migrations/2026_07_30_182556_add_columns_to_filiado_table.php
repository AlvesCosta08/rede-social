<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Adicionar colunas necessárias para autenticação e perfil
            if (!Schema::hasColumn('filiado', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('filiado', 'foto')) {
                $table->string('foto')->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('filiado', 'bio')) {
                $table->text('bio')->nullable()->after('funcao');
            }
            
            if (!Schema::hasColumn('filiado', 'privacidade')) {
                $table->boolean('privacidade')->default(true)->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            
            if (!Schema::hasColumn('filiado', 'data_saida')) {
                $table->timestamp('data_saida')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'telefone2')) {
                $table->string('telefone2')->nullable()->after('telefone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $table->dropColumn([
                'password', 
                'foto', 
                'bio', 
                'privacidade', 
                'remember_token',
                'data_saida',
                'telefone2'
            ]);
        });
    }
};