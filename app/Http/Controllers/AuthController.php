<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * ⭐ CONSTANTES DE NÍVEIS
     */
    const NIVEL_USUARIO = 'usuario';
    const NIVEL_SECRETARIO = 'secretario';
    const NIVEL_ADMIN = 'admin';

    /**
     * Cargos que exigem data de consagração
     */
    private $cargosComConsagracao = [
        'Pastor-Presidente',
        'Pastor-Vice-Presidente',
        'Pastor',
        'Evangelista',
        'Presbitero',
        'Diacono',
        'Auxiliar'
    ];

    /**
     * ⭐ Mapeamento de funções para níveis
     */
    private $mapeamentoNiveis = [
        'Pastor-Presidente' => self::NIVEL_ADMIN,
        'Pastor-Vice-Presidente' => self::NIVEL_ADMIN,
        'Pastor' => self::NIVEL_ADMIN,
        'Evangelista' => self::NIVEL_SECRETARIO,
        'Presbitero' => self::NIVEL_SECRETARIO,
        'Diacono' => self::NIVEL_SECRETARIO,
        'Auxiliar' => self::NIVEL_SECRETARIO,
        'Secretario' => self::NIVEL_SECRETARIO,
        'Secretário' => self::NIVEL_SECRETARIO,
    ];

    // ============================================================
    // BUSCAR MEMBRO (AJAX) - SEM SISTEMA ANTIGO
    // ============================================================
    public function buscarMembroAntigo($matricula)
    {
        $startTime = microtime(true);
        Log::channel('auth')->info('🔍 INICIANDO BUSCA DE MEMBRO', [
            'matricula' => $matricula,
            'ip' => request()->ip()
        ]);
        
        try {
            if (empty($matricula) || !is_numeric($matricula)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matrícula inválida. Digite um número válido.'
                ], 400);
            }

            // Buscar apenas no sistema novo
            $user = User::where('matricula', $matricula)->first();
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($user) {
                Log::channel('auth')->info('✅ MEMBRO ENCONTRADO', [
                    'matricula' => $matricula,
                    'nome' => $user->nome,
                    'execution_time_ms' => $executionTime
                ]);
                
                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'dados' => [
                        'matricula' => $user->matricula,
                        'nome' => $user->nome ?? '',
                        'funcao' => $user->funcao ?? 'Membro',
                        'cidade' => $user->cidade ?? '',
                        'uf' => $user->uf ?? '',
                        'foto' => $user->foto ?? null,
                        'email' => $user->email ?? '',
                        'telefone' => $user->telefone ?? '',
                        'documento' => $user->documento ?? '',
                        'dataNascimento' => $user->dataNascimento ?? '',
                        'dataBatismo' => $user->dataBatismo ?? '',
                        'data_Consagracao' => $user->data_Consagracao ?? '',
                        'endereco' => $user->endereco ?? '',
                        'congregacao' => $user->congregacao ?? '',
                    ]
                ]);
            }
            
            Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA', [
                'matricula' => $matricula,
                'execution_time_ms' => $executionTime
            ]);
            
            return response()->json([
                'success' => true,
                'exists' => false,
                'message' => 'Favor procurar a Secretaria Geral da Sede'
            ]);
            
        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO AO BUSCAR MEMBRO', [
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados. Tente novamente mais tarde.'
            ], 500);
        }
    }

    // ============================================================
    // VERIFICAR MATRÍCULA (AJAX)
    // ============================================================
    public function verificarMatricula($matricula)
    {
        Log::channel('auth')->info('🔍 VERIFICANDO MATRÍCULA', [
            'matricula' => $matricula,
            'ip' => request()->ip()
        ]);
        
        try {
            if (empty($matricula) || !is_numeric($matricula)) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Matrícula inválida. Digite um número válido.'
                ], 400);
            }

            $user = User::where('matricula', $matricula)->first();
            
            if ($user) {
                $jaAtivado = !empty($user->password);
                
                return response()->json([
                    'exists' => true,
                    'status' => $user->status,
                    'nome' => $user->nome,
                    'ja_ativado' => $jaAtivado,
                    'message' => $jaAtivado ? 'Esta conta já foi ativada. Faça login.' : 'Matrícula válida!'
                ]);
            }
            
            return response()->json([
                'exists' => false,
                'message' => 'Favor procurar a Secretaria Geral da Sede'
            ]);
            
        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO AO VERIFICAR MATRÍCULA', [
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'exists' => false,
                'message' => 'Erro ao verificar matrícula. Tente novamente.'
            ], 500);
        }
    }

    // ============================================================
    // MOSTRAR CADASTRO
    // ============================================================
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }
        
        return view('auth.register');
    }

    // ============================================================
    // PROCESSAR CADASTRO
    // ============================================================
    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }

        Log::channel('auth')->info('📝 INICIANDO PROCESSO DE CADASTRO', [
            'matricula' => $request->matricula,
            'ip' => request()->ip()
        ]);

        try {
            // Verificar se matrícula já existe no banco novo
            $userExistente = User::where('matricula', $request->matricula)->first();
            if ($userExistente && !empty($userExistente->password)) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Esta matrícula já possui uma conta ativa. Faça login.');
            }

            // Validação
            $validator = Validator::make($request->all(), [
                'matricula' => 'required|numeric|digits_between:1,10',
                'nome' => 'required|string|max:255|min:3',
                'email' => 'required|email|max:255|unique:filiado,email',
                'telefone' => 'required|string|max:20|min:10',
                'documento' => [
                    'required',
                    'string',
                    'max:20',
                    function ($attribute, $value, $fail) {
                        $limpo = preg_replace('/[^0-9]/', '', $value);
                        if (strlen($limpo) < 11) {
                            $fail('Digite um documento válido (CPF ou RG com pelo menos 11 dígitos).');
                        }
                    },
                    'unique:filiado,documento'
                ],
                'dataNascimento' => 'required|date|before:today|after:1900-01-01',
                'dataBatismo' => 'nullable|date|before_or_equal:today',
                'endereco' => 'required|string|max:255|min:5',
                'cidade' => 'required|string|max:100|min:2',
                'uf' => 'required|string|max:2|min:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
                'congregacao' => 'required|string|max:255|min:2',
                'funcao' => 'required|string|in:Membro,Pastor-Presidente,Pastor-Vice-Presidente,Pastor,Evangelista,Presbitero,Diacono,Auxiliar,Secretario,Secretário',
                'data_Consagracao' => 'nullable|date|before_or_equal:today',
                'password' => 'required|string|min:8|confirmed',
                'terms' => 'accepted'
            ], [
                'matricula.required' => 'A matrícula é obrigatória.',
                'matricula.numeric' => 'A matrícula deve conter apenas números.',
                'matricula.digits_between' => 'A matrícula deve ter entre 1 e 10 dígitos.',
                'nome.required' => 'O nome completo é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Digite um e-mail válido.',
                'email.unique' => 'Este e-mail já está cadastrado.',
                'telefone.required' => 'O telefone é obrigatório.',
                'telefone.min' => 'Digite um telefone válido (mínimo 10 dígitos).',
                'documento.required' => 'O documento é obrigatório.',
                'documento.unique' => 'Este documento já está cadastrado.',
                'dataNascimento.required' => 'A data de nascimento é obrigatória.',
                'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
                'dataNascimento.after' => 'Data de nascimento inválida.',
                'endereco.required' => 'O endereço é obrigatório.',
                'endereco.min' => 'Digite um endereço válido.',
                'cidade.required' => 'A cidade é obrigatória.',
                'cidade.min' => 'Digite uma cidade válida.',
                'uf.required' => 'O estado (UF) é obrigatório.',
                'uf.in' => 'Selecione um estado (UF) válido.',
                'congregacao.required' => 'A congregação é obrigatória.',
                'funcao.required' => 'A função/cargo é obrigatória.',
                'funcao.in' => 'Selecione uma função/cargo válido.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
                'password.confirmed' => 'A confirmação da senha não coincide.',
                'terms.accepted' => 'Você precisa aceitar os termos de uso.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
            }

            // Valida data de consagração
            $funcao = $request->funcao;
            $dataConsagracao = $request->data_Consagracao;

            if (in_array($funcao, $this->cargosComConsagracao)) {
                if (empty($dataConsagracao)) {
                    return back()
                        ->withErrors(['data_Consagracao' => "A Data de Consagração é obrigatória para o cargo {$funcao}."])
                        ->withInput($request->except('password', 'password_confirmation'));
                }

                try {
                    $dataConsagracaoObj = \Carbon\Carbon::parse($dataConsagracao);
                    if ($dataConsagracaoObj->isFuture()) {
                        return back()
                            ->withErrors(['data_Consagracao' => 'A Data de Consagração não pode ser uma data futura.'])
                            ->withInput($request->except('password', 'password_confirmation'));
                    }
                } catch (\Exception $e) {
                    return back()
                        ->withErrors(['data_Consagracao' => 'Data de Consagração inválida.'])
                        ->withInput($request->except('password', 'password_confirmation'));
                }
            } else {
                if (empty($dataConsagracao)) {
                    $dataConsagracao = $request->dataBatismo ?: '2000-01-01';
                }
                $request->merge(['data_Consagracao' => $dataConsagracao]);
            }

            $documentoLimpo = preg_replace('/[^0-9]/', '', $request->documento);

            // ⭐ DETERMINA O NÍVEL BASEADO NA FUNÇÃO
            $nivel = self::NIVEL_USUARIO;
            
            // Verifica se a função tem um nível mapeado
            if (isset($this->mapeamentoNiveis[$funcao])) {
                $nivel = $this->mapeamentoNiveis[$funcao];
            }
            
            // Se for Secretário, define como secretário
            if (in_array($funcao, ['Secretario', 'Secretário'])) {
                $nivel = self::NIVEL_SECRETARIO;
            }

            Log::channel('auth')->info('🏷️ NÍVEL DEFINIDO PARA O USUÁRIO', [
                'matricula' => $request->matricula,
                'funcao' => $funcao,
                'nivel' => $nivel
            ]);

            $dadosParaSalvar = [
                'matricula' => $request->matricula,
                'nome' => $request->nome,
                'nome_carteira' => $request->nome,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'documento' => $documentoLimpo,
                'dataNascimento' => $request->dataNascimento,
                'dataBatismo' => $request->dataBatismo,
                'data_Consagracao' => $request->data_Consagracao,
                'endereco' => $request->endereco,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'congregacao' => $request->congregacao,
                'funcao' => $request->funcao,
                'password' => Hash::make($request->password),
                'status' => 'ativo',
                'privacidade' => true,
                'nivel' => $nivel,
                'created_at' => now(),
                'updated_at' => now(),
                'datCadastro' => now(),
            ];

            DB::beginTransaction();

            try {
                $user = User::where('matricula', $request->matricula)->first();

                if (!$user) {
                    $user = User::create($dadosParaSalvar);
                    Log::channel('auth')->info('✅ NOVO USUÁRIO CRIADO', [
                        'matricula' => $user->matricula,
                        'nome' => $user->nome,
                        'nivel' => $nivel
                    ]);
                } else if (empty($user->password)) {
                    unset($dadosParaSalvar['matricula']);
                    $user->update($dadosParaSalvar);
                    Log::channel('auth')->info('✅ USUÁRIO ATUALIZADO', [
                        'matricula' => $user->matricula,
                        'nome' => $user->nome,
                        'nivel' => $nivel
                    ]);
                } else {
                    DB::rollBack();
                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->with('error', 'Esta matrícula já possui uma conta ativa. Faça login.');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            // ⭐ LOGIN
            Auth::login($user);
            
            // ⭐ SESSÃO PARA COMPATIBILIDADE
            $this->atualizarSessao($user);
            
            Session::regenerate();

            Log::channel('auth')->info('🎉 CADASTRO REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $nivel
            ]);

            // ⭐ MENSAGEM PERSONALIZADA POR NÍVEL
            $mensagem = 'Conta ativada com sucesso! Bem-vindo(a)! 🎉';
            if ($nivel === self::NIVEL_ADMIN) {
                $mensagem .= ' 👑 Você tem acesso administrativo completo.';
            } elseif ($nivel === self::NIVEL_SECRETARIO) {
                $mensagem .= ' 📋 Você tem permissões de secretário.';
            }

            return redirect()->route('feed.index')->with('success', $mensagem);

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE CADASTRO', [
                'matricula' => $request->matricula,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }

    // ============================================================
    // MOSTRAR LOGIN
    // ============================================================
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }
        
        return view('auth.login');
    }

    // ============================================================
    // PROCESSAR LOGIN - SEM SISTEMA ANTIGO
    // ============================================================
    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }

        Log::channel('auth')->info('🔐 INICIANDO PROCESSO DE LOGIN', [
            'matricula' => $request->matricula,
            'ip' => request()->ip()
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'matricula' => 'required|numeric|digits_between:1,10',
                'password' => 'required|string|min:8',
                'remember' => 'nullable|boolean'
            ], [
                'matricula.required' => 'A matrícula é obrigatória.',
                'matricula.numeric' => 'A matrícula deve conter apenas números.',
                'matricula.digits_between' => 'A matrícula deve ter entre 1 e 10 dígitos.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->except('password'));
            }

            // Buscar usuário apenas no sistema novo
            $user = User::where('matricula', $request->matricula)->first();
            
            if (!$user) {
                Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA', [
                    'matricula' => $request->matricula,
                    'ip' => request()->ip()
                ]);
                return back()->with('error', 'Favor procurar a Secretaria Geral da Sede');
            }

            // Verificar senha
            if (!Hash::check($request->password, $user->password)) {
                Log::channel('auth')->warning('⚠️ SENHA INCORRETA', [
                    'matricula' => $request->matricula,
                    'ip' => request()->ip()
                ]);
                return back()->with('error', 'Matrícula ou senha incorretos!');
            }

            // Verificar status
            if (!in_array(strtolower($user->status), ['ativo', 'membro'])) {
                Log::channel('auth')->warning('⚠️ CONTA INATIVA', [
                    'matricula' => $request->matricula,
                    'status' => $user->status,
                    'ip' => request()->ip()
                ]);
                return back()->with('error', 'Sua conta está ' . $user->status . '. Contate a secretaria.');
            }

            // ⭐ VERIFICA SE O USUÁRIO TEM NÍVEL DEFINIDO
            if (empty($user->nivel)) {
                // Se for admin pelo campo antigo, define como admin
                if ($user->admin === true || $user->admin === 1) {
                    $user->nivel = self::NIVEL_ADMIN;
                } else {
                    $user->nivel = self::NIVEL_USUARIO;
                }
                $user->save();
                
                Log::channel('auth')->info('🔄 NÍVEL DEFINIDO AUTOMATICAMENTE', [
                    'matricula' => $user->matricula,
                    'nivel' => $user->nivel
                ]);
            }

            // ⭐ LOGIN
            Auth::login($user, $request->remember ?? false);
            
            // ⭐ SESSÃO PARA COMPATIBILIDADE
            $this->atualizarSessao($user);
            
            Session::regenerate();

            Log::channel('auth')->info('✅ LOGIN REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $user->nivel
            ]);

            // ⭐ MENSAGEM PERSONALIZADA POR NÍVEL
            $mensagem = "Bem-vindo(a) {$user->nome}!";
            if ($user->isAdmin()) {
                $mensagem .= " 👑 Você tem acesso administrativo completo.";
            } elseif ($user->isSecretario()) {
                $mensagem .= " 📋 Você tem permissões de secretário.";
            }

            return redirect()->route('feed.index')->with('success', $mensagem);

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE LOGIN', [
                'matricula' => $request->matricula,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erro ao processar login. Tente novamente.');
        }
    }

    // ============================================================
    // MÉTODO PARA ATUALIZAR SESSÃO
    // ============================================================
    private function atualizarSessao($user)
    {
        Session::put('membro_logado', $user->matricula);
        Session::put('membro_nome', $user->nome);
        Session::put('membro_funcao', $user->funcao);
        Session::put('membro_foto', $user->foto);
        Session::put('membro_nivel', $user->nivel ?? self::NIVEL_USUARIO);
    }

    // ============================================================
    // LOGOUT
    // ============================================================
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::channel('auth')->info('🚪 LOGOUT', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $user->nivel
            ]);
        }
        
        Auth::logout();
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }

    // ============================================================
    // SAIR DA IGREJA
    // ============================================================
    public function showSair()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }
        
        $user = Auth::user();
        return view('auth.sair', compact('user'));
    }

    public function sair(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }
        
        $user = Auth::user();
        $matricula = $user->matricula;
        
        Log::channel('auth')->info('🚪 INICIANDO PROCESSO DE SAÍDA DA IGREJA', [
            'matricula' => $matricula,
            'nivel' => $user->nivel
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'confirmacao' => 'required|string|in:' . $user->nome
            ], [
                'confirmacao.required' => 'A confirmação é obrigatória.',
                'confirmacao.in' => 'Digite exatamente o seu nome completo para confirmar.'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            DB::beginTransaction();

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
                    'password' => Hash::make(uniqid()),
                ]);

                $user->publicacoes()->delete();
                $user->comentarios()->delete();

                DB::commit();

                Log::channel('auth')->info('✅ SAÍDA DA IGREJA REALIZADA', [
                    'matricula' => $matricula
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            Auth::logout();
            Session::flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Sentiremos sua falta. Que Deus te abençoe! 🙏');

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE SAÍDA', [
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erro ao processar saída. Tente novamente.');
        }
    }
}