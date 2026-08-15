<?php

namespace App\Observers;

use App\Models\Membro;
use App\Events\MembroRegistered;
use Illuminate\Support\Facades\Log;

class MembroObserver
{
    /**
     * Executado quando um membro é criado
     */
    public function created(Membro $membro): void
    {
        // Dispara o evento de registro
        event(new MembroRegistered($membro));

        Log::info('👤 Membro criado via Observer', [
            'matricula' => $membro->matricula,
            'nome' => $membro->nome,
            'ip' => request()->ip() ?? 'CLI'
        ]);
    }

    /**
     * Executado quando um membro é atualizado
     */
    public function updated(Membro $membro): void
    {
        Log::info('✏️ Membro atualizado via Observer', [
            'matricula' => $membro->matricula,
            'changes' => $membro->getChanges(),
            'ip' => request()->ip() ?? 'CLI'
        ]);
    }

    /**
     * Executado quando um membro é deletado
     */
    public function deleted(Membro $membro): void
    {
        Log::info('🗑️ Membro deletado via Observer', [
            'matricula' => $membro->matricula,
            'nome' => $membro->nome,
            'ip' => request()->ip() ?? 'CLI'
        ]);
    }

    /**
     * Executado quando um membro é restaurado
     */
    public function restored(Membro $membro): void
    {
        Log::info('♻️ Membro restaurado via Observer', [
            'matricula' => $membro->matricula,
            'nome' => $membro->nome,
            'ip' => request()->ip() ?? 'CLI'
        ]);
    }

    /**
     * Executado quando um membro é forçado a ser deletado
     */
    public function forceDeleted(Membro $membro): void
    {
        Log::info('💥 Membro forçado a deletar via Observer', [
            'matricula' => $membro->matricula,
            'nome' => $membro->nome,
            'ip' => request()->ip() ?? 'CLI'
        ]);
    }
}