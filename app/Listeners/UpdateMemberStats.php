<?php

namespace App\Listeners;

use App\Events\PublicacaoCreated;
use App\Events\PublicacaoDeleted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateMemberStats
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof PublicacaoCreated) {
            $this->incrementStats($event->publicacao->filiado_matricula, 'publicacoes');
        }

        if ($event instanceof PublicacaoDeleted) {
            $this->decrementStats($event->publicacao->filiado_matricula, 'publicacoes');
        }
    }

    /**
     * Incrementa estatísticas do membro
     */
    private function incrementStats(string $matricula, string $type): void
    {
        $key = "membro_stats_{$matricula}";
        
        Cache::tags(['member_stats'])->increment($key . "_{$type}");
        
        Log::debug('📊 Estatísticas incrementadas', [
            'matricula' => $matricula,
            'type' => $type
        ]);
    }

    /**
     * Decrementa estatísticas do membro
     */
    private function decrementStats(string $matricula, string $type): void
    {
        $key = "membro_stats_{$matricula}";
        
        Cache::tags(['member_stats'])->decrement($key . "_{$type}");
        
        Log::debug('📊 Estatísticas decrementadas', [
            'matricula' => $matricula,
            'type' => $type
        ]);
    }
}