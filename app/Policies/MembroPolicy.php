<?php

namespace App\Policies;

use App\Models\Membro;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class MembroPolicy
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
     * Verifica se pode ver a lista de membros
     */
    public function viewAny(Membro $user): bool
    {
        return $user->pode('ver_membros');
    }

    /**
     * Verifica se pode ver um membro específico
     */
    public function view(Membro $user, Membro $membro): bool
    {
        return $user->pode('ver_membro', $membro);
    }

    /**
     * Verifica se pode criar um membro
     */
    public function create(Membro $user): bool
    {
        return $user->pode('criar_membro');
    }

    /**
     * Verifica se pode atualizar um membro
     */
    public function update(Membro $user, Membro $membro): bool
    {
        return $user->pode('editar_membro', $membro);
    }

    /**
     * Verifica se pode deletar um membro
     */
    public function delete(Membro $user, Membro $membro): bool
    {
        return $user->pode('excluir_membro', $membro);
    }

    /**
     * Verifica se pode restaurar um membro
     */
    public function restore(Membro $user, Membro $membro): bool
    {
        return $user->isAdmin();
    }

    /**
     * Verifica se pode forçar a exclusão de um membro
     */
    public function forceDelete(Membro $user, Membro $membro): bool
    {
        return $user->isAdmin();
    }

    /**
     * Verifica se pode ver dados privados de um membro
     */
    public function viewPrivate(Membro $user, Membro $membro): bool
    {
        return $user->matricula === $membro->matricula || $user->pode('ver_membro', $membro);
    }

    /**
     * Verifica se pode exportar dados de membros
     */
    public function export(Membro $user): bool
    {
        return $user->pode('exportar_membros');
    }

    /**
     * Verifica se pode fazer ações em massa
     */
    public function bulkAction(Membro $user): bool
    {
        return $user->isAdmin();
    }
}