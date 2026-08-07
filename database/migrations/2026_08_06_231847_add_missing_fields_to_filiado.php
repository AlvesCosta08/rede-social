<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Campos adicionais
            if (!Schema::hasColumn('filiado', 'telefone2')) {
                $table->string('telefone2', 17)->nullable()->after('telefone');
            }
            
            if (!Schema::hasColumn('filiado', 'admin')) {
                $table->boolean('admin')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('admin');
            }
            
            if (!Schema::hasColumn('filiado', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            
            if (!Schema::hasColumn('filiado', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('filiado', 'data_saida')) {
                $table->date('data_saida')->nullable()->after('data_Consagracao');
            }
            
            if (!Schema::hasColumn('filiado', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent()->after('datCadastro');
            }
            
            if (!Schema::hasColumn('filiado', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->after('created_at');
            }

            // Índices
            $table->index('status');
            $table->index('funcao');
            $table->index('congregacao');
            $table->index('nome');
            $table->index('email');
            $table->index('telefone');
            $table->index('documento');
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $table->dropColumn([
                'telefone2',
                'admin',
                'latitude',
                'longitude',
                'remember_token',
                'data_saida',
                'created_at',
                'updated_at'
            ]);
        });
    }
};