<?php

namespace App\Services;

use App\Models\Filiado;
use App\Models\Antigo\FiliadoAntigo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SincronizacaoUsuarioService
{
    public function sincronizar($matricula)
    {
        $cacheKey = 'sincronizado_' . $matricula;
        
        if (Cache::get($cacheKey) === true) {
            return true;
        }

        if (Filiado::where('matricula', $matricula)->exists()) {
            Cache::put($cacheKey, true, 3600);
            return true;
        }

        try {
            $filiadoAntigo = FiliadoAntigo::where('matricula', $matricula)->first();
            
            if (!$filiadoAntigo) {
                Log::warning("Usuário {$matricula} não encontrado no sistema antigo");
                Cache::put($cacheKey, false, 3600);
                return false;
            }

            Filiado::create([
                'matricula' => $filiadoAntigo->matricula,
                'nome' => $filiadoAntigo->nome ?? 'Usuário sem nome',
                'foto' => $filiadoAntigo->foto ?? null,
                'funcao' => $filiadoAntigo->funcao ?? null,
                'cidade' => $filiadoAntigo->cidade ?? null,
                'uf' => $filiadoAntigo->uf ?? null,
                'data_nascimento' => $filiadoAntigo->data_nascimento ?? null,
                'telefone' => $filiadoAntigo->telefone ?? null,
                'email' => $filiadoAntigo->email ?? null,
            ]);

            Log::info("Usuário {$matricula} sincronizado com sucesso");
            Cache::put($cacheKey, true, 86400);
            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao sincronizar usuário {$matricula}: " . $e->getMessage());
            return false;
        }
    }
}