<?php

namespace App\Policies;

use App\Models\Membro;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerfilPolicy
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
     * Verifica se pode editar o perfil
     */
    public function update(Membro $user, Membro $target): bool
    {
        // Pode editar se for o próprio ou se tiver permissão
        return $user->matricula === $target->matricula || 
               $user->pode('editar_membro', $target);
    }

    /**
     * Verifica se pode ver dados privados do perfil
     */
    public function viewPrivate(Membro $user, Membro $target): bool
    {
        return $user->matricula === $target->matricula || 
               $user->pode('ver_membro', $target);
    }

    /**
     * Verifica se pode fazer upload de foto
     */
    public function uploadPhoto(Membro $user, Membro $target): bool
    {
        return $user->matricula === $target->matricula;
    }

    /**
     * Verifica se pode remover foto
     */
    public function removePhoto(Membro $user, Membro $target): bool
    {
        return $user->matricula === $target->matricula;
    }

    /**
     * Verifica se pode ver os posts de um perfil
     */
    public function viewPosts(Membro $user, Membro $target): bool
    {
        return !$target->privacidade || 
               $user->matricula === $target->matricula || 
               $user->pode('ver_membro', $target);
    }
}