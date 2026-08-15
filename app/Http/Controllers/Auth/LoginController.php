<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Auth\LoginDTO;
use App\Http\Resources\Auth\LoginResource;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\Attributes\Controllers\Throttle;

/**
 * Controlador de Autenticação - Laravel 13
 * 
 * @see https://laravel.com/docs/13.x/controllers#attributes
 */
#[Middleware('guest')]
#[Throttle(10, 1)] // 10 tentativas por minuto
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

            // ⭐ CRIAR DTO
            $loginDTO = LoginDTO::fromRequest($request->all());

            // 🔍 Buscar no banco novo
            $user = Membro::where('matricula', $loginDTO->matricula)->first();

            if (!$user) {
                Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA', [
                    'matricula' => $loginDTO->matricula,
                    'ip' => $request->ip()
                ]);

                return back()->with('error', 'Matrícula não encontrada. Contate a secretaria.');
            }

            // 🔐 VERIFICAR SENHA
            if (!Hash::check($loginDTO->password, $user->password)) {
                Log::channel('auth')->warning('⚠️ SENHA INCORRETA', [
                    'matricula' => $loginDTO->matricula,
                    'ip' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'password' => 'Senha incorreta.',
                ]);
            }

            // 🔐 VERIFICAR STATUS
            if (!$user->isAtivo()) {
                Log::channel('auth')->warning('⚠️ CONTA INATIVA', [
                    'matricula' => $loginDTO->matricula,
                    'status' => $user->status,
                    'ip' => $request->ip()
                ]);

                return back()->with('error', "Sua conta está {$user->status}. Contate a secretaria.");
            }

            // ⭐ VERIFICAR SE O USUÁRIO TEM UM NÍVEL DEFINIDO
            if (empty($user->nivel)) {
                // Se for admin pelo campo antigo, define como admin
                if ($user->admin === true || $user->admin === 1) {
                    $user->nivel = Membro::NIVEL_ADMIN;
                } else {
                    // Por padrão, usuário comum
                    $user->nivel = Membro::NIVEL_USUARIO;
                }
                $user->save();
                
                Log::channel('auth')->info('🔄 NÍVEL DEFINIDO AUTOMATICAMENTE', [
                    'matricula' => $user->matricula,
                    'nivel' => $user->nivel,
                    'ip' => $request->ip()
                ]);
            }

            // ✅ FAZER LOGIN COM LARAVEL AUTH
            Auth::login($user, $loginDTO->remember);

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