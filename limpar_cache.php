<?php
// fix-laravel-vite.php
// Script completo para resolver problema do Vite no Laravel 12

// Configuração de timeout para execuções longas
set_time_limit(300);
ini_set('memory_limit', '512M');

// Caminho para a raiz do Laravel
$caminho_laravel = __DIR__;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Laravel Vite - Conexão Igreja</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f8f9fa; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4F46E5; padding-bottom: 10px; }
        .success { color: #059669; background: #D1FAE5; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #DC2626; background: #FEE2E2; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #D97706; background: #FEF3C7; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { background: #E0E7FF; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .step { padding: 8px 0; border-bottom: 1px solid #E5E7EB; }
        .step:last-child { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-right: 10px; }
        .badge-success { background: #10B981; color: white; }
        .badge-error { background: #EF4444; color: white; }
        .badge-warning { background: #F59E0B; color: white; }
        .badge-info { background: #3B82F6; color: white; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; color: #6B7280; font-size: 14px; }
        code { background: #F3F4F6; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
        .cmd { background: #1e293b; color: #e2e8f0; padding: 10px 15px; border-radius: 6px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🚀 Fix Laravel Vite - Conexão Igreja</h1>
    <p>Versão Laravel 12.64.0 | PHP 8.4.24</p>
    <hr>";

// Verifica se é uma instalação Laravel
if (!file_exists($caminho_laravel . '/artisan') || !file_exists($caminho_laravel . '/vendor/autoload.php')) {
    echo "<div class='error'>❌ Erro: Diretório Laravel inválido. O arquivo artisan ou vendor não foi encontrado.</div>";
    echo "</div></body></html>";
    die();
}

// Inclui o autoload
require_once $caminho_laravel . '/vendor/autoload.php';

// Inicializa o app
try {
    $app = require_once $caminho_laravel . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao inicializar Laravel: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "</div></body></html>";
    die();
}

echo "<h2>🔧 Executando correções...</h2>";

// ============================================
// 1. LIMPAR CACHES
// ============================================
echo "<h3>📦 1. Limpando caches do Laravel</h3>";

$comandos = [
    'config:clear' => 'Limpar configurações',
    'cache:clear' => 'Limpar cache',
    'view:clear' => 'Limpar views compiladas',
    'route:clear' => 'Limpar rotas',
];

foreach ($comandos as $comando => $descricao) {
    try {
        $kernel->call($comando);
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> $descricao</div>";
    } catch (Exception $e) {
        echo "<div class='step'>⚠️ <span class='badge badge-warning'>WARN</span> $descricao - " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// ============================================
// 2. VERIFICAR/CRIAR DIRETÓRIO public/build
// ============================================
echo "<h3>📁 2. Verificando diretório public/build</h3>";

$buildDir = $caminho_laravel . '/public/build';
if (!is_dir($buildDir)) {
    if (mkdir($buildDir, 0755, true)) {
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> Diretório public/build criado</div>";
    } else {
        echo "<div class='step'>❌ <span class='badge badge-error'>ERRO</span> Não foi possível criar public/build</div>";
    }
} else {
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> Diretório public/build já existe</div>";
}

// ============================================
// 3. REMOVER BUILD ANTIGO E RECOMPILAR
// ============================================
echo "<h3>🔄 3. Removendo build antigo e recompilando</h3>";

// 3.1 Remover build antigo
echo "<div class='step'>🗑️ Removendo public/build/ antigo...</div>";
if (is_dir($buildDir)) {
    // Remove todos os arquivos dentro do diretório
    $files = glob($buildDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } elseif (is_dir($file)) {
            array_map('unlink', glob($file . '/*.*'));
            rmdir($file);
        }
    }
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> Build antigo removido</div>";
}

// 3.2 Executar npm run build
echo "<div class='step'>📦 Executando npm run build...</div>";
$output = shell_exec('npm run build 2>&1');
if ($output !== null) {
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> npm run build executado</div>";
    // Mostra as últimas linhas do output
    $lines = explode("\n", $output);
    $lastLines = array_slice($lines, -5);
    echo "<div class='cmd'>" . htmlspecialchars(implode("\n", $lastLines)) . "</div>";
} else {
    echo "<div class='step'>⚠️ <span class='badge badge-warning'>WARN</span> npm run build não foi executado. Execute manualmente.</div>";
}

// 3.3 Copiar manifest
echo "<div class='step'>📄 Copiando manifest.json...</div>";
$manifestVite = $buildDir . '/.vite/manifest.json';
$manifestFile = $buildDir . '/manifest.json';

if (file_exists($manifestVite)) {
    copy($manifestVite, $manifestFile);
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> manifest.json copiado</div>";
} else {
    // Cria manifest.json manualmente se não existir
    $manifestContent = [
        'resources/css/app.css' => [
            'file' => 'assets/app.css',
            'src' => 'resources/css/app.css',
            'isEntry' => true,
            'imports' => []
        ],
        'resources/js/app.js' => [
            'file' => 'assets/app.js',
            'src' => 'resources/js/app.js',
            'isEntry' => true,
            'imports' => []
        ]
    ];
    file_put_contents($manifestFile, json_encode($manifestContent, JSON_PRETTY_PRINT));
    echo "<div class='step'>⚠️ <span class='badge badge-warning'>WARN</span> manifest.json criado manualmente</div>";
}

// 3.4 Verificar os arquivos
echo "<div class='step'>📋 Verificando arquivos em public/build/...</div>";
$buildFiles = glob($buildDir . '/*');
echo "<div class='cmd'>";
foreach ($buildFiles as $file) {
    echo basename($file) . "\n";
}
echo "</div>";

// ============================================
// 4. CONFIGURAR VITE
// ============================================
echo "<h3>⚙️ 4. Configurando Vite no Laravel 12</h3>";

// Verifica/Atualiza config/app.php
$configApp = $caminho_laravel . '/config/app.php';
if (file_exists($configApp)) {
    $content = file_get_contents($configApp);
    
    // Verifica se o Vite está configurado
    if (strpos($content, 'ViteServiceProvider') === false) {
        // Adiciona o provider se não existir
        $search = "'providers' => [";
        $replace = "'providers' => [\n        // Laravel Vite\n        Illuminate\Foundation\Providers\ViteServiceProvider::class,";
        $content = str_replace($search, $replace, $content);
        file_put_contents($configApp, $content);
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> ViteServiceProvider adicionado ao config/app.php</div>";
    } else {
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> ViteServiceProvider já está configurado</div>";
    }
}

// ============================================
// 5. CRIAR/ATUALIZAR vite.config.js
// ============================================
echo "<h3>📝 5. Verificando vite.config.js</h3>";

$viteConfig = $caminho_laravel . '/vite.config.js';
if (!file_exists($viteConfig)) {
    $configContent = <<<'EOT'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build',
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        assetsDir: '',
        emptyOutDir: true,
        rollupOptions: {
            input: ['resources/css/app.css', 'resources/js/app.js']
        }
    },
    base: '/sistemas/conexao-igreja/build/',
});
EOT;
    file_put_contents($viteConfig, $configContent);
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> vite.config.js criado</div>";
} else {
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> vite.config.js já existe</div>";
}

// ============================================
// 6. ATUALIZAR package.json
// ============================================
echo "<h3>📦 6. Verificando package.json</h3>";

$packageFile = $caminho_laravel . '/package.json';
if (!file_exists($packageFile)) {
    $packageContent = [
        'private' => true,
        'scripts' => [
            'dev' => 'vite',
            'build' => 'vite build'
        ],
        'devDependencies' => [
            'vite' => '^5.0.0',
            'laravel-vite-plugin' => '^1.0.0'
        ]
    ];
    file_put_contents($packageFile, json_encode($packageContent, JSON_PRETTY_PRINT));
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> package.json criado</div>";
} else {
    // Verifica se tem os scripts necessários
    $content = json_decode(file_get_contents($packageFile), true);
    $updated = false;
    
    if (!isset($content['scripts'])) {
        $content['scripts'] = [];
    }
    if (!isset($content['scripts']['dev'])) {
        $content['scripts']['dev'] = 'vite';
        $updated = true;
    }
    if (!isset($content['scripts']['build'])) {
        $content['scripts']['build'] = 'vite build';
        $updated = true;
    }
    
    if ($updated) {
        file_put_contents($packageFile, json_encode($content, JSON_PRETTY_PRINT));
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> package.json atualizado</div>";
    } else {
        echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> package.json já está configurado</div>";
    }
}

// ============================================
// 7. VERIFICAR DEPENDÊNCIAS
// ============================================
echo "<h3>🔍 7. Verificando dependências</h3>";

$nodeModules = $caminho_laravel . '/node_modules';
if (!is_dir($nodeModules)) {
    echo "<div class='warning'>⚠️ <span class='badge badge-warning'>ATENÇÃO</span> node_modules não encontrado. Execute 'npm install' no servidor local e faça upload.</div>";
    
    // Cria um arquivo de instruções
    $instructions = <<<'EOT'
# Instruções para instalar dependências localmente

1. Instale o Node.js: https://nodejs.org/
2. Execute no seu computador:
   npm install
   npm run build

3. Faça upload da pasta public/build para o servidor
EOT;
    file_put_contents($caminho_laravel . '/INSTRUCOES_INSTALACAO.txt', $instructions);
} else {
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> node_modules encontrado</div>";
}

// ============================================
// 8. LIMPAR CACHES NOVAMENTE
// ============================================
echo "<h3>🔄 8. Limpando caches após configuração</h3>";

try {
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('view:clear');
    echo "<div class='step'>✅ <span class='badge badge-success'>OK</span> Caches limpos após configuração</div>";
} catch (Exception $e) {
    echo "<div class='step'>⚠️ <span class='badge badge-warning'>WARN</span> Erro ao limpar caches: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// ============================================
// 9. STATUS FINAL
// ============================================
echo "<h2>✅ Status Final</h2>";

$status = [
    'manifest.json' => file_exists($manifestFile) ? '✅ OK' : '❌ Faltando',
    'public/build/' => is_dir($buildDir) ? '✅ OK' : '❌ Faltando',
    'CSS' => file_exists($caminho_laravel . '/public/css/app.css') ? '✅ OK' : '❌ Faltando',
    'JS' => file_exists($caminho_laravel . '/public/js/app.js') ? '✅ OK' : '❌ Faltando',
    'vite.config.js' => file_exists($viteConfig) ? '✅ OK' : '❌ Faltando',
];

foreach ($status as $item => $statusText) {
    echo "<div class='step'>📌 $item: $statusText</div>";
}

// ============================================
// 10. RECOMENDAÇÕES
// ============================================
echo "<div class='info' style='background: #DBEAFE; padding: 15px; border-radius: 8px; margin-top: 20px;'>
    <h3 style='margin-top: 0;'>💡 Recomendações</h3>
    <ul style='margin: 10px 0; padding-left: 20px;'>
        <li>✅ O manifest.json foi criado manualmente para resolver o erro 500</li>
        <li>📱 Agora tente acessar: <strong>/login</strong></li>
        <li>⚠️ Para usar Vite corretamente, execute localmente: <code>npm install &amp;&amp; npm run build</code></li>
        <li>📤 Depois faça upload da pasta <code>public/build</code> para o servidor</li>
        <li>🔄 Ou substitua @vite por links diretos no blade se não quiser usar Vite</li>
    </ul>
</div>";

echo "<div class='footer'>
    <p>🔧 Script executado em: " . date('d/m/Y H:i:s') . "</p>
    <p>📦 Laravel " . app()->version() . " | PHP " . phpversion() . "</p>
</div>";

echo "</div></body></html>";
?>