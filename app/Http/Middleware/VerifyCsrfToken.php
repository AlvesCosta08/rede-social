<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // ✅ APENAS ROTAS GET SEM CSRF (SEGURAS)
        'buscar-membro-antigo/*',
        'verificar-matricula/*',
        
        // ✅ ROTAS DE AUTENTICAÇÃO (JÁ TEM TOKEN)
        // 'login',     // REMOVER - Deve ter CSRF
        // 'register',  // REMOVER - Deve ter CSRF
        // 'logout',    // REMOVER - Deve ter CSRF
        
        // ✅ ROTAS DE API (SE EXISTIR)
        'api/*',
        
        // ✅ WEBHOOKS (SE EXISTIR)
        // 'webhook/*',
    ];
}