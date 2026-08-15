<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            if (Schema::hasColumn('filiado', 'bio')) {
                $table->text('bio')->nullable()->change();
            } else {
                $table->text('bio')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            if (Schema::hasColumn('filiado', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
};
