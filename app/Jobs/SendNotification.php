<?php

namespace App\Jobs;

use App\Models\Membro;
use App\Models\Publicacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Membro $recipient,
        public readonly string $type,
        public readonly ?Publicacao $publicacao = null,
        public readonly ?Membro $sender = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🔔 Enviando notificação', [
                'to' => $this->recipient->matricula,
                'type' => $this->type
            ]);

            switch ($this->type) {
                case 'new_post':
                    $this->notifyNewPost();
                    break;
                case 'new_follower':
                    $this->notifyNewFollower();
                    break;
                case 'new_comment':
                    $this->notifyNewComment();
                    break;
                case 'new_like':
                    $this->notifyNewLike();
                    break;
                default:
                    Log::warning('⚠️ Tipo de notificação desconhecido', [
                        'type' => $this->type
                    ]);
            }

            Log::info('✅ Notificação enviada com sucesso', [
                'to' => $this->recipient->matricula,
                'type' => $this->type
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao enviar notificação', [
                'to' => $this->recipient->matricula,
                'type' => $this->type,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifica sobre nova publicação
     */
    private function notifyNewPost(): void
    {
        // ⭐ AQUI VOCÊ PODE ADICIONAR NOTIFICAÇÕES EM TEMPO REAL
        // Exemplo: broadcast(new NewPostNotification($this->recipient, $this->publicacao));
        // Exemplo: DatabaseNotification::create([...]);
    }

    /**
     * Notifica sobre novo seguidor
     */
    private function notifyNewFollower(): void
    {
        // ⭐ AQUI VOCÊ PODE ADICIONAR NOTIFICAÇÕES EM TEMPO REAL
    }

    /**
     * Notifica sobre novo comentário
     */
    private function notifyNewComment(): void
    {
        // ⭐ AQUI VOCÊ PODE ADICIONAR NOTIFICAÇÕES EM TEMPO REAL
    }

    /**
     * Notifica sobre nova curtida
     */
    private function notifyNewLike(): void
    {
        // ⭐ AQUI VOCÊ PODE ADICIONAR NOTIFICAÇÕES EM TEMPO REAL
    }
}