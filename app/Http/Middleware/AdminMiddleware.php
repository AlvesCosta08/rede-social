<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ✅ VERIFICA SE ESTÁ AUTENTICADO
        if (!Auth::check()) {
            Log::warning('🔒 Tentativa de acesso admin sem autenticação', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faça login para acessar esta área.'
                ], 401);
            }
            
            return redirect()->route('login')->with('error', 'Faça login para acessar esta área.');
        }

        $user = Auth::user();

        // ✅ VERIFICA SE É ADMIN USANDO A TRAIT
        if (!$user->isAdmin()) {
            Log::warning('🔒 Tentativa de acesso admin negada', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar esta área.'
                ], 403);
            }
            
            return redirect()->route('feed.index')->with('error', 'Você não tem permissão para acessar esta área.');
        }

        // ✅ ADMIN AUTORIZADO
        Log::info('🔐 Acesso admin autorizado', [
            'matricula' => $user->matricula,
            'nome' => $user->nome,
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        return $next($request);
    }
}