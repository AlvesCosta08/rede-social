<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    /**
     * Realizar logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        Log::channel('auth')->info('🚪 REALIZANDO LOGOUT', [
            'matricula' => $user?->matricula,
            'nome' => $user?->nome,
            'nivel' => $user?->nivel,
            'ip' => $request->ip()
        ]);

        Auth::logout();
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }

    /**
     * Sair da igreja - mostrar formulário
     */
    public function showSairForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        $user = Auth::user();
        return view('auth.sair', compact('user'));
    }

    /**
     * Processar saída da igreja
     */
    public function sair(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        $user = Auth::user();
        $matricula = $user->matricula;

        Log::channel('auth')->info('🚪 INICIANDO PROCESSO DE SAÍDA DA IGREJA', [
            'matricula' => $matricula,
            'nivel' => $user->nivel,
            'ip' => $request->ip()
        ]);

        try {
            $request->validate([
                'confirmacao' => 'required|string|in:' . $user->nome
            ], [
                'confirmacao.required' => 'A confirmação é obrigatória.',
                'confirmacao.in' => 'Digite exatamente o seu nome completo para confirmar.'
            ]);

            \DB::beginTransaction();

            try {
                $user->update([
                    'status' => 'saida',
                    'data_saida' => now(),
                    'nome' => 'Membro Anônimo #' . $user->matricula,
                    'nome_carteira' => null,
                    'email' => null,
                    'telefone' => null,
                    'documento' => null,
                    'endereco' => null,
                    'bairro' => null,
                    'cidade' => null,
                    'uf' => null,
                    'bio' => null,
                    'foto' => null,
                    'password' => \Hash::make(uniqid()),
                ]);

                $user->publicacoes()->delete();
                $user->comentarios()->delete();

                \DB::commit();

                Log::channel('auth')->info('✅ SAÍDA DA IGREJA REALIZADA', [
                    'matricula' => $matricula,
                    'ip' => $request->ip()
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

            Auth::logout();
            session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Sentiremos sua falta. Que Deus te abençoe! 🙏');

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE SAÍDA', [
                'matricula' => $matricula,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->with('error', 'Erro ao processar saída. Tente novamente.');
        }
    }
}