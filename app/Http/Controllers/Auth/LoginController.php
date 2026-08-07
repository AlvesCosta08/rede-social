<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Antigo\FiliadoAntigo;
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

            // 🔍 PASSO 1: Buscar no banco novo
            $user = User::where('matricula', $matricula)->first();

            // 🔍 PASSO 2: Se não existe, buscar no banco antigo
            if (!$user) {
                Log::channel('auth')->info('🔍 BUSCANDO NO BANCO ANTIGO', [
                    'matricula' => $matricula,
                    'ip' => $request->ip()
                ]);

                $userAntigo = FiliadoAntigo::on('sistema_antigo')
                                ->where('matricula', $matricula)
                                ->first();

                if ($userAntigo) {
                    Log::channel('auth')->info('📝 CRIANDO USUÁRIO A PARTIR DO BANCO ANTIGO', [
                        'matricula' => $matricula,
                        'nome' => $userAntigo->nome,
                        'ip' => $request->ip()
                    ]);

                    $user = $this->migrarUsuarioAntigo($userAntigo, $password);

                    if (!$user) {
                        return back()->with('error', 'Erro ao migrar seus dados. Contate a secretaria.');
                    }
                } else {
                    Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA EM NENHUM BANCO', [
                        'matricula' => $matricula,
                        'ip' => $request->ip()
                    ]);

                    return back()->with('error', 'Matrícula não encontrada. Contate a secretaria.');
                }
            }

            // 🔐 PASSO 3: Verificar senha
            if (!Hash::check($password, $user->password)) {
                Log::channel('auth')->warning('⚠️ SENHA INCORRETA', [
                    'matricula' => $matricula,
                    'ip' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'password' => 'Senha incorreta.',
                ]);
            }

            // 🔐 PASSO 4: Verificar status
            if (!in_array(strtolower($user->status), ['ativo', 'membro'])) {
                Log::channel('auth')->warning('⚠️ CONTA INATIVA', [
                    'matricula' => $matricula,
                    'status' => $user->status,
                    'ip' => $request->ip()
                ]);

                return back()->with('error', "Sua conta está {$user->status}. Contate a secretaria.");
            }

            // ✅ PASSO 5: Fazer login com Laravel Auth
            Auth::login($user, $request->has('remember'));

            // ✅ PASSO 6: Manter compatibilidade com código existente
            session([
                'membro_logado' => $user->matricula,
                'membro_nome' => $user->nome,
                'membro_funcao' => $user->funcao,
            ]);

            // ✅ PASSO 7: Regenerar sessão por segurança
            $request->session()->regenerate();

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::channel('auth')->info('✅ LOGIN REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'funcao' => $user->funcao,
                'execution_time_ms' => $executionTime,
                'ip' => $request->ip()
            ]);

            return redirect()->intended(route('feed.index'))
                ->with('success', "Bem-vindo(a) {$user->nome}!");

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
     * Migrar usuário do banco antigo
     */
    private function migrarUsuarioAntigo($userAntigo, $password)
    {
        try {
            $dados = [
                'matricula' => $userAntigo->matricula,
                'nome' => $userAntigo->nome ?? 'Usuário',
                'nome_carteira' => $userAntigo->nome_carteira ?? $userAntigo->nome ?? null,
                'password' => Hash::make($password),
                'funcao' => $userAntigo->funcao ?? 'Membro',
                'status' => 'ativo',
                'admin' => 0,
                'cidade' => $userAntigo->cidade ?? null,
                'uf' => $userAntigo->uf ?? null,
                'endereco' => $userAntigo->endereco ?? null,
                'bairro' => $userAntigo->bairro ?? null,
                'email' => $userAntigo->email ?? null,
                'telefone' => $userAntigo->telefone ?? null,
                'documento' => $userAntigo->documento ?? null,
                'dataNascimento' => $userAntigo->dataNascimento ?? null,
                'dataBatismo' => $userAntigo->dataBatismo ?? null,
                'data_Consagracao' => $userAntigo->data_Consagracao ?? null,
                'congregacao' => $userAntigo->congregacao ?? null,
                'datCadastro' => $userAntigo->datCadastro ?? now(),
                'created_at' => $userAntigo->datCadastro ?? now(),
                'updated_at' => now(),
            ];

            // Verifica se já existe (evita duplicidade)
            $existing = User::where('matricula', $userAntigo->matricula)->first();

            if ($existing) {
                $existing->update($dados);
                return $existing;
            }

            return User::create($dados);

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO AO MIGRAR USUÁRIO', [
                'matricula' => $userAntigo->matricula,
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            return null;
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
            'ip' => $request->ip()
        ]);

        Auth::logout();
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }
}