<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MembroController extends Controller
{
    /**
     * Mostrar o cartão do membro
     */
    public function meuCartao()
    {
        // Verificar se o usuário está logado
        if (!Session::has('membro_logado')) {
            return redirect()->route('login')->with('error', 'Faça login para acessar.');
        }

        // Buscar o membro pela matrícula da sessão
        $matricula = Session::get('membro_logado');
        $membro = Filiado::where('matricula', $matricula)->first();

        if (!$membro) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Membro não encontrado.');
        }

        // Alterado para 'membro.cartao' se você mantiver o nome do arquivo como cartao.blade.php
        return view('membro.cartao', compact('membro'));
    }

    /**
     * Mostrar perfil do membro
     */
    public function perfil()
    {
        if (!Session::has('membro_logado')) {
            return redirect()->route('login')->with('error', 'Faça login para acessar.');
        }

        $matricula = Session::get('membro_logado');
        $membro = Filiado::where('matricula', $matricula)->first();

        if (!$membro) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Membro não encontrado.');
        }

        return view('membro.perfil', compact('membro'));
    }

    /**
     * Atualizar perfil do membro
     */
    public function atualizarPerfil(Request $request)
    {
        if (!Session::has('membro_logado')) {
            return redirect()->route('login')->with('error', 'Faça login para acessar.');
        }

        $matricula = Session::get('membro_logado');
        $membro = Filiado::where('matricula', $matricula)->first();

        if (!$membro) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Membro não encontrado.');
        }

        // Validar dados
        $request->validate([
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
        ]);

        // Atualizar membro
        $membro->update([
            'telefone' => $request->telefone ?? $membro->telefone,
            'endereco' => $request->endereco ?? $membro->endereco,
            'cidade' => $request->cidade ?? $membro->cidade,
            'uf' => $request->uf ?? $membro->uf,
        ]);

        return redirect()->route('membro.perfil')->with('success', 'Perfil atualizado com sucesso!');
    }
}