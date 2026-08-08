<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('feed.index');
        }

        Log::channel('auth')->info('📄 ACESSANDO PÁGINA DE LOGIN', [
            'ip' => request()->ip()
        ]);

        return view('auth.login');
    }

    /**
     * Processar login com Laravel Auth
     */
    public function login(Request $request)
    {
        // Verifica se já está logado
        if (Auth::check()) {
            Log::channel('auth')->info('⏭️ TENTATIVA DE LOGIN COM SESSÃO ATIVA', [
                'matricula' => Auth::user()->matricula,
                'ip' => $request->ip()
            ]);
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }

        $startTime = microtime(true);
        Log::channel('auth')->info('🔐 INICIANDO PROCESSO DE LOGIN', [
            'matricula' => $request->matricula,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // ⭐ VALIDAÇÃO
            $request->validate([
                'matricula' => 'required|numeric|digits_between:1,10',
                'password' => 'required|string|min:8'
            ], [
                'matricula.required' => 'A matrícula é obrigatória.',
                'matricula.numeric' => 'A matrícula deve conter apenas números.',
                'matricula.digits_between' => 'A matrícula deve ter entre 1 e 10 dígitos.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.'
            ]);

            $matricula = $request->matricula;
            $password = $request->password;

            // 🔍 Buscar no banco novo
            $user = User::where('matricula', $matricula)->first();

            if (!$user) {
                Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA', [
                    'matricula' => $matricula,
                    'ip' => $request->ip()
                ]);

                return back()->with('error', 'Matrícula não encontrada. Contate a secretaria.');
            }

            // 🔐 VERIFICAR SENHA
            if (!Hash::check($password, $user->password)) {
                Log::channel('auth')->warning('⚠️ SENHA INCORRETA', [
                    'matricula' => $matricula,
                    'ip' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'password' => 'Senha incorreta.',
                ]);
            }

            // 🔐 VERIFICAR STATUS
            if (!in_array(strtolower($user->status), ['ativo', 'membro'])) {
                Log::channel('auth')->warning('⚠️ CONTA INATIVA', [
                    'matricula' => $matricula,
                    'status' => $user->status,
                    'ip' => $request->ip()
                ]);

                return back()->with('error', "Sua conta está {$user->status}. Contate a secretaria.");
            }

            // ⭐ VERIFICAR SE O USUÁRIO TEM UM NÍVEL DEFINIDO
            if (empty($user->nivel)) {
                // Se for admin pelo campo antigo, define como admin
                if ($user->admin === true || $user->admin === 1) {
                    $user->nivel = User::NIVEL_ADMIN;
                } else {
                    // Por padrão, usuário comum
                    $user->nivel = User::NIVEL_USUARIO;
                }
                $user->save();
                
                Log::channel('auth')->info('🔄 NÍVEL DEFINIDO AUTOMATICAMENTE', [
                    'matricula' => $user->matricula,
                    'nivel' => $user->nivel,
                    'ip' => $request->ip()
                ]);
            }

            // ✅ FAZER LOGIN COM LARAVEL AUTH
            Auth::login($user, $request->has('remember'));

            // ✅ MANTER COMPATIBILIDADE COM CÓDIGO EXISTENTE
            session([
                'membro_logado' => $user->matricula,
                'membro_nome' => $user->nome,
                'membro_funcao' => $user->funcao,
                'membro_nivel' => $user->nivel,
            ]);

            // ✅ REGENERAR SESSÃO POR SEGURANÇA
            $request->session()->regenerate();

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::channel('auth')->info('✅ LOGIN REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'funcao' => $user->funcao,
                'nivel' => $user->nivel,
                'execution_time_ms' => $executionTime,
                'ip' => $request->ip()
            ]);

            // ⭐ REDIRECIONA PARA O FEED COM MENSAGEM PERSONALIZADA
            $mensagem = "Bem-vindo(a) {$user->nome}!";
            
            // Adiciona mensagem especial para admins
            if ($user->isAdmin()) {
                $mensagem .= " 👑 Você tem acesso administrativo completo.";
            } elseif ($user->isSecretario()) {
                $mensagem .= " 📋 Você tem permissões de secretário.";
            }

            return redirect()->intended(route('feed.index'))
                ->with('success', $mensagem);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE LOGIN', [
                'matricula' => $request->matricula,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'execution_time_ms' => $executionTime,
                'ip' => $request->ip()
            ]);

            return back()->with('error', 'Erro ao processar login. Tente novamente.');
        }
    }

    /**
     * Logout
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
}