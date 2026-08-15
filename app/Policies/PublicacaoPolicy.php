<?php

namespace App\Policies;

use App\Models\Membro;
use App\Models\Publicacao;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicacaoPolicy
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
     * Verifica se pode ver publicações
     */
    public function viewAny(Membro $user): bool
    {
        return true; // Todos podem ver
    }

    /**
     * Verifica se pode ver uma publicação específica
     */
    public function view(Membro $user, Publicacao $publicacao): bool
    {
        return true; // Todos podem ver publicações públicas
    }

    /**
     * Verifica se pode criar uma publicação
     */
    public function create(Membro $user): bool
    {
        return true; // Todos podem criar
    }

    /**
     * Verifica se pode atualizar uma publicação
     */
    public function update(Membro $user, Publicacao $publicacao): bool
    {
        // Dono da publicação ou secretário/admin
        return $user->matricula === $publicacao->filiado_matricula || 
               $user->pode('editar_membro');
    }

    /**
     * Verifica se pode deletar uma publicação
     */
    public function delete(Membro $user, Publicacao $publicacao): bool
    {
        // Dono da publicação, secretário ou admin
        return $user->matricula === $publicacao->filiado_matricula || 
               $user->pode('excluir_membro');
    }

    /**
     * Verifica se pode moderar publicações
     */
    public function moderate(Membro $user): bool
    {
        return $user->isAdmin() || $user->isSecretario();
    }
}