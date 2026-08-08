<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ VERIFICA SE ESTÁ AUTENTICADO
        if (!Auth::check()) {
            Log::warning('Tentativa de acesso admin sem autenticação', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return $this->respostaNaoAutenticado($request);
        }

        $user = Auth::user();

        // ✅ VERIFICA SE O USUÁRIO ESTÁ ATIVO
        if (!$this->isUsuarioAtivo($user)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Sua conta está inativa.');
        }

        // ✅ VERIFICA SE É ADMIN (USANDO O NOVO SISTEMA)
        if (!$this->isAdmin($user)) {
            Log::warning('Tentativa de acesso admin negada', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $user->nivel ?? 'N/A',
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return $this->respostaNaoAutorizado($request);
        }

        // ✅ ADMIN AUTORIZADO
        Log::info('Acesso admin autorizado', [
            'matricula' => $user->matricula,
            'nome' => $user->nome,
            'ip' => $request->ip()
        ]);

        return $next($request);
    }

    /**
     * Verifica se o usuário é administrador
     */
    private function isAdmin($user): bool
    {
        // ⭐ PRIORIZA O NOVO SISTEMA DE NÍVEIS
        if (method_exists($user, 'isAdmin')) {
            return $user->isAdmin();
        }
        
        // MANTÉM COMPATIBILIDADE COM OS MÉTODOS ANTIGOS
        return $user->admin === true || 
               $user->admin === 1 ||
               strtolower($user->funcao ?? '') === 'administrador' ||
               strtolower($user->nivel ?? '') === 'admin';
    }

    /**
     * Verifica se o usuário está ativo
     */
    private function isUsuarioAtivo($user): bool
    {
        $statusAtivos = ['ativo', 'membro', 'active', 'activated'];
        return in_array(strtolower($user->status ?? ''), $statusAtivos);
    }

    /**
     * Resposta para usuário não autenticado
     */
    private function respostaNaoAutenticado(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Faça login para acessar esta área.'
            ], 401);
        }
        
        return redirect()->route('login')
            ->with('error', 'Faça login para acessar esta área.');
    }

    /**
     * Resposta para usuário não autorizado
     */
    private function respostaNaoAutorizado(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta área.'
            ], 403);
        }
        
        return redirect()->route('feed.index')
            ->with('error', 'Acesso restrito a administradores.');
    }
}