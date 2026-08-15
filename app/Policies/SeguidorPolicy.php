<?php

namespace App\Policies;

use App\Models\Membro;
use App\Models\Seguidor;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeguidorPolicy
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
     * Verifica se pode seguir um membro
     */
    public function follow(Membro $user, Membro $target): bool
    {
        // Não pode seguir a si mesmo
        if ($user->matricula === $target->matricula) {
            return false;
        }

        // Não pode seguir se o alvo estiver inativo
        if (!$target->isAtivo()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se pode deixar de seguir um membro
     */
    public function unfollow(Membro $user, Membro $target): bool
    {
        return $user->matricula !== $target->matricula;
    }

    /**
     * Verifica se pode ver seguidores de um membro
     */
    public function viewFollowers(Membro $user, Membro $target): bool
    {
        // Pode ver se o perfil é público ou se é o próprio
        return !$target->privacidade || 
               $user->matricula === $target->matricula || 
               $user->pode('ver_membro', $target);
    }

    /**
     * Verifica se pode ver quem um membro segue
     */
    public function viewFollowing(Membro $user, Membro $target): bool
    {
        return !$target->privacidade || 
               $user->matricula === $target->matricula || 
               $user->pode('ver_membro', $target);
    }
}