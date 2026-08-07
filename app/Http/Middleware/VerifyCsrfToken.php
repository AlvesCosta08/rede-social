<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // ⭐ ROTAS PÚBLICAS AJAX
        'buscar-membro-antigo/*',
        'verificar-matricula/*',
        
        // ⭐ ROTAS DE LOGIN E REGISTRO
        'login',
        'register',
        
        // ⭐ LOGOUT
        'logout',
        
        // ⭐ UPLOAD DE FOTO
        'perfil/foto',
        'upload-foto',
        
        // ⭐ FEED - AÇÕES AJAX
        'feed/publicar',
        'feed/*/curtir',
        'feed/*/comentar',
        
        // ⭐ ADMIN - AÇÕES EM MASSA
        'admin/membros/bulk',
    ];
}