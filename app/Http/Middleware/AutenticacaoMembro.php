<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Filiado;

class AutenticacaoMembro
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('membro_logado')) {
            return redirect()->route('login')->with('error', 'Faça login para acessar.');
        }

        $membro = Filiado::find(Session::get('membro_logado'));
        
        if (!$membro || !in_array(strtolower($membro->status), ['ativo', 'membro'])) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Sua conta está inativa.');
        }

        return $next($request);
    }
}