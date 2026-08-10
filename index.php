<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Verifica se o app está em manutenção
if (file_exists(__DIR__.'/storage/framework/maintenance.php')) {
    require __DIR__.'/storage/framework/maintenance.php';
}

// Carrega o autoloader do Composer
require __DIR__.'/vendor/autoload.php';

// Inicializa o app e trata a requisição
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());

