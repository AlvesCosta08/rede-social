<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompatibilidadeMembro
{
    /**
     * Handle an incoming request.
     * 
     * ⭐ GARANTE COMPATIBILIDADE ENTRE LARAVEL AUTH E SESSÃO LEGADA
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário está autenticado via Laravel Auth
        if (Auth::check()) {
            $user = Auth::user();

            // Mantém compatibilidade com código existente
            session([
                'membro_logado' => $user->matricula,
                'membro_nome' => $user->nome,
                'membro_funcao' => $user->funcao,
            ]);
        }

        return $next($request);
    }
}