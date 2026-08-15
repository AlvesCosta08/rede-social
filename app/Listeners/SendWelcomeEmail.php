<?php

namespace App\Listeners;

use App\Events\MembroRegistered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(MembroRegistered $event): void
    {
        $membro = $event->membro;

        Log::info('📧 Email de boas-vindas preparado para: ' . $membro->email, [
            'matricula' => $membro->matricula,
            'nome' => $membro->nome
        ]);

        // ⭐ DESCOMENTE QUANDO CONFIGURAR O MAIL
        // try {
        //     Mail::to($membro->email)->send(new \App\Mail\WelcomeMail($membro));
        //     Log::info('✅ Email de boas-vindas enviado com sucesso', [
        //         'matricula' => $membro->matricula
        //     ]);
        // } catch (\Exception $e) {
        //     Log::error('❌ Erro ao enviar email de boas-vindas', [
        //         'matricula' => $membro->matricula,
        //         'error' => $e->getMessage()
        //     ]);
        // }
    }
}