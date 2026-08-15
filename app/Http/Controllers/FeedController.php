<?php

namespace App\Http\Controllers;

use App\Models\Membro;
use App\Models\Publicacao;
use App\Models\Comentario;
use App\Models\Curtida;
use App\DTOs\Feed\FeedDTO;
use App\DTOs\Publicacao\CreatePublicacaoDTO;
use App\Http\Resources\Feed\FeedResource;
use App\Http\Resources\Publicacao\PublicacaoResource;
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

        // 🔥 FORÇA O CARREGAMENTO DO RELACIONAMENTO
        $user->load('seguindo');
        
        // 🔥 OBTÉM IDs DOS SEGUIDOS
        $seguindoIds = $user->seguindo()->pluck('matricula')->toArray();
        
        // 🔥 SE NÃO SEGUE NINGUÉM, MOSTRA FEED GLOBAL (FALLBACK)
        if (empty($seguindoIds)) {
            Log::info('⚠️ Usuário não segue ninguém - mostrando feed global como fallback', [
                'matricula' => $user->matricula
            ]);
            
            $publicacoes = Publicacao::on('mysql')
                ->with(['autor', 'comentarios.autor', 'curtidas'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // 🔥 ADICIONA O PRÓPRIO USUÁRIO
            $seguindoIds[] = $user->matricula;
            
            $publicacoes = Publicacao::on('mysql')
                ->with(['autor', 'comentarios.autor', 'curtidas'])
                ->whereIn('filiado_matricula', $seguindoIds)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        // 🔥 ADICIONA FLAG DE CURTIDA
        $publicacoes->each(function($publicacao) use ($user) {
            $publicacao->curtida_por_mim = $publicacao->isCurtidoPor($user);
            
            if ($publicacao->autor) {
                $publicacao->autor->funcao_formatada = $publicacao->autor->funcao ?? 'Membro';
            }
        });

        // 🔥 CONFIGURA O MEMBRO PARA A VIEW
        $membro = $user;
        $membro->seguindo_count = $user->seguindo()->count();
        $membro->funcao_formatada = $user->funcao ?? 'Membro';

        // 🔥 LOG PARA DEBUG
        Log::info('📊 Feed Seguindo', [
            'matricula' => $user->matricula,
            'seguindo_count' => $membro->seguindo_count,
            'publicacoes_count' => $publicacoes->total(),
            'seguindo_ids' => $seguindoIds
        ]);

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
            
            if ($publicacao->autor) {
                $publicacao->autor->funcao_formatada = $publicacao->autor->funcao ?? 'Membro';
            }
        });

        $membro = $user;
        $membro->seguindo_count = $user->seguindo()->count();
        $membro->funcao_formatada = $user->funcao ?? 'Membro';

        Log::info('📊 Feed Global', [
            'matricula' => $user->matricula,
            'publicacoes_count' => $publicacoes->total()
        ]);

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
            // ⭐ CRIAR DTO
            $createDTO = CreatePublicacaoDTO::fromRequest([
                'filiado_matricula' => $user->matricula,
                'conteudo' => $request->conteudo
            ]);

            $publicacao = Publicacao::on('mysql')->create($createDTO->toArray());
            $publicacao->load('autor');

            Log::info('📝 Nova publicação criada', [
                'matricula' => $user->matricula,
                'publicacao_id' => $publicacao->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publicação criada com sucesso!',
                'data' => (new PublicacaoResource($publicacao))->toArray($request)
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
                $mensagem = 'Publicação descurtida';
            } else {
                Curtida::on('mysql')->create([
                    'publicacao_id' => $id,
                    'filiado_matricula' => $user->matricula,
                    'created_at' => now()
                ]);
                $curtido = true;
                $mensagem = 'Publicação curtida';
            }

            $totalCurtidas = Curtida::on('mysql')
                ->where('publicacao_id', $id)
                ->count();

            Log::info('❤️ Curtida alterada', [
                'publicacao_id' => $id,
                'matricula' => $user->matricula,
                'curtido' => $curtido
            ]);

            return response()->json([
                'success' => true,
                'message' => $mensagem,
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
                'autor_nome' => $comentario->autor->nome ?? 'Usuário',
                'autor_matricula' => $comentario->autor->matricula ?? $user->matricula,
                'autor_foto' => $comentario->autor->foto ?? null,
                'autor_inicial' => substr($comentario->autor->nome ?? 'U', 0, 1),
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
     * Deletar uma publicação
     * ⭐ VERIFICA PERMISSÃO USANDO PODE()
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

            // ⭐ VERIFICA PERMISSÃO: dono OU pode excluir membros (admin/secretário)
            if ($publicacao->filiado_matricula !== $user->matricula && !$user->pode('excluir_membro')) {
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
     * Deletar um comentário
     * ⭐ VERIFICA PERMISSÃO USANDO PODE()
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

            // ⭐ VERIFICA PERMISSÃO: dono OU pode excluir membros (admin/secretário)
            if ($comentario->filiado_matricula !== $user->matricula && !$user->pode('excluir_membro')) {
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

    /**
     * Seguir um membro
     */
    public function seguir($matricula)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            $membro = Membro::on('mysql')->findOrFail($matricula);

            if ($user->matricula === $membro->matricula) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode seguir a si mesmo.'
                ], 422);
            }

            if ($user->segue($membro)) {
                $user->seguindo()->detach($membro->matricula);
                $seguindo = false;
                $mensagem = 'Você deixou de seguir ' . $membro->nome;
            } else {
                $user->seguindo()->attach($membro->matricula, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $seguindo = true;
                $mensagem = 'Você está seguindo ' . $membro->nome;
            }

            Log::info('👥 Seguir alterado', [
                'matricula' => $user->matricula,
                'seguido_matricula' => $membro->matricula,
                'seguindo' => $seguindo
            ]);

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'seguindo' => $seguindo,
                'seguidores_count' => $membro->seguidores()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao seguir', [
                'matricula' => $user->matricula,
                'seguido_matricula' => $matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao seguir membro.'
            ], 500);
        }
    }
}