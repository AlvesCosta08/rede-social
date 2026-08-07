<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $table->string('foto', 100)->nullable()->change();
        });
    }
};
