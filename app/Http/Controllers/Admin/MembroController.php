<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Publicacao;
use App\Models\Comentario;
use App\Models\Curtida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MembroController extends Controller
{
    /**
     * Listar todos os membros (admin)
     * ⭐ VERIFICA PERMISSÃO DASHBOARD
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
        if (!$user || !$user->pode('dashboard')) {
            abort(403, 'Acesso restrito.');
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('matricula', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('documento', 'LIKE', "%{$search}%")
                  ->orWhere('telefone', 'LIKE', "%{$search}%")
                  ->orWhere('cidade', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status !== 'todos') {
                $query->where('status', $request->status);
            }
        } else {
            $query->where('status', '!=', 'saida');
        }

        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }

        if ($request->filled('congregacao')) {
            $query->where('congregacao', 'LIKE', "%{$request->congregacao}%");
        }

        $ordenar = $request->get('ordenar', 'matricula');
        $direcao = $request->get('direcao', 'desc');
        
        $camposPermitidos = ['matricula', 'nome', 'funcao', 'cidade', 'status', 'datCadastro', 'dataNascimento'];
        if (in_array($ordenar, $camposPermitidos)) {
            $query->orderBy($ordenar, $direcao);
        } else {
            $query->orderBy('matricula', 'desc');
        }

        $membros = $query->paginate(30)->withQueryString();

        $funcoes = User::whereNotNull('funcao')
            ->distinct()
            ->pluck('funcao')
            ->sort()
            ->values()
            ->toArray();

        $congregacoes = User::whereNotNull('congregacao')
            ->distinct()
            ->pluck('congregacao')
            ->sort()
            ->values()
            ->toArray();

        $stats = [
            'total' => User::count(),
            'ativos' => User::where('status', 'ativo')->count(),
            'inativos' => User::where('status', 'inativo')->count(),
            'transferidos' => User::where('status', 'transferido')->count(),
            'saida' => User::where('status', 'saida')->count(),
        ];

        $statusList = ['ativo', 'inativo', 'transferido', 'saida'];

        $totalMembros = $stats['total'];
        $totalAtivos = $stats['ativos'];
        $totalInativos = $stats['inativos'];
        $totalTransferidos = $stats['transferidos'];
        $totalSaida = $stats['saida'];

        return view('admin.membros.index', compact(
            'membros',
            'funcoes',
            'congregacoes',
            'stats',
            'statusList',
            'totalMembros',
            'totalAtivos',
            'totalInativos',
            'totalTransferidos',
            'totalSaida'
        ));
    }

    /**
     * Mostrar detalhes de um membro (admin)
     * ⭐ VERIFICA PERMISSÃO VER_MEMBRO
     */
    public function show($matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('ver_membro')) {
            abort(403, 'Acesso restrito.');
        }

        $membro = User::with(['publicacoes' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            }])
            ->where('matricula', $matricula)
            ->firstOrFail();

        $stats = [
            'total_publicacoes' => $membro->publicacoes()->count(),
            'total_comentarios' => $membro->comentarios()->count(),
            'total_seguidores' => $membro->seguidores()->count(),
            'total_seguindo' => $membro->seguindo()->count(),
        ];

        return view('admin.membros.show', compact('membro', 'stats'));
    }

    /**
     * Formulário para criar um novo membro (admin)
     * ⭐ VERIFICA PERMISSÃO CRIAR_MEMBRO
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('criar_membro')) {
            abort(403, 'Você não tem permissão para criar membros.');
        }

        $estadoCivilList = [
            'Solteiro(a)',
            'Casado(a)',
            'Divorciado(a)',
            'Viúvo(a)',
            'União Estável'
        ];

        $ufList = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
            'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
            'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        $statusList = ['ativo', 'inativo', 'transferido'];

        $congregacoes = User::whereNotNull('congregacao')
            ->distinct()
            ->pluck('congregacao')
            ->sort()
            ->values()
            ->toArray();

        if (empty($congregacoes)) {
            $congregacoes = ['Sede'];
        }

        $funcoes = User::whereNotNull('funcao')
            ->distinct()
            ->pluck('funcao')
            ->sort()
            ->values()
            ->toArray();

        if (empty($funcoes)) {
            $funcoes = [
                'Membro',
                'Pastor-Presidente',
                'Pastor-Vice-Presidente',
                'Pastor',
                'Evangelista',
                'Presbitero',
                'Diacono',
                'Auxiliar'
            ];
        }

        return view('admin.membros.create', compact(
            'estadoCivilList',
            'ufList',
            'statusList',
            'congregacoes',
            'funcoes'
        ));
    }

    /**
     * Criar um novo membro (admin)
     * ⭐ VERIFICA PERMISSÃO CRIAR_MEMBRO
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('criar_membro')) {
            abort(403, 'Você não tem permissão para criar membros.');
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|min:3',
            'nome_carteira' => 'nullable|string|max:100',
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
            'endereco' => 'required|string|max:255|min:5',
            'numero' => 'required|numeric',
            'bairro' => 'required|string|max:255',
            'cep' => 'required|string|max:20',
            'cidade' => 'required|string|max:100|min:2',
            'uf' => 'required|string|max:2|min:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'congregacao' => 'required|string|max:255|min:2',
            'funcao' => 'required|string|max:255',
            'senha' => 'required|string|min:8|confirmed',
            'status' => 'nullable|in:ativo,inativo,transferido',
            'dataBatismo' => 'nullable|date|before_or_equal:today',
            'data_Consagracao' => 'nullable|date|before_or_equal:today',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'estadoCivil' => 'nullable|string|max:50',
        ], [
            'nome.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.min' => 'Digite um telefone válido (mínimo 10 dígitos).',
            'documento.required' => 'O documento é obrigatório.',
            'documento.unique' => 'Este documento já está cadastrado.',
            'dataNascimento.required' => 'A data de nascimento é obrigatória.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'numero.numeric' => 'Digite um número válido.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'uf.required' => 'O estado (UF) é obrigatório.',
            'congregacao.required' => 'A congregação é obrigatória.',
            'funcao.required' => 'A função/cargo é obrigatória.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'senha.confirmed' => 'A confirmação da senha não coincide.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('senha', 'senha_confirmation'));
        }

        $documentoLimpo = preg_replace('/[^0-9]/', '', $request->documento);

        // ⭐ SE FOR SECRETÁRIO, FORÇA A CONGREGAÇÃO DELE
        $congregacao = $request->congregacao;
        if ($user->isSecretario()) {
            $congregacao = $user->congregacao;
        }

        try {
            // ⭐ GERA PRÓXIMA MATRÍCULA
            $ultimaMatricula = User::max('matricula') ?? 0;
            $novaMatricula = $ultimaMatricula + 1;

            $dados = [
                'matricula' => $novaMatricula,
                'nome' => $request->nome,
                'nome_carteira' => $request->nome_carteira ?? $request->nome,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'documento' => $documentoLimpo,
                'dataNascimento' => $request->dataNascimento,
                'dataBatismo' => $request->dataBatismo,
                'data_Consagracao' => $request->data_Consagracao,
                'endereco' => $request->endereco,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cep' => $request->cep,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'congregacao' => $congregacao,
                'funcao' => $request->funcao,
                'password' => Hash::make($request->senha),
                'status' => $request->status ?? 'ativo',
                'privacidade' => true,
                'nivel' => 'usuario',
                'datCadastro' => date('d/m/Y'),
                'created_at' => now(),
                'updated_at' => now(),
                'mae' => $request->mae,
                'pai' => $request->pai,
                'bio' => $request->bio,
                'estadoCivil' => $request->estadoCivil,
            ];

            $membro = User::create($dados);

            Log::info('👤 Admin criou novo membro', [
                'admin' => $user->matricula,
                'membro' => $membro->matricula,
                'nome' => $membro->nome
            ]);

            return redirect()->route('admin.membros.show', $membro->matricula)
                ->with('success', "Membro criado com sucesso! Matrícula: {$membro->matricula}");

        } catch (\Exception $e) {
            Log::error('❌ Erro ao criar membro', [
                'admin' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput($request->except('senha', 'senha_confirmation'))
                ->with('error', 'Erro ao criar membro: ' . $e->getMessage());
        }
    }

    /**
     * Editar um membro (admin)
     * ⭐ VERIFICA PERMISSÃO EDITAR_MEMBRO
     */
    public function edit($matricula)
    {
        $user = Auth::user();
        
        $membro = User::where('matricula', $matricula)->firstOrFail();
        
        // ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
        if (!$user || !$user->pode('editar_membro', $membro)) {
            abort(403, 'Você não tem permissão para editar este membro.');
        }

        $estadoCivilList = [
            'Solteiro(a)',
            'Casado(a)',
            'Divorciado(a)',
            'Viúvo(a)',
            'União Estável'
        ];

        $ufList = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
            'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
            'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        $statusList = ['ativo', 'inativo', 'transferido', 'saida'];

        $congregacoes = User::whereNotNull('congregacao')
            ->distinct()
            ->pluck('congregacao')
            ->sort()
            ->values()
            ->toArray();

        if (empty($congregacoes)) {
            $congregacoes = ['Sede'];
        }

        $funcoes = User::whereNotNull('funcao')
            ->distinct()
            ->pluck('funcao')
            ->sort()
            ->values()
            ->toArray();

        if (empty($funcoes)) {
            $funcoes = [
                'Membro',
                'Pastor-Presidente',
                'Pastor-Vice-Presidente',
                'Pastor',
                'Evangelista',
                'Presbitero',
                'Diacono',
                'Auxiliar'
            ];
        }

        return view('admin.membros.edit', compact(
            'membro',
            'estadoCivilList',
            'ufList',
            'statusList',
            'congregacoes',
            'funcoes'
        ));
    }

    /**
     * Atualizar um membro (admin)
     * ⭐ VERIFICA PERMISSÃO EDITAR_MEMBRO
     */
    public function update(Request $request, $matricula)
    {
        $user = Auth::user();
        
        $membro = User::where('matricula', $matricula)->firstOrFail();
        
        // ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
        if (!$user || !$user->pode('editar_membro', $membro)) {
            abort(403, 'Você não tem permissão para editar este membro.');
        }

        $rules = [
            'nome' => 'required|string|max:255|min:3',
            'nome_carteira' => 'nullable|string|max:100',
            'telefone' => 'required|string|max:20|min:10',
            'dataNascimento' => 'required|date|before:today|after:1900-01-01',
            'endereco' => 'required|string|max:255|min:5',
            'numero' => 'required|numeric',
            'bairro' => 'required|string|max:255',
            'cep' => 'required|string|max:20',
            'cidade' => 'required|string|max:100|min:2',
            'uf' => 'required|string|max:2|min:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'congregacao' => 'required|string|max:255|min:2',
            'funcao' => 'required|string|max:255',
            'status' => 'nullable|in:ativo,inativo,transferido,saida',
            'dataBatismo' => 'nullable|date|before_or_equal:today',
            'data_Consagracao' => 'nullable|date|before_or_equal:today',
            'admin' => 'nullable|boolean',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'estadoCivil' => 'nullable|string|max:50',
        ];

        if ($request->has('email') && $request->email !== $membro->email) {
            $rules['email'] = 'required|email|max:255|unique:filiado,email';
        } elseif ($request->has('email')) {
            $rules['email'] = 'required|email|max:255';
        }

        if ($request->has('documento') && $request->documento !== $membro->documento) {
            $rules['documento'] = [
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
            ];
        } elseif ($request->has('documento')) {
            $rules['documento'] = 'required|string|max:20';
        }

        if ($request->filled('nova_senha')) {
            $rules['nova_senha'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'nome.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.min' => 'Digite um telefone válido (mínimo 10 dígitos).',
            'documento.required' => 'O documento é obrigatório.',
            'documento.unique' => 'Este documento já está cadastrado.',
            'dataNascimento.required' => 'A data de nascimento é obrigatória.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'numero.numeric' => 'Digite um número válido.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'uf.required' => 'O estado (UF) é obrigatório.',
            'congregacao.required' => 'A congregação é obrigatória.',
            'funcao.required' => 'A função/cargo é obrigatória.',
            'nova_senha.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da senha não coincide.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('nova_senha', 'nova_senha_confirmation'));
        }

        try {
            $dadosParaAtualizar = [
                'nome' => $request->nome,
                'nome_carteira' => $request->nome_carteira,
                'telefone' => $request->telefone,
                'dataNascimento' => $request->dataNascimento,
                'endereco' => $request->endereco,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cep' => $request->cep,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'congregacao' => $request->congregacao,
                'funcao' => $request->funcao,
                'status' => $request->status ?? 'ativo',
                'dataBatismo' => $request->dataBatismo,
                'data_Consagracao' => $request->data_Consagracao,
                'admin' => $request->has('admin'),
                'updated_at' => now(),
                'mae' => $request->mae,
                'pai' => $request->pai,
                'bio' => $request->bio,
                'estadoCivil' => $request->estadoCivil,
            ];

            // ⭐ SE FOR SECRETÁRIO, NÃO PODE MUDAR A CONGREGAÇÃO
            if ($user->isSecretario()) {
                $dadosParaAtualizar['congregacao'] = $membro->congregacao;
            }

            if ($request->has('email') && $request->email !== $membro->email) {
                $dadosParaAtualizar['email'] = $request->email;
            }

            if ($request->has('documento') && $request->documento !== $membro->documento) {
                $dadosParaAtualizar['documento'] = preg_replace('/[^0-9]/', '', $request->documento);
            }

            if ($request->filled('nova_senha')) {
                $dadosParaAtualizar['password'] = Hash::make($request->nova_senha);
            }

            $membro->update($dadosParaAtualizar);

            Log::info('👤 Admin atualizou membro', [
                'admin' => $user->matricula,
                'membro' => $membro->matricula,
                'nome' => $membro->nome
            ]);

            return redirect()->route('admin.membros.show', $membro->matricula)
                ->with('success', 'Membro atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar membro', [
                'admin' => $user->matricula,
                'membro' => $membro->matricula,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput($request->except('nova_senha', 'nova_senha_confirmation'))
                ->with('error', 'Erro ao atualizar membro: ' . $e->getMessage());
        }
    }

    /**
     * Deletar um membro (admin)
     * ⭐ VERIFICA PERMISSÃO EXCLUIR_MEMBRO
     */
    public function destroy($matricula)
    {
        $user = Auth::user();
        
        // ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
        if (!$user || !$user->pode('excluir_membro')) {
            abort(403, 'Você não tem permissão para excluir membros.');
        }

        try {
            $membro = User::where('matricula', $matricula)->firstOrFail();

            if ($membro->matricula == $user->matricula) {
                return back()->with('error', 'Você não pode deletar sua própria conta.');
            }

            Publicacao::where('filiado_matricula', $matricula)->delete();
            Comentario::where('filiado_matricula', $matricula)->delete();
            Curtida::where('filiado_matricula', $matricula)->delete();
            
            if ($membro->foto) {
                $this->removerFoto($membro->foto);
            }

            $nome = $membro->nome;
            $membro->delete();

            Log::info('🗑️ Admin deletou membro', [
                'admin' => $user->matricula,
                'membro' => $matricula,
                'nome' => $nome
            ]);

            return redirect()->route('admin.membros.index')
                ->with('success', "Membro {$nome} deletado com sucesso!");

        } catch (\Exception $e) {
            Log::error('❌ Erro ao deletar membro', [
                'admin' => $user->matricula,
                'membro' => $matricula,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro ao deletar membro: ' . $e->getMessage());
        }
    }

    /**
     * Ações em massa (admin)
     */
    public function bulkAction(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('dashboard')) {
            abort(403, 'Acesso restrito.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:filiado,matricula',
            'action' => 'required|in:ativar,inativar,transferir,deletar'
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if (in_array($user->matricula, $ids) && $action === 'deletar') {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode deletar sua própria conta.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($ids as $id) {
                $membro = User::where('matricula', $id)->first();
                if (!$membro) continue;

                switch ($action) {
                    case 'ativar':
                        $membro->status = 'ativo';
                        $membro->save();
                        $count++;
                        break;
                    case 'inativar':
                        $membro->status = 'inativo';
                        $membro->save();
                        $count++;
                        break;
                    case 'transferir':
                        $membro->status = 'transferido';
                        $membro->save();
                        $count++;
                        break;
                    case 'deletar':
                        if ($id != $user->matricula) {
                            Publicacao::where('filiado_matricula', $id)->delete();
                            Comentario::where('filiado_matricula', $id)->delete();
                            Curtida::where('filiado_matricula', $id)->delete();
                            if ($membro->foto) {
                                $this->removerFoto($membro->foto);
                            }
                            $membro->delete();
                            $count++;
                        }
                        break;
                }
            }

            DB::commit();

            Log::info('📦 Ação em massa realizada', [
                'admin' => $user->matricula,
                'action' => $action,
                'count' => $count,
                'ids' => $ids
            ]);

            $messages = [
                'ativar' => "{$count} membro(s) ativado(s) com sucesso!",
                'inativar' => "{$count} membro(s) inativado(s) com sucesso!",
                'transferir' => "{$count} membro(s) transferido(s) com sucesso!",
                'deletar' => "{$count} membro(s) deletado(s) com sucesso!"
            ];

            return response()->json([
                'success' => true,
                'message' => $messages[$action] ?? 'Ação realizada com sucesso!',
                'count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Erro na ação em massa', [
                'admin' => $user->matricula,
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar ação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar membros (CSV)
     */
    public function exportar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('exportar_membros')) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $query = User::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }

        $membros = $query->orderBy('matricula')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="membros_' . date('Y-m-d_H-i') . '.csv"',
        ];

        $callback = function() use ($membros) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Matrícula',
                'Nome', 
                'Email',
                'Telefone',
                'Função',
                'Cidade',
                'UF',
                'Congregação',
                'Status',
                'Data Nascimento',
                'Data Cadastro',
                'Data Batismo',
                'Data Consagração',
                'Admin'
            ]);

            foreach ($membros as $membro) {
                fputcsv($file, [
                    $membro->matricula,
                    $membro->nome,
                    $membro->email ?? '',
                    $membro->telefone ?? '',
                    $membro->funcao ?? 'Membro',
                    $membro->cidade ?? '',
                    $membro->uf ?? '',
                    $membro->congregacao ?? '',
                    $membro->status ?? 'ativo',
                    $membro->dataNascimento?->format('d/m/Y') ?? '',
                    $membro->datCadastro?->format('d/m/Y H:i') ?? '',
                    $membro->dataBatismo?->format('d/m/Y') ?? '',
                    $membro->data_Consagracao?->format('d/m/Y') ?? '',
                    $membro->admin ? 'Sim' : 'Não'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Estatísticas (admin)
     */
    public function estatisticas()
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('dashboard')) {
            abort(403, 'Acesso restrito.');
        }

        $stats = [
            'total' => User::count(),
            'ativos' => User::where('status', 'ativo')->count(),
            'inativos' => User::where('status', 'inativo')->count(),
            'transferidos' => User::where('status', 'transferido')->count(),
            'saida' => User::where('status', 'saida')->count(),
        ];

        $porFuncao = User::selectRaw('funcao, count(*) as total')
            ->groupBy('funcao')
            ->orderBy('total', 'desc')
            ->get();

        $porCidade = User::selectRaw('cidade, count(*) as total')
            ->whereNotNull('cidade')
            ->groupBy('cidade')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $porCongregacao = User::selectRaw('congregacao, count(*) as total')
            ->whereNotNull('congregacao')
            ->groupBy('congregacao')
            ->orderBy('total', 'desc')
            ->get();

        $aniversariantes = User::whereMonth('dataNascimento', now()->month)
            ->orderByRaw('DAY(dataNascimento)')
            ->get(['matricula', 'nome', 'dataNascimento', 'foto', 'funcao']);

        return view('admin.membros.estatisticas', compact(
            'stats',
            'porFuncao',
            'porCidade',
            'porCongregacao',
            'aniversariantes'
        ));
    }

    /**
     * Listar posts de um membro (admin)
     */
    public function posts($matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('dashboard')) {
            abort(403, 'Acesso restrito.');
        }

        $membro = User::where('matricula', $matricula)->firstOrFail();

        $publicacoes = Publicacao::where('filiado_matricula', $matricula)
            ->with(['autor', 'comentarios', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.membros.posts', compact('membro', 'publicacoes'));
    }

    /**
     * Deletar um post (admin)
     */
    public function deletePost($id)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('dashboard')) {
            abort(403, 'Acesso restrito.');
        }

        try {
            $publicacao = Publicacao::findOrFail($id);
            $matricula = $publicacao->filiado_matricula;
            
            Comentario::where('publicacao_id', $id)->delete();
            Curtida::where('publicacao_id', $id)->delete();
            $publicacao->delete();

            Log::info('🗑️ Admin deletou publicação', [
                'admin' => $user->matricula,
                'publicacao_id' => $id,
                'autor' => $matricula
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publicação deletada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao deletar publicação (admin)', [
                'admin' => $user->matricula,
                'publicacao_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar publicação.'
            ], 500);
        }
    }

    /**
     * Remover foto do membro
     */
    private function removerFoto($nome)
    {
        $storagePath = storage_path('app/public/fotos/' . $nome);
        if (file_exists($storagePath)) {
            unlink($storagePath);
        }

        $publicPath = public_path('storage/fotos/' . $nome);
        if (file_exists($publicPath)) {
            unlink($publicPath);
        }
    }
}