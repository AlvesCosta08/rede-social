<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SecretarioController extends Controller
{
    /**
     * Listar todos os secretários
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        $query = Membro::where('nivel', Membro::NIVEL_SECRETARIO)
            ->orWhere('funcao', 'Secretario')
            ->orWhere('funcao', 'Secretário');

        // Busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('matricula', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('congregacao', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por congregação
        if ($request->filled('congregacao')) {
            $query->where('congregacao', 'LIKE', "%{$request->congregacao}%");
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $secretarios = $query->orderBy('nome')->paginate(30)->withQueryString();

        // Lista de membros disponíveis para promoção
        $membros = Membro::where('status', 'ativo')
            ->where('nivel', '!=', Membro::NIVEL_ADMIN)
            ->where('nivel', '!=', Membro::NIVEL_SECRETARIO)
            ->orderBy('nome')
            ->get(['matricula', 'nome', 'congregacao', 'funcao']);

        // Lista de congregações para filtro
        $congregacoes = Membro::whereNotNull('congregacao')
            ->distinct()
            ->pluck('congregacao')
            ->sort();

        $statusList = ['ativo', 'inativo', 'pendente', 'transferido', 'saida'];

        return view('admin.secretarios.index', compact('secretarios', 'membros', 'congregacoes', 'statusList'));
    }

    /**
     * Promover ou rebaixar um membro
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
            $membro = Membro::where('matricula', $matricula)->firstOrFail();

            // ⭐ Não pode modificar a si mesmo
            if ($membro->matricula == $user->matricula) {
                return back()->with('error', 'Você não pode modificar seu próprio nível.');
            }

            // Se está promovendo a secretário
            if ($request->nivel === 'secretario') {
                // ⭐ Verifica se já é admin
                if ($membro->isAdmin()) {
                    return back()->with('error', 'Administradores não podem ser rebaixados a secretários.');
                }

                // ⭐ Verifica se já é secretário
                if ($membro->isSecretario()) {
                    return back()->with('error', 'Este membro já é secretário.');
                }

                $membro->nivel = Membro::NIVEL_SECRETARIO;
                $membro->funcao = 'Secretario';
                
                // Se o secretário tem uma congregação específica
                if ($request->filled('congregacao')) {
                    $membro->congregacao = $request->congregacao;
                }
                
                $membro->save();

                Log::info('👑 USUÁRIO PROMOVIDO A SECRETÁRIO', [
                    'admin' => $user->matricula,
                    'usuario' => $membro->matricula,
                    'nome' => $membro->nome,
                    'congregacao' => $membro->congregacao,
                    'ip' => $request->ip()
                ]);

                return back()->with('success', "{$membro->nome} agora é secretário!");

            } else {
                // ⭐ Rebaixar de secretário para usuário
                if (!$membro->isSecretario()) {
                    return back()->with('error', 'Este membro não é secretário.');
                }

                $membro->nivel = Membro::NIVEL_USUARIO;
                $membro->save();

                Log::info('⬇️ USUÁRIO REBAIXADO DE SECRETÁRIO', [
                    'admin' => $user->matricula,
                    'usuario' => $membro->matricula,
                    'nome' => $membro->nome,
                    'ip' => $request->ip()
                ]);

                return back()->with('warning', "{$membro->nome} não é mais secretário.");
            }

        } catch (\Exception $e) {
            Log::error('❌ ERRO AO GERENCIAR SECRETÁRIO', [
                'admin' => $user->matricula,
                'matricula' => $matricula,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return back()->with('error', 'Erro ao processar solicitação: ' . $e->getMessage());
        }
    }

    /**
     * Remover um secretário (rebaixar para usuário)
     */
    public function destroy($matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        try {
            $membro = Membro::where('matricula', $matricula)->firstOrFail();

            // ⭐ Impedir que remova o próprio admin
            if ($membro->matricula == $user->matricula) {
                return back()->with('error', 'Você não pode remover a si mesmo.');
            }

            // ⭐ Verifica se é admin
            if ($membro->isAdmin()) {
                return back()->with('error', 'Não é possível remover um administrador.');
            }

            // ⭐ Verifica se é secretário
            if (!$membro->isSecretario()) {
                return back()->with('error', 'Este membro não é secretário.');
            }

            $nome = $membro->nome;
            $membro->nivel = Membro::NIVEL_USUARIO;
            $membro->save();

            Log::info('🗑️ SECRETÁRIO REMOVIDO', [
                'admin' => $user->matricula,
                'usuario' => $membro->matricula,
                'nome' => $nome,
                'ip' => request()->ip()
            ]);

            return redirect()->route('admin.secretarios.index')
                ->with('warning', "{$nome} não é mais secretário.");

        } catch (\Exception $e) {
            Log::error('❌ ERRO AO REMOVER SECRETÁRIO', [
                'admin' => $user->matricula,
                'matricula' => $matricula,
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            return back()->with('error', 'Erro ao processar solicitação: ' . $e->getMessage());
        }
    }

    /**
     * Estatísticas de secretários (VIEW)
     */
    public function estatisticas()
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            abort(403, 'Acesso restrito.');
        }

        $totalSecretarios = Membro::where('nivel', Membro::NIVEL_SECRETARIO)->count();
        
        $secretariosAtivos = Membro::where('nivel', Membro::NIVEL_SECRETARIO)
            ->where('status', 'ativo')
            ->count();
            
        $secretariosInativos = Membro::where('nivel', Membro::NIVEL_SECRETARIO)
            ->where('status', '!=', 'ativo')
            ->count();

        $porCongregacao = Membro::where('nivel', Membro::NIVEL_SECRETARIO)
            ->selectRaw('congregacao, count(*) as total')
            ->whereNotNull('congregacao')
            ->groupBy('congregacao')
            ->orderBy('total', 'desc')
            ->get();

        $porFuncao = Membro::where('nivel', Membro::NIVEL_SECRETARIO)
            ->selectRaw('funcao, count(*) as total')
            ->groupBy('funcao')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.secretarios.estatisticas', compact(
            'totalSecretarios',
            'secretariosAtivos',
            'secretariosInativos',
            'porCongregacao',
            'porFuncao'
        ));
    }

    /**
     * Buscar membros para promover (AJAX)
     */
    public function buscarMembros(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('gerenciar_secretarios')) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $termo = $request->get('q', '');

        $query = Membro::where('status', 'ativo')
            ->where('nivel', '!=', Membro::NIVEL_ADMIN)
            ->where('nivel', '!=', Membro::NIVEL_SECRETARIO);

        if (strlen($termo) >= 2) {
            $query->where(function($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                  ->orWhere('matricula', 'LIKE', "%{$termo}%")
                  ->orWhere('email', 'LIKE', "%{$termo}%");
            });
        }

        $membros = $query->limit(20)->get(['matricula', 'nome', 'funcao', 'congregacao']);

        return response()->json([
            'success' => true,
            'membros' => $membros->map(function($membro) {
                return [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'funcao' => $membro->funcao ?? 'Membro',
                    'congregacao' => $membro->congregacao,
                ];
            })
        ]);
    }

    /**
     * ============================================================
     * ⭐ UPLOAD DE FOTO - SECRETÁRIO
     * ============================================================
     */

    /**
     * Upload de foto (Secretário - apenas membros da sua congregação)
     */
    public function uploadFoto(Request $request, $matricula)
    {
        $user = Auth::user();
        
        if (!$user || !$user->pode('editar_membro')) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso restrito.'
            ], 403);
        }

        $membro = Membro::where('matricula', $matricula)->firstOrFail();

        // ⭐ SECRETÁRIO: verifica se o membro é da sua congregação
        if ($user->isSecretario() && $user->congregacao !== $membro->congregacao) {
            return response()->json([
                'success' => false,
                'message' => 'Você só pode editar membros da sua congregação.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|max:2048|mimes:jpeg,png,jpg,gif,webp'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo inválido. Use uma imagem de até 2MB (JPEG, PNG, JPG, GIF ou WEBP).'
            ], 422);
        }

        try {
            $file = $request->file('foto');
            $nomeArquivo = time() . '_' . $membro->matricula . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('fotos', $nomeArquivo, 'public');
            
            if (!$path) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            // Remove foto antiga
            if ($membro->foto) {
                Storage::disk('public')->delete('fotos/' . $membro->foto);
                $publicPath = public_path('storage/fotos/' . $membro->foto);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
            }

            $membro->foto = $nomeArquivo;
            $membro->save();

            Log::info('📸 Secretário atualizou foto do membro', [
                'secretario' => $user->matricula,
                'membro' => $membro->matricula,
                'arquivo' => $nomeArquivo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'foto_url' => asset('storage/fotos/' . $nomeArquivo)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro no upload (secretário)', [
                'secretario' => $user->matricula,
                'membro' => $membro->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }
}