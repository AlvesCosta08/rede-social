<?php

namespace App\Http\Controllers;

use App\Models\Membro; // ⭐ MUDADO de Filiado para Membro
use App\Models\Seguidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeguidorController extends Controller
{
    /**
     * Seguir um membro
     * ⭐ QUALQUER USUÁRIO AUTENTICADO PODE SEGUIR
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

        // Não pode seguir a si mesmo
        if ($user->matricula == $matricula) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode seguir a si mesmo.'
            ], 400);
        }

        try {
            // Verifica se o membro existe
            $membro = Membro::on('mysql')->where('matricula', $matricula)->first(); // ⭐ MUDADO de Filiado para Membro
            if (!$membro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membro não encontrado.'
                ], 404);
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            if ($user->segue($membro)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já segue este membro.'
                ], 400);
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            $user->seguindo()->attach($membro->matricula, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('✅ Seguindo membro', [
                'seguidor' => $user->matricula,
                'seguido' => $matricula,
                'nivel' => $user->nivel ?? 'usuario'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Agora você está seguindo ' . $membro->nome . '!',
                'seguindo' => true,
                'total_seguidores' => $this->countSeguidores($matricula)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao seguir', [
                'seguidor' => $user->matricula,
                'seguido' => $matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao seguir membro.'
            ], 500);
        }
    }

    /**
     * Deixar de seguir um membro
     */
    public function deixarSeguir($matricula)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            // Verifica se o membro existe
            $membro = Membro::on('mysql')->where('matricula', $matricula)->first(); // ⭐ MUDADO de Filiado para Membro
            if (!$membro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membro não encontrado.'
                ], 404);
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            if (!$user->segue($membro)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não segue este membro.'
                ], 400);
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            $user->seguindo()->detach($membro->matricula);

            Log::info('✅ Deixou de seguir', [
                'seguidor' => $user->matricula,
                'seguido' => $matricula,
                'nivel' => $user->nivel ?? 'usuario'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Você deixou de seguir ' . $membro->nome . '.',
                'seguindo' => false,
                'total_seguidores' => $this->countSeguidores($matricula)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao deixar de seguir', [
                'seguidor' => $user->matricula,
                'seguido' => $matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deixar de seguir membro.'
            ], 500);
        }
    }

    /**
     * Sugerir membros para seguir
     * ⭐ QUALQUER USUÁRIO AUTENTICADO PODE VER SUGESTÕES
     */
    public function sugerir()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            // ⭐ USA O RELACIONAMENTO DO MODEL
            $seguindo = $user->seguindo()->pluck('matricula')->toArray();

            // Não sugerir a si mesmo nem quem já segue
            $excluir = array_merge($seguindo, [$user->matricula]);

            $sugestoes = Membro::on('mysql') // ⭐ MUDADO de Filiado para Membro
                ->where('status', 'ativo')
                ->whereNotIn('matricula', $excluir)
                ->inRandomOrder()
                ->limit(10)
                ->get(['matricula', 'nome', 'foto', 'funcao', 'cidade']);

            Log::info('📋 Sugestões de membros', [
                'matricula' => $user->matricula,
                'total' => $sugestoes->count(),
                'nivel' => $user->nivel ?? 'usuario'
            ]);

            return response()->json([
                'success' => true,
                'sugestoes' => $sugestoes->map(function($membro) {
                    return [
                        'matricula' => $membro->matricula,
                        'nome' => $membro->nome,
                        'foto_url' => $membro->foto_url,
                        'funcao' => $membro->funcao ?? 'Membro',
                        'cidade' => $membro->cidade,
                        'url' => route('perfil.show', $membro->matricula)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao sugerir membros', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar sugestões.'
            ], 500);
        }
    }

    /**
     * Listar quem um membro está seguindo
     * ⭐ VERIFICA PERMISSÃO PARA VER SEGUINDO DE OUTROS
     */
    public function seguindo($matricula)
    {
        try {
            $membro = Membro::on('mysql')->where('matricula', $matricula)->firstOrFail(); // ⭐ MUDADO de Filiado para Membro

            // ⭐ VERIFICA SE O USUÁRIO LOGADO PODE VER SEGUINDO DE OUTROS
            $user = Auth::user();
            
            // Se o perfil for privado e não for o próprio usuário, verifica permissão
            if ($membro->privacidade && $user && $user->matricula !== $matricula) {
                if (!$user->pode('ver_membro', $membro)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este perfil é privado.'
                    ], 403);
                }
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            $seguindo = $membro->seguindo()
                ->select('matricula', 'nome', 'foto', 'funcao', 'cidade')
                ->orderBy('nome')
                ->paginate(20);

            Log::info('📋 Lista de seguindo', [
                'matricula' => $matricula,
                'total' => $seguindo->total(),
                'visualizado_por' => $user?->matricula ?? 'anonimo'
            ]);

            return response()->json([
                'success' => true,
                'membro' => [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'foto_url' => $membro->foto_url
                ],
                'total' => $seguindo->total(),
                'seguindo' => $seguindo->map(function($item) {
                    return [
                        'matricula' => $item->matricula,
                        'nome' => $item->nome,
                        'foto_url' => $item->foto_url,
                        'funcao' => $item->funcao ?? 'Membro',
                        'cidade' => $item->cidade,
                        'url' => route('perfil.show', $item->matricula)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao listar seguindo', [
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar seguindo.'
            ], 500);
        }
    }

    /**
     * Listar seguidores de um membro
     * ⭐ VERIFICA PERMISSÃO PARA VER SEGUIDORES DE OUTROS
     */
    public function seguidores($matricula)
    {
        try {
            $membro = Membro::on('mysql')->where('matricula', $matricula)->firstOrFail(); // ⭐ MUDADO de Filiado para Membro

            // ⭐ VERIFICA SE O USUÁRIO LOGADO PODE VER SEGUIDORES DE OUTROS
            $user = Auth::user();
            
            // Se o perfil for privado e não for o próprio usuário, verifica permissão
            if ($membro->privacidade && $user && $user->matricula !== $matricula) {
                if (!$user->pode('ver_membro', $membro)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este perfil é privado.'
                    ], 403);
                }
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            $seguidores = $membro->seguidores()
                ->select('matricula', 'nome', 'foto', 'funcao', 'cidade')
                ->orderBy('nome')
                ->paginate(20);

            Log::info('📋 Lista de seguidores', [
                'matricula' => $matricula,
                'total' => $seguidores->total(),
                'visualizado_por' => $user?->matricula ?? 'anonimo'
            ]);

            return response()->json([
                'success' => true,
                'membro' => [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'foto_url' => $membro->foto_url
                ],
                'total' => $seguidores->total(),
                'seguidores' => $seguidores->map(function($item) {
                    return [
                        'matricula' => $item->matricula,
                        'nome' => $item->nome,
                        'foto_url' => $item->foto_url,
                        'funcao' => $item->funcao ?? 'Membro',
                        'cidade' => $item->cidade,
                        'url' => route('perfil.show', $item->matricula)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao listar seguidores', [
                'matricula' => $matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar seguidores.'
            ], 500);
        }
    }

    /**
     * Verificar se um membro segue outro
     */
    public function verificarSegue($seguidor, $seguido)
    {
        try {
            $seguidorMembro = Membro::on('mysql')->where('matricula', $seguidor)->first(); // ⭐ MUDADO de Filiado para Membro
            $seguidoMembro = Membro::on('mysql')->where('matricula', $seguido)->first(); // ⭐ MUDADO de Filiado para Membro

            if (!$seguidorMembro || !$seguidoMembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membro não encontrado.'
                ], 404);
            }

            // ⭐ USA O RELACIONAMENTO DO MODEL
            $segue = $seguidorMembro->segue($seguidoMembro);

            return response()->json([
                'success' => true,
                'segue' => $segue
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao verificar segue', [
                'seguidor' => $seguidor,
                'seguido' => $seguido,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar.'
            ], 500);
        }
    }

    /**
     * Contar seguidores de um membro
     */
    private function countSeguidores($matricula)
    {
        return Membro::on('mysql')->where('matricula', $matricula)->first()?->seguidores()->count() ?? 0;
    }
}