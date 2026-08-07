<?php

// Caminho para a raiz do Laravel (ajuste se necessário)
$caminho_laravel = __DIR__;

// Verifica se é uma instalação Laravel
if (!file_exists($caminho_laravel . '/artisan') || !file_exists($caminho_laravel . '/vendor/autoload.php')) {
    die("Erro: Diretório Laravel inválido.\n");
}

// Inclui o autoload
require_once $caminho_laravel . '/vendor/autoload.php';

// Inicializa o app
try {
    $app = require_once $caminho_laravel . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
} catch (Exception $e) {
    die("Erro ao inicializar: " . $e->getMessage() . "\n");
}

echo "<pre>\n";
echo "Limpando caches do Laravel...\n";

$comandos = [
    'config:clear',
    'cache:clear',
    'view:clear',
    'route:clear',
];

foreach ($comandos as $comando) {
    $kernel->call($comando);
    echo "✅ $comando executado.\n";
}

echo "Limpeza concluída.\n";
echo "</pre>\n";