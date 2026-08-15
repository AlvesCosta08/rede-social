<?php

namespace App\Listeners;

use App\Events\MembroRegistered;
use App\Events\PublicacaoCreated;
use Illuminate\Support\Facades\Log;

class LogMemberActivity
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof MembroRegistered) {
            Log::channel('member')->info('👤 Novo membro registrado', [
                'matricula' => $event->membro->matricula,
                'nome' => $event->membro->nome,
                'email' => $event->membro->email,
                'ip' => request()->ip() ?? 'CLI'
            ]);
        }

        if ($event instanceof PublicacaoCreated) {
            Log::channel('member')->info('📝 Nova publicação criada', [
                'publicacao_id' => $event->publicacao->id,
                'autor_matricula' => $event->publicacao->filiado_matricula,
                'ip' => request()->ip() ?? 'CLI'
            ]);
        }
    }
}