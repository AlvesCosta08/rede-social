#!/bin/bash

echo "🔧 CORRIGINDO TODAS AS MIGRAÇÕES DE UMA VEZ"
echo "============================================"
echo ""

# 1. CORRIGIR MIGRAÇÃO DA COLUNA BIO
echo "1️⃣ Corrigindo migração da coluna bio..."
cat > database/migrations/2026_08_08_165833_increase_bio_field_length_in_filiado.php << 'EOF'
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
EOF
echo "✅ Corrigido!"

# 2. CORRIGIR MIGRAÇÃO FIX_TEXT_FIELDS
echo "2️⃣ Corrigindo migração fix_text_fields_safely..."
cat > database/migrations/2026_08_08_170852_fix_text_fields_safely.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $columns = ['bio', 'observacoes', 'foto', 'telefone2', 'remember_token', 'data_saida'];
            foreach ($columns as $column) {
                if (!Schema::hasColumn('filiado', $column)) {
                    if (in_array($column, ['bio', 'observacoes'])) {
                        $table->text($column)->nullable();
                    } elseif (in_array($column, ['data_saida'])) {
                        $table->date($column)->nullable();
                    } else {
                        $table->string($column, 255)->nullable();
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('filiado', function (Blueprint $table) {
            $columns = ['bio', 'observacoes', 'foto', 'telefone2', 'remember_token', 'data_saida'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('filiado', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
EOF
echo "✅ Corrigido!"

# 3. DESABILITAR MIGRAÇÕES DUPLICADAS
echo "3️⃣ Removendo migrações duplicadas..."
if [ -f "database/migrations/2026_08_08_142834_add_nivel_column_to_filiado_table.php" ]; then
    mv database/migrations/2026_08_08_142834_add_nivel_column_to_filiado_table.php database/migrations/2026_08_08_142834_add_nivel_column_to_filiado_table.php.disabled
    echo "   ✅ Desabilitada: 2026_08_08_142834_add_nivel_column_to_filiado_table.php"
fi

if [ -f "database/migrations/2026_08_08_165925_add_nivel_column_if_not_exists_to_filiado_table.php" ]; then
    mv database/migrations/2026_08_08_165925_add_nivel_column_if_not_exists_to_filiado_table.php database/migrations/2026_08_08_165925_add_nivel_column_if_not_exists_to_filiado_table.php.disabled
    echo "   ✅ Desabilitada: 2026_08_08_165925_add_nivel_column_if_not_exists_to_filiado_table.php"
fi

if [ -f "database/migrations/2026_08_08_170758_fix_text_fields_safely.php" ]; then
    mv database/migrations/2026_08_08_170758_fix_text_fields_safely.php database/migrations/2026_08_08_170758_fix_text_fields_safely.php.disabled
    echo "   ✅ Desabilitada: 2026_08_08_170758_fix_text_fields_safely.php"
fi

# 4. REMOVER TABELA FILIADO SE EXISTIR (opcional)
echo "4️⃣ Verificando tabela filiado..."
php artisan tinker --execute="
try {
    DB::statement('DROP TABLE IF EXISTS filiado');
    echo '   ✅ Tabela filiado removida com sucesso!' . PHP_EOL;
} catch(Exception \$e) {
    echo '   ⚠️  Tabela filiado não existe ou já foi removida' . PHP_EOL;
}
" 2>/dev/null

# 5. REMOVER MIGRAÇÃO DO BANCO
echo "5️⃣ Limpando registros de migração..."
php artisan tinker --execute="
try {
    DB::table('migrations')->where('migration', 'like', '%filiado%')->delete();
    echo '   ✅ Registros de migração removidos!' . PHP_EOL;
} catch(Exception \$e) {
    echo '   ⚠️  Nenhum registro encontrado' . PHP_EOL;
}
" 2>/dev/null

# 6. RECRIAR A TABELA CORRETAMENTE
echo "6️⃣ Recriando a tabela filiado com todas as colunas..."
cat > database/migrations/2026_07_31_195840_create_filiado_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filiado', function (Blueprint $table) {
            // Chave primária
            $table->integer('matricula', true)->autoIncrement();
            
            // Dados pessoais
            $table->string('nome');
            $table->string('email')->nullable()->unique();
            $table->string('documento')->nullable()->unique();
            $table->date('dataNascimento')->nullable();
            $table->string('estadoCivil')->nullable();
            $table->string('mae')->nullable();
            $table->string('pai')->nullable();
            
            // Contato
            $table->string('telefone')->nullable();
            $table->string('telefone2')->nullable();
            $table->string('foto')->nullable();
            $table->text('bio')->nullable();
            
            // Endereço
            $table->string('logradouro')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep')->nullable();
            
            // Dados da igreja
            $table->string('congregacao')->nullable();
            $table->date('dataBatismo')->nullable();
            $table->date('data_Consagracao')->nullable();
            $table->date('data_saida')->nullable();
            $table->date('datCadastro')->nullable();
            
            // Função e status
            $table->string('funcao')->nullable()->default('Membro');
            $table->string('status', 50)->default('ativo');
            $table->integer('nivel')->default(1);
            $table->string('cargo')->nullable();
            $table->string('departamento')->nullable();
            
            // Arquivos
            $table->string('arquivo')->nullable();
            $table->string('cartas', 200)->nullable();
            $table->string('nome_carteira')->nullable();
            
            // Autenticação
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            
            // Observações
            $table->text('observacoes')->nullable();
            
            // Timestamps
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            
            // Índices
            $table->index('status');
            $table->index('funcao');
            $table->index('congregacao');
            $table->index('nome');
            $table->index('cidade');
            $table->index('uf');
            $table->index('dataNascimento');
            $table->index('datCadastro');
            $table->index('nivel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filiado');
    }
};
EOF
echo "✅ Tabela filiado recriada com todas as colunas!"

# 7. EXECUTAR TODAS AS MIGRAÇÕES
echo "7️⃣ Executando todas as migrações..."
php artisan migrate --force

# 8. VERIFICAR RESULTADO
echo ""
echo "============================================"
echo "📊 VERIFICANDO RESULTADO"
echo "============================================"

echo ""
echo "📋 Status das migrações:"
php artisan migrate:status

echo ""
echo "📋 Colunas da tabela filiado:"
php artisan tinker --execute="
\$columns = DB::select('SHOW COLUMNS FROM filiado');
echo 'Colunas encontradas:' . PHP_EOL;
foreach (\$columns as \$col) {
    echo '  ✅ ' . \$col->Field . ' (' . \$col->Type . ')' . PHP_EOL;
}
" 2>/dev/null

echo ""
echo "============================================"
echo "✅ TODAS AS MIGRAÇÕES CONCLUÍDAS COM SUCESSO!"
echo "============================================"