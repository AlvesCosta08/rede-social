<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Antigo\FiliadoAntigo;
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

    // ============================================================
    // BUSCAR MEMBRO NO BANCO ANTIGO (AJAX)
    // ============================================================
    public function buscarMembroAntigo($matricula)
    {
        $startTime = microtime(true);
        Log::channel('auth')->info('🔍 INICIANDO BUSCA DE MEMBRO ANTIGO', [
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

            $filiadoAntigo = FiliadoAntigo::on('sistema_antigo')
                                ->where('matricula', $matricula)
                                ->first();
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($filiadoAntigo) {
                $existeNoNovo = User::where('matricula', $matricula)->exists();
                
                Log::channel('auth')->info('✅ MEMBRO ENCONTRADO NO BANCO ANTIGO', [
                    'matricula' => $matricula,
                    'nome' => $filiadoAntigo->nome,
                    'execution_time_ms' => $executionTime
                ]);
                
                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'existe_no_novo' => $existeNoNovo,
                    'dados' => [
                        'matricula' => $filiadoAntigo->matricula,
                        'nome' => $filiadoAntigo->nome ?? '',
                        'funcao' => $filiadoAntigo->funcao ?? 'Membro',
                        'cidade' => $filiadoAntigo->cidade ?? '',
                        'uf' => $filiadoAntigo->uf ?? '',
                        'foto' => $filiadoAntigo->foto ?? null,
                        'email' => $filiadoAntigo->email ?? '',
                        'telefone' => $filiadoAntigo->telefone ?? '',
                        'documento' => $filiadoAntigo->documento ?? '',
                        'dataNascimento' => $filiadoAntigo->dataNascimento ?? '',
                        'dataBatismo' => $filiadoAntigo->dataBatismo ?? '',
                        'data_Consagracao' => $filiadoAntigo->data_Consagracao ?? '',
                        'endereco' => $filiadoAntigo->endereco ?? '',
                        'congregacao' => $filiadoAntigo->congregacao ?? '',
                    ]
                ]);
            }
            
            Log::channel('auth')->warning('⚠️ MATRÍCULA NÃO ENCONTRADA NO BANCO ANTIGO', [
                'matricula' => $matricula,
                'execution_time_ms' => $executionTime
            ]);
            
            return response()->json([
                'success' => true,
                'exists' => false,
                'message' => 'Favor procurar a Secretaria Geral da Sede'
            ]);
            
        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO AO BUSCAR MEMBRO ANTIGO', [
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
            
            $filiadoAntigo = FiliadoAntigo::on('sistema_antigo')
                                ->where('matricula', $matricula)
                                ->first();
            
            if ($filiadoAntigo) {
                return response()->json([
                    'exists' => true,
                    'status' => 'ativo',
                    'nome' => $filiadoAntigo->nome,
                    'ja_ativado' => false,
                    'message' => 'Matrícula validada! Complete seu cadastro.'
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
                'funcao' => 'required|string|in:Membro,Pastor-Presidente,Pastor-Vice-Presidente,Pastor,Evangelista,Presbitero,Diacono,Auxiliar',
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

            // Verifica no banco antigo
            $filiadoAntigo = FiliadoAntigo::on('sistema_antigo')
                                ->where('matricula', $request->matricula)
                                ->first();
            
            if (!$filiadoAntigo) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Favor procurar a Secretaria Geral da Sede');
            }

            $documentoLimpo = preg_replace('/[^0-9]/', '', $request->documento);

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
                        'nome' => $user->nome
                    ]);
                } else if (empty($user->password)) {
                    unset($dadosParaSalvar['matricula']);
                    $user->update($dadosParaSalvar);
                    Log::channel('auth')->info('✅ USUÁRIO ATUALIZADO', [
                        'matricula' => $user->matricula,
                        'nome' => $user->nome
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
            
            // ⭐ SESSÃO PARA COMPATIBILIDADE (views ainda usam session)
            $this->atualizarSessao($user);
            
            Session::regenerate();

            Log::channel('auth')->info('🎉 CADASTRO REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome
            ]);

            return redirect()->route('feed.index')->with('success', 'Conta ativada com sucesso! Bem-vindo(a)! 🎉');

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
    // PROCESSAR LOGIN (MELHORADO)
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

            // Buscar usuário
            $user = User::where('matricula', $request->matricula)->first();
            
            if (!$user) {
                // Tenta migrar do banco antigo
                $userAntigo = FiliadoAntigo::on('sistema_antigo')
                                    ->where('matricula', $request->matricula)
                                    ->first();
                
                if ($userAntigo) {
                    $user = $this->migrarUsuario($userAntigo, $request->password);
                } else {
                    return back()->with('error', 'Favor procurar a Secretaria Geral da Sede');
                }
            }

            // Verificar senha
            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Matrícula ou senha incorretos!');
            }

            // Verificar status
            if (!in_array(strtolower($user->status), ['ativo', 'membro'])) {
                return back()->with('error', 'Sua conta está ' . $user->status . '. Contate a secretaria.');
            }

            // ⭐ LOGIN
            Auth::login($user, $request->remember ?? false);
            
            // ⭐ SESSÃO PARA COMPATIBILIDADE
            $this->atualizarSessao($user);
            
            Session::regenerate();

            Log::channel('auth')->info('✅ LOGIN REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome
            ]);

            return redirect()->route('feed.index')->with('success', "Bem-vindo(a) {$user->nome}!");

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE LOGIN', [
                'matricula' => $request->matricula,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erro ao processar login. Tente novamente.');
        }
    }

    // ============================================================
    // ⭐ NOVO: MÉTODO PARA MIGRAR USUÁRIO
    // ============================================================
    private function migrarUsuario($userAntigo, $password)
    {
        Log::channel('auth')->info('📝 MIGRANDO USUÁRIO DO BANCO ANTIGO', [
            'matricula' => $userAntigo->matricula,
            'nome' => $userAntigo->nome
        ]);

        $dados = [
            'matricula' => $userAntigo->matricula,
            'nome' => $userAntigo->nome ?? 'Usuário',
            'password' => Hash::make($password),
            'funcao' => $userAntigo->funcao ?? 'Membro',
            'status' => 'ativo',
            'created_at' => now(),
            'updated_at' => now(),
            'datCadastro' => $userAntigo->datCadastro ?? now(),
        ];

        // Copia dados se existirem
        $campos = ['email', 'telefone', 'documento', 'cidade', 'uf', 'endereco', 'foto'];
        foreach ($campos as $campo) {
            if (!empty($userAntigo->$campo)) {
                $dados[$campo] = $campo === 'documento' 
                    ? preg_replace('/[^0-9]/', '', $userAntigo->$campo) 
                    : $userAntigo->$campo;
            }
        }

        return User::create($dados);
    }

    // ============================================================
    // ⭐ NOVO: MÉTODO PARA ATUALIZAR SESSÃO
    // ============================================================
    private function atualizarSessao($user)
    {
        Session::put('membro_logado', $user->matricula);
        Session::put('membro_nome', $user->nome);
        Session::put('membro_funcao', $user->funcao);
        Session::put('membro_foto', $user->foto);
    }

    // ============================================================
    // LOGOUT (MELHORADO)
    // ============================================================
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::channel('auth')->info('🚪 LOGOUT', [
                'matricula' => $user->matricula,
                'nome' => $user->nome
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
            'matricula' => $matricula
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