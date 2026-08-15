<?php

namespace App\Policies;

use App\Models\Membro;
use App\Models\Comentario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComentarioPolicy
{
    use HandlesAuthorization;

    /**
     * Verificação global (admin tem tudo)
     */
    public function before(Membro $user, $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Verifica se pode ver comentários
     */
    public function viewAny(Membro $user): bool
    {
        return true; // Todos podem ver
    }

    /**
     * Verifica se pode ver um comentário específico
     */
    public function view(Membro $user, Comentario $comentario): bool
    {
        return true;
    }

    /**
     * Verifica se pode criar um comentário
     */
    public function create(Membro $user): bool
    {
        return true; // Todos podem comentar
    }

    /**
     * Verifica se pode atualizar um comentário
     */
    public function update(Membro $user, Comentario $comentario): bool
    {
        return $user->matricula === $comentario->filiado_matricula;
    }

    /**
     * Verifica se pode deletar um comentário
     */
    public function delete(Membro $user, Comentario $comentario): bool
    {
        // Dono do comentário ou moderador
        return $user->matricula === $comentario->filiado_matricula || 
               $user->pode('excluir_membro');
    }
}