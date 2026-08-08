<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
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
            $membro = Filiado::on('mysql')->where('matricula', $matricula)->first();
            if (!$membro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membro não encontrado.'
                ], 404);
            }

            // Verifica se já segue
            $jaSegue = Seguidor::on('mysql')
                ->where('seguidor_matricula', $user->matricula)
                ->where('seguido_matricula', $matricula)
                ->exists();

            if ($jaSegue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já segue este membro.'
                ], 400);
            }

            // Cria o relacionamento
            Seguidor::on('mysql')->create([
                'seguidor_matricula' => $user->matricula,
                'seguido_matricula' => $matricula,
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
            $membro = Filiado::on('mysql')->where('matricula', $matricula)->first();
            if (!$membro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membro não encontrado.'
                ], 404);
            }

            // Verifica se segue
            $jaSegue = Seguidor::on('mysql')
                ->where('seguidor_matricula', $user->matricula)
                ->where('seguido_matricula', $matricula)
                ->exists();

            if (!$jaSegue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não segue este membro.'
                ], 400);
            }

            // Remove o relacionamento
            Seguidor::on('mysql')
                ->where('seguidor_matricula', $user->matricula)
                ->where('seguido_matricula', $matricula)
                ->delete();

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
            // Membros que o usuário já segue
            $seguindo = Seguidor::on('mysql')
                ->where('seguidor_matricula', $user->matricula)
                ->pluck('seguido_matricula')
                ->toArray();

            // Não sugerir a si mesmo nem quem já segue
            $excluir = array_merge($seguindo, [$user->matricula]);

            $sugestoes = Filiado::on('mysql')
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
            $membro = Filiado::on('mysql')->where('matricula', $matricula)->firstOrFail();

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

            $seguindo = Seguidor::on('mysql')
                ->where('seguidor_matricula', $matricula)
                ->with(['seguido' => function($query) {
                    $query->select('matricula', 'nome', 'foto', 'funcao', 'cidade');
                }])
                ->orderBy('created_at', 'desc')
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
                        'matricula' => $item->seguido->matricula,
                        'nome' => $item->seguido->nome,
                        'foto_url' => $item->seguido->foto_url,
                        'funcao' => $item->seguido->funcao ?? 'Membro',
                        'cidade' => $item->seguido->cidade,
                        'url' => route('perfil.show', $item->seguido->matricula)
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
            $membro = Filiado::on('mysql')->where('matricula', $matricula)->firstOrFail();

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

            $seguidores = Seguidor::on('mysql')
                ->where('seguido_matricula', $matricula)
                ->with(['seguidor' => function($query) {
                    $query->select('matricula', 'nome', 'foto', 'funcao', 'cidade');
                }])
                ->orderBy('created_at', 'desc')
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
                        'matricula' => $item->seguidor->matricula,
                        'nome' => $item->seguidor->nome,
                        'foto_url' => $item->seguidor->foto_url,
                        'funcao' => $item->seguidor->funcao ?? 'Membro',
                        'cidade' => $item->seguidor->cidade,
                        'url' => route('perfil.show', $item->seguidor->matricula)
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
            $segue = Seguidor::on('mysql')
                ->where('seguidor_matricula', $seguidor)
                ->where('seguido_matricula', $seguido)
                ->exists();

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
        return Seguidor::on('mysql')
            ->where('seguido_matricula', $matricula)
            ->count();
    }
}