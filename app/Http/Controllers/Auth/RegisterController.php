<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Auth\RegisterDTO;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Cargos que exigem data de consagração
     */
    private array $cargosComConsagracao = [
        'Pastor-Presidente',
        'Pastor-Vice-Presidente',
        'Pastor',
        'Evangelista',
        'Presbitero',
        'Diacono',
        'Auxiliar'
    ];

    /**
     * Mapeamento de funções para níveis
     */
    private array $mapeamentoNiveis = [
        'Pastor-Presidente' => Membro::NIVEL_ADMIN,
        'Pastor-Vice-Presidente' => Membro::NIVEL_ADMIN,
        'Pastor' => Membro::NIVEL_ADMIN,
        'Evangelista' => Membro::NIVEL_SECRETARIO,
        'Presbitero' => Membro::NIVEL_SECRETARIO,
        'Diacono' => Membro::NIVEL_SECRETARIO,
        'Auxiliar' => Membro::NIVEL_SECRETARIO,
        'Secretario' => Membro::NIVEL_SECRETARIO,
        'Secretário' => Membro::NIVEL_SECRETARIO,
    ];

    /**
     * Mostrar formulário de cadastro
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }

        Log::channel('auth')->info('📄 ACESSANDO PÁGINA DE CADASTRO', [
            'ip' => request()->ip()
        ]);

        return view('auth.register');
    }

    /**
     * Processar cadastro
     */
    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('feed.index')->with('info', 'Você já está logado.');
        }

        // Gera matrícula automaticamente
        $matriculaGerada = $this->generateMatricula();

        Log::channel('auth')->info('📝 INICIANDO PROCESSO DE CADASTRO', [
            'matricula_gerada' => $matriculaGerada,
            'ip' => $request->ip()
        ]);

        try {
            // VALIDAÇÃO - REMOVIDA MATRÍCULA
            $validated = $request->validate([
                'nome' => 'required|string|max:255|min:3',
                'email' => 'required|email|max:255|unique:filiado,email',
                'telefone' => 'required|string|max:20|min:10',
                'documento' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:filiado,documento',
                    function ($attribute, $value, $fail) {
                        $limpo = preg_replace('/[^0-9]/', '', $value);
                        if (strlen($limpo) < 11) {
                            $fail('Digite um documento válido (CPF ou RG com pelo menos 11 dígitos).');
                        }
                    },
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
                'terms' => 'accepted',
                'nome_carteira' => 'nullable|string|max:100',
                'telefone2' => 'nullable|string|max:20',
                'numero' => 'nullable|string|max:20',
                'bairro' => 'nullable|string|max:255',
                'cep' => 'nullable|string|max:20',
                'mae' => 'nullable|string|max:255',
                'pai' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:500',
                'privacidade' => 'nullable|boolean',
            ], [
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

            // VALIDAR DATA DE CONSAGRAÇÃO
            $funcao = $validated['funcao'];
            $dataConsagracao = $validated['data_Consagracao'] ?? null;

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
                    $validated['data_Consagracao'] = $validated['dataBatismo'] ?? '2000-01-01';
                }
            }

            // LIMPAR DOCUMENTO
            $validated['documento'] = preg_replace('/[^0-9]/', '', $validated['documento']);

            // ADICIONAR MATRÍCULA GERADA AOS DADOS
            $validated['matricula'] = $matriculaGerada;

            // DETERMINAR NÍVEL
            $nivel = Membro::NIVEL_USUARIO;
            
            if (isset($this->mapeamentoNiveis[$funcao])) {
                $nivel = $this->mapeamentoNiveis[$funcao];
            }
            
            if (in_array($funcao, ['Secretario', 'Secretário'])) {
                $nivel = Membro::NIVEL_SECRETARIO;
            }

            // CRIAR DTO
            $registerDTO = RegisterDTO::fromRequest([
                ...$validated,
                'nivel' => $nivel,
            ]);

            Log::channel('auth')->info('🏷️ NÍVEL DEFINIDO PARA O USUÁRIO', [
                'matricula' => $registerDTO->matricula,
                'funcao' => $funcao,
                'nivel' => $nivel
            ]);

            // VERIFICAR SE USUÁRIO JÁ EXISTE POR EMAIL
            $existingUser = Membro::where('email', $registerDTO->email)->first();

            DB::beginTransaction();

            try {
                if (!$existingUser) {
                    // Criar novo usuário
                    $user = Membro::create($registerDTO->toArray());
                    
                    Log::channel('auth')->info('✅ NOVO USUÁRIO CRIADO', [
                        'matricula' => $user->matricula,
                        'nome' => $user->nome,
                        'nivel' => $nivel,
                        'email' => $user->email
                    ]);
                } else {
                    DB::rollBack();
                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->with('error', 'Este e-mail já possui uma conta ativa. Faça login.');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            // LOGIN
            Auth::login($user);
            
            session([
                'membro_logado' => $user->matricula,
                'membro_nome' => $user->nome,
                'membro_funcao' => $user->funcao,
                'membro_nivel' => $user->nivel,
            ]);
            
            session()->regenerate();

            Log::channel('auth')->info('🎉 CADASTRO REALIZADO COM SUCESSO', [
                'matricula' => $user->matricula,
                'nome' => $user->nome,
                'nivel' => $nivel
            ]);

            // MENSAGEM PERSONALIZADA COM A MATRÍCULA GERADA
            $mensagem = "✅ Cadastro realizado com sucesso! Sua matrícula é: {$user->matricula} 🎉";
            
            if ($nivel === Membro::NIVEL_ADMIN) {
                $mensagem .= ' 👑 Você tem acesso administrativo completo.';
            } elseif ($nivel === Membro::NIVEL_SECRETARIO) {
                $mensagem .= ' 📋 Você tem permissões de secretário.';
            }

            return redirect()->route('feed.index')->with('success', $mensagem);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO NO PROCESSO DE CADASTRO', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }

    /**
     * Gera matrícula automática no formato: ANO + SEQUENCIAL
     * Exemplo: 20260001, 20260002, ...
     */
    private function generateMatricula(): string
    {
        $ano = date('Y');
        
        // Busca o último membro cadastrado no ano atual
        $ultimoMembro = Membro::whereYear('created_at', $ano)
            ->orderBy('matricula', 'desc')
            ->first();
        
        if ($ultimoMembro && !empty($ultimoMembro->matricula)) {
            // Extrai o número sequencial do último (últimos 4 dígitos)
            $ultimoNumero = (int) substr($ultimoMembro->matricula, -4);
            $novoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $novoNumero = '0001';
        }
        
        return $ano . $novoNumero;
    }

    /**
     * Verificar email (AJAX)
     */
    public function verificarEmail(Request $request)
    {
        $email = $request->email;

        Log::channel('auth')->info('🔍 VERIFICANDO EMAIL', [
            'email' => $email,
            'ip' => $request->ip()
        ]);

        try {
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'available' => false,
                    'message' => 'E-mail inválido.'
                ], 400);
            }

            $user = Membro::where('email', $email)->first();
            
            if ($user) {
                return response()->json([
                    'available' => false,
                    'message' => 'Este e-mail já está cadastrado.'
                ]);
            }
            
            return response()->json([
                'available' => true,
                'message' => 'E-mail disponível para cadastro! ✅'
            ]);

        } catch (\Exception $e) {
            Log::channel('auth')->error('❌ ERRO AO VERIFICAR EMAIL', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'available' => false,
                'message' => 'Erro ao verificar e-mail. Tente novamente.'
            ], 500);
        }
    }
}