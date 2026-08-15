<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Membro; // ⭐ ADICIONADO

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        // Verifica se o usuário está logado
        if (!$user) {
            Log::warning('Tentativa de acesso sem autenticação', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'permission' => $permission
            ]);
            
            return $this->respostaNaoAutenticado($request);
        }

        // Busca o membro da rota (se existir)
        $membro = null;
        
        // Tenta pegar da rota
        if ($request->route('membro')) {
            $membro = $request->route('membro');
        } elseif ($request->route('matricula')) {
            $membro = Membro::where('matricula', $request->route('matricula'))->first(); // ⭐ MUDADO de Filiado para Membro
        }

        // Verifica a permissão usando o método do Membro // ⭐ CORRIGIDO comentário
        if (!$user->pode($permission, $membro)) {
            Log::warning('Permissão negada', [
                'user' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $user->nivel,
                'permission' => $permission,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return $this->respostaNaoAutorizado($request);
        }

        // Permissão concedida
        Log::info('Permissão concedida', [
            'user' => $user->matricula,
            'nome' => $user->nome,
            'nivel' => $user->nivel,
            'permission' => $permission
        ]);

        return $next($request);
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
                'message' => 'Você não tem permissão para realizar esta ação.'
            ], 403);
        }
        
        return redirect()->back()
            ->with('error', 'Você não tem permissão para realizar esta ação.');
    }
}