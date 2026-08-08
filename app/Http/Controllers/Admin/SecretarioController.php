<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SecretarioController extends Controller
{
    /**
     * Listar todos os secretários
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        $secretarios = User::where('nivel', User::NIVEL_SECRETARIO)
            ->orWhere('funcao', 'Secretario')
            ->orWhere('funcao', 'Secretário')
            ->orderBy('nome')
            ->paginate(30);

        $membros = User::where('status', 'ativo')
            ->where('nivel', '!=', User::NIVEL_ADMIN)
            ->where('nivel', '!=', User::NIVEL_SECRETARIO)
            ->orderBy('nome')
            ->get(['matricula', 'nome', 'congregacao']);

        return view('admin.secretarios.index', compact('secretarios', 'membros'));
    }

    /**
     * Promover um membro a secretário
     */
    public function update(Request $request, $matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        $validator = Validator::make($request->all(), [
            'nivel' => 'required|in:secretario,usuario',
            'congregacao' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $membro = User::where('matricula', $matricula)->firstOrFail();

            // Se está promovendo a secretário
            if ($request->nivel === 'secretario') {
                // Verifica se já é admin
                if ($membro->isAdmin()) {
                    return back()->with('error', 'Administradores não podem ser secretários.');
                }

                $membro->nivel = User::NIVEL_SECRETARIO;
                $membro->funcao = 'Secretario';
                
                // Se o secretário tem uma congregação específica
                if ($request->filled('congregacao')) {
                    $membro->congregacao = $request->congregacao;
                }
                
                $membro->save();

                Log::info('Usuario promovido a secretario', [
                    'admin' => $user->matricula,
                    'usuario' => $membro->matricula,
                    'nome' => $membro->nome,
                    'congregacao' => $membro->congregacao
                ]);

                return back()->with('success', $membro->nome . ' agora é secretário!');

            } else {
                // Rebaixar de secretário para usuário
                $membro->nivel = User::NIVEL_USUARIO;
                $membro->save();

                Log::info('Usuario rebaixado de secretario', [
                    'admin' => $user->matricula,
                    'usuario' => $membro->matricula,
                    'nome' => $membro->nome
                ]);

                return back()->with('warning', $membro->nome . ' não é mais secretário.');
            }

        } catch (\Exception $e) {
            Log::error('Erro ao gerenciar secretario', [
                'admin' => $user->matricula,
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro ao processar solicitação.');
        }
    }

    /**
     * Remover um secretário
     */
    public function destroy($matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        try {
            $membro = User::where('matricula', $matricula)->firstOrFail();

            // Impedir que remova o próprio admin
            if ($membro->matricula == $user->matricula) {
                return back()->with('error', 'Você não pode remover a si mesmo.');
            }

            $nome = $membro->nome;
            $membro->nivel = User::NIVEL_USUARIO;
            $membro->save();

            Log::info('Secretario removido', [
                'admin' => $user->matricula,
                'usuario' => $membro->matricula,
                'nome' => $nome
            ]);

            return redirect()->route('admin.secretarios.index')
                ->with('warning', $nome . ' não é mais secretário.');

        } catch (\Exception $e) {
            Log::error('Erro ao remover secretario', [
                'admin' => $user->matricula,
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro ao processar solicitação.');
        }
    }
}
