<?php

namespace App\Listeners;

use App\Events\MembroRegistered;
use App\Events\MembroUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserCache
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $matricula = null;

        if ($event instanceof MembroRegistered) {
            $matricula = $event->membro->matricula;
        }

        if ($event instanceof MembroUpdated) {
            $matricula = $event->membro->matricula;
        }

        if ($matricula) {
            // Limpa cache do membro
            Cache::tags(['member'])->forget("membro_{$matricula}");
            Cache::tags(['member_stats'])->forget("membro_stats_{$matricula}");
            
            Log::debug('🧹 Cache do membro limpo', [
                'matricula' => $matricula
            ]);
        }
    }
}