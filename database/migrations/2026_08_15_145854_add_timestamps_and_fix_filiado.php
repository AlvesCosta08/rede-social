<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ========================================
        // 1. ADICIONAR COLUNAS FALTANTES
        // ========================================
        Schema::table('filiado', function (Blueprint $table) {
            // Timestamps
            if (!Schema::hasColumn('filiado', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent()->after('status');
            }
            
            if (!Schema::hasColumn('filiado', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->after('created_at');
            }
            
            // Remember token para autenticação
            if (!Schema::hasColumn('filiado', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }
            
            // Campos de texto que podem estar faltando
            if (!Schema::hasColumn('filiado', 'bio')) {
                $table->text('bio')->nullable()->after('cartas');
            }
            
            if (!Schema::hasColumn('filiado', 'foto')) {
                $table->string('foto')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('filiado', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('super_admin');
            }
            
            if (!Schema::hasColumn('filiado', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });

        // ========================================
        // 2. AJUSTAR DADOS EXISTENTES
        // ========================================
        
        // Preencher created_at com datCadastro se estiver vazio
        DB::table('filiado')
            ->whereNull('created_at')
            ->whereNotNull('datCadastro')
            ->update(['created_at' => DB::raw('datCadastro')]);

        // Se created_at ainda estiver vazio, usar data atual
        DB::table('filiado')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        // Preencher updated_at com created_at se estiver vazio
        DB::table('filiado')
            ->whereNull('updated_at')
            ->update(['updated_at' => DB::raw('created_at')]);

        // Corrigir status vazios
        DB::table('filiado')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'ativo']);

        // Corrigir funcao vazia
        DB::table('filiado')
            ->whereNull('funcao')
            ->orWhere('funcao', '')
            ->update(['funcao' => 'Membro']);

        // Corrigir nivel vazio
        DB::table('filiado')
            ->whereNull('nivel')
            ->orWhere('nivel', '')
            ->update(['nivel' => 'usuario']);

        // ========================================
        // 3. CORRIGIR MATRÍCULAS VAZIAS (SEM ROW_NUMBER)
        // ========================================
        // Buscar membros com matrícula vazia
        $membrosSemMatricula = DB::table('filiado')
            ->whereNull('matricula')
            ->orWhere('matricula', '')
            ->get();

        if ($membrosSemMatricula->isNotEmpty()) {
            $ano = date('Y');
            
            // Buscar a maior matrícula do ano atual
            $ultimaMatricula = DB::table('filiado')
                ->where('matricula', 'LIKE', $ano . '%')
                ->orderBy('matricula', 'desc')
                ->value('matricula');
            
            $ultimoNumero = 0;
            if ($ultimaMatricula) {
                $ultimoNumero = (int) substr($ultimaMatricula, -4);
            }
            
            foreach ($membrosSemMatricula as $membro) {
                $ultimoNumero++;
                $novaMatricula = $ano . str_pad($ultimoNumero, 4, '0', STR_PAD_LEFT);
                
                DB::table('filiado')
                    ->where('matricula', $membro->matricula)
                    ->orWhere('id', $membro->id ?? 0)
                    ->update(['matricula' => $novaMatricula]);
            }
        }

        // ========================================
        // 4. ADICIONAR ÍNDICES (SE NÃO EXISTIREM)
        // ========================================
        $this->addIndexIfNotExists('filiado', 'created_at');
        $this->addIndexIfNotExists('filiado', 'updated_at');
        $this->addIndexIfNotExists('filiado', 'nivel');
        $this->addIndexIfNotExists('filiado', 'dataNascimento');
        $this->addIndexIfNotExists('filiado', 'datCadastro');
        $this->addIndexIfNotExists('filiado', 'cidade');
        $this->addIndexIfNotExists('filiado', 'uf');
    }

    /**
     * Adiciona índice se não existir
     */
    private function addIndexIfNotExists(string $table, string $column): void
    {
        try {
            $indexName = "{$table}_{$column}_index";
            $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            
            if (empty($exists)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->index($column);
                });
            }
        } catch (\Exception $e) {
            // Tenta criar mesmo assim
            try {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->index($column);
                });
            } catch (\Exception $e) {
                // Ignora se já existir
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            // Remover colunas adicionadas
            $columns = ['created_at', 'updated_at', 'remember_token', 'bio', 'foto', 'latitude', 'longitude'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('filiado', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Remover índices
            $indexes = ['created_at', 'updated_at', 'nivel', 'dataNascimento', 'datCadastro', 'cidade', 'uf'];
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex([$index]);
                } catch (\Exception $e) {
                    // Ignora se não existir
                }
            }
        });
    }
};
