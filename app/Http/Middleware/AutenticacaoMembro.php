<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutenticacaoMembro
{
    /**
     * Handle an incoming request.
     * 
     * ⭐ MANTIDO PARA COMPATIBILIDADE COM CÓDIGO LEGADO
     * ⭐ USA LARAVEL AUTH + SESSÃO
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== MIDDLEWARE AUTENTICACAO MEMBRO ===');
        Log::info('URL: ' . $request->fullUrl());

        // ⭐ 1. TENTA USAR LARAVEL AUTH PRIMEIRO
        if (Auth::check()) {
            $user = Auth::user();
            
            // Atualiza sessão para compatibilidade
            Session::put('membro_logado', $user->matricula);
            Session::put('membro_nome', $user->nome);
            Session::put('membro_funcao', $user->funcao);
            
            Log::info('USUÁRIO AUTENTICADO VIA LARAVEL AUTH: ' . $user->nome);
            return $next($request);
        }

        // ⭐ 2. FALLBACK: VERIFICA SESSÃO LEGADA
        Log::info('Session membro_logado: ' . Session::get('membro_logado'));

        if (!Session::has('membro_logado')) {
            Log::info('REDIRECIONANDO PARA LOGIN - Sessão vazia');
            return redirect()->route('login')->with('error', 'Faça login para acessar.');
        }

        $matricula = Session::get('membro_logado');
        Log::info('Matrícula da sessão: ' . $matricula);
        
        $user = User::where('matricula', $matricula)->first();
        Log::info('Usuário encontrado? ' . ($user ? 'SIM' : 'NÃO'));
        
        if ($user) {
            Log::info('Status do usuário: ' . ($user->status ?? 'null'));
        }
        
        // Verifica se o usuário existe e está ativo
        if (!$user || !in_array(strtolower($user->status), ['ativo', 'membro'])) {
            Log::warning('Usuário inválido ou inativo: ' . $matricula);
            Session::flush();
            return redirect()->route('login')->with('error', 'Sua conta está inativa.');
        }

        // ⭐ 3. LOGIN AUTOMÁTICO PARA COMPATIBILIDADE
        Auth::login($user);

        Log::info('USUÁRIO AUTENTICADO COM SUCESSO: ' . $user->nome);
        return $next($request);
    }
}