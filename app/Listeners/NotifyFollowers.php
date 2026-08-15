<?php

namespace App\Listeners;

use App\Events\PublicacaoCreated;
use Illuminate\Support\Facades\Log;

class NotifyFollowers
{
    /**
     * Handle the event.
     */
    public function handle(PublicacaoCreated $event): void
    {
        $publicacao = $event->publicacao;
        $autor = $publicacao->autor;

        if (!$autor) {
            Log::warning('⚠️ Autor não encontrado para notificação', [
                'publicacao_id' => $publicacao->id
            ]);
            return;
        }

        // Busca seguidores do autor
        $seguidores = $autor->seguidores()->get();

        Log::info('🔔 Notificação para seguidores', [
            'autor' => $autor->matricula,
            'publicacao' => $publicacao->id,
            'seguidores' => $seguidores->count()
        ]);

        // ⭐ AQUI VOCÊ PODE ADICIONAR NOTIFICAÇÕES EM TEMPO REAL
        // foreach ($seguidores as $seguidor) {
        //     // Notificar cada seguidor
        //     // Exemplo: Notification::send($seguidor, new NewPublicacaoNotification($publicacao));
        // }

        // ⭐ OU USAR BROADCASTING
        // broadcast(new NewPublicacaoNotification($publicacao))->to($seguidores->pluck('matricula'));
    }
}