<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Verifica se a coluna existe
            if (Schema::hasColumn('filiado', 'data_Consagracao')) {
                $table->date('data_Consagracao')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            if (Schema::hasColumn('filiado', 'data_Consagracao')) {
                $table->date('data_Consagracao')->nullable(false)->change();
            }
        });
    }
};