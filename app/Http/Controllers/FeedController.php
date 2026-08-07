<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use App\Models\Publicacao;
use App\Models\Comentario;
use App\Models\Curtida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    /**
     * Mostrar o feed do usuário (seguindo)
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para ver o feed.');
        }

        // Buscar publicações dos membros que o usuário segue + as próprias
        $seguindo = $user->seguindo()->pluck('matricula')->toArray();
        $seguindo[] = $user->matricula;

        $publicacoes = Publicacao::on('mysql')
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->whereIn('filiado_matricula', $seguindo)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Adiciona flag de curtida para cada publicação
        $publicacoes->each(function($publicacao) use ($user) {
            $publicacao->curtida_por_mim = $publicacao->isCurtidoPor($user);
        });

        // ✅ CORRIGIDO: Passa a variável $membro para a view
        $membro = $user;
        $membro->seguindo_count = $user->seguindo()->count();
        $membro->funcao_formatada = $user->funcao ?? 'Membro';

        return view('feed.index', compact('publicacoes', 'membro'));
    }

    /**
     * Feed global (todas as publicações)
     */
    public function global()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para ver o feed.');
        }

        $publicacoes = Publicacao::on('mysql')
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $publicacoes->each(function($publicacao) use ($user) {
            $publicacao->curtida_por_mim = $publicacao->isCurtidoPor($user);
        });

        // ✅ CORRIGIDO: Passa a variável $membro para a view
        $membro = $user;
        $membro->seguindo_count = $user->seguindo()->count();
        $membro->funcao_formatada = $user->funcao ?? 'Membro';

        return view('feed.global', compact('publicacoes', 'membro'));
    }

    /**
     * Criar uma nova publicação
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conteudo' => 'required|string|max:1000|min:2',
        ], [
            'conteudo.required' => 'Digite algo para publicar.',
            'conteudo.max' => 'A publicação deve ter no máximo 1000 caracteres.',
            'conteudo.min' => 'A publicação deve ter pelo menos 2 caracteres.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $publicacao = Publicacao::on('mysql')->create([
                'filiado_matricula' => $user->matricula,
                'conteudo' => $request->conteudo,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $publicacao->load('autor');

            Log::info('📝 Nova publicação criada', [
                'matricula' => $user->matricula,
                'publicacao_id' => $publicacao->id
            ]);

            // ✅ CORRIGIDO: Retorna dados completos para o AJAX
            return response()->json([
                'success' => true,
                'message' => 'Publicação criada com sucesso!',
                'id' => $publicacao->id,
                'conteudo' => $publicacao->conteudo,
                'autor_nome' => $publicacao->autor->nome,
                'autor_matricula' => $publicacao->autor->matricula,
                'autor_foto' => $publicacao->autor->foto,
                'autor_funcao' => $publicacao->autor->funcao ?? 'Membro',
                'created_at' => $publicacao->created_at->diffForHumans()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao criar publicação', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar publicação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Curtir uma publicação
     */
    public function curtir($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            $publicacao = Publicacao::on('mysql')->findOrFail($id);

            $jaCurtiu = Curtida::on('mysql')
                ->where('publicacao_id', $id)
                ->where('filiado_matricula', $user->matricula)
                ->exists();

            if ($jaCurtiu) {
                Curtida::on('mysql')
                    ->where('publicacao_id', $id)
                    ->where('filiado_matricula', $user->matricula)
                    ->delete();
                $curtido = false;
            } else {
                Curtida::on('mysql')->create([
                    'publicacao_id' => $id,
                    'filiado_matricula' => $user->matricula,
                    'created_at' => now()
                ]);
                $curtido = true;
            }

            $totalCurtidas = Curtida::on('mysql')
                ->where('publicacao_id', $id)
                ->count();

            return response()->json([
                'success' => true,
                'curtido' => $curtido,
                'curtidas' => $totalCurtidas
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao curtir', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao curtir publicação.'
            ], 500);
        }
    }

    /**
     * Comentar uma publicação
     */
    public function comentar(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conteudo' => 'required|string|max:500|min:2',
        ], [
            'conteudo.required' => 'Digite um comentário.',
            'conteudo.max' => 'O comentário deve ter no máximo 500 caracteres.',
            'conteudo.min' => 'O comentário deve ter pelo menos 2 caracteres.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $publicacao = Publicacao::on('mysql')->findOrFail($id);

            $comentario = Comentario::on('mysql')->create([
                'publicacao_id' => $id,
                'filiado_matricula' => $user->matricula,
                'conteudo' => $request->conteudo,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $comentario->load('autor');

            Log::info('💬 Novo comentário', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'comentario_id' => $comentario->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comentário adicionado!',
                'id' => $comentario->id,
                'conteudo' => $comentario->conteudo,
                'autor_nome' => $comentario->autor->nome,
                'autor_matricula' => $comentario->autor->matricula,
                'autor_foto' => $comentario->autor->foto,
                'autor_inicial' => substr($comentario->autor->nome, 0, 1),
                'created_at' => $comentario->created_at->diffForHumans()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao comentar', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao comentar publicação.'
            ], 500);
        }
    }

    /**
     * Deletar uma publicação (apenas o autor ou admin)
     */
    public function delete($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            $publicacao = Publicacao::on('mysql')->findOrFail($id);

            if ($publicacao->filiado_matricula !== $user->matricula && !$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar esta publicação.'
                ], 403);
            }

            Comentario::on('mysql')->where('publicacao_id', $id)->delete();
            Curtida::on('mysql')->where('publicacao_id', $id)->delete();
            $publicacao->delete();

            Log::info('🗑️ Publicação deletada', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publicação deletada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao deletar publicação', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar publicação.'
            ], 500);
        }
    }

    /**
     * Atualizar uma publicação (apenas o autor)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conteudo' => 'required|string|max:1000|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $publicacao = Publicacao::on('mysql')->findOrFail($id);

            if ($publicacao->filiado_matricula !== $user->matricula) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você só pode editar suas próprias publicações.'
                ], 403);
            }

            $publicacao->conteudo = $request->conteudo;
            $publicacao->updated_at = now();
            $publicacao->save();

            Log::info('✏️ Publicação atualizada', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publicação atualizada!',
                'publicacao' => [
                    'id' => $publicacao->id,
                    'conteudo' => $publicacao->conteudo,
                    'updated_at' => $publicacao->updated_at->diffForHumans()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar publicação', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar publicação.'
            ], 500);
        }
    }

    /**
     * Deletar um comentário (apenas o autor ou admin)
     */
    public function deleteComentario($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            $comentario = Comentario::on('mysql')->findOrFail($id);

            if ($comentario->filiado_matricula !== $user->matricula && !$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar este comentário.'
                ], 403);
            }

            $comentario->delete();

            Log::info('🗑️ Comentário deletado', [
                'comentario_id' => $id,
                'matricula' => $user->matricula
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comentário deletado com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao deletar comentário', [
                'comentario_id' => $id,
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar comentário.'
            ], 500);
        }
    }
}