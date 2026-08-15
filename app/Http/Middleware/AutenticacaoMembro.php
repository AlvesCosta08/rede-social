<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Membro;
use Symfony\Component\HttpFoundation\Response;

class AutenticacaoMembro
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. TENTA USAR LARAVEL AUTH
        if (Auth::check()) {
            $user = Auth::user();
            
            // ✅ VERIFICA STATUS DO USUÁRIO
            if (!$this->isUsuarioAtivo($user)) {
                Auth::logout();
                Session::flush();
                return redirect()->route('login')
                    ->with('error', 'Sua conta está inativa. Entre em contato com o administrador.');
            }
            
            $this->sincronizarSessao($user);
            return $next($request);
        }

        // 2. VERIFICA SESSÃO LEGADA (FALLBACK)
        if (Session::has('membro_logado')) {
            $matricula = Session::get('membro_logado');
            $user = Membro::where('matricula', $matricula)->first();
            
            // ✅ VERIFICAÇÃO MAIS ROBUSTA
            if ($user && $this->isUsuarioAtivo($user)) {
                // ✅ LOGIN SEGURO COM VERIFICAÇÃO ADICIONAL
                Auth::login($user);
                $this->sincronizarSessao($user);
                
                Log::info('Usuário autenticado via sessão: ' . $user->matricula);
                return $next($request);
            }
            
            // Sessão inválida
            Log::warning('Sessão inválida para matrícula: ' . $matricula);
            Session::flush();
            return redirect()->route('login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // 3. NÃO AUTENTICADO
        return redirect()->route('login')
            ->with('error', 'Faça login para acessar esta área.');
    }

    /**
     * Verifica se o usuário está ativo
     */
    private function isUsuarioAtivo($user): bool
    {
        if (!$user) {
            return false;
        }
        
        $statusAtivos = ['ativo', 'membro', 'active', 'activated'];
        return in_array(strtolower($user->status ?? ''), $statusAtivos);
    }

    /**
     * Sincroniza a sessão com os dados do usuário
     */
    private function sincronizarSessao($user): void
    {
        Session::put([
            'membro_logado' => $user->matricula,
            'membro_nome' => $user->nome,
            'membro_funcao' => $user->funcao,
        ]);
    }
}