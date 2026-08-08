<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Repositories\MembroRepositoryInterface;

class PermissaoService
{
    protected $membroRepository;

    public function __construct(MembroRepositoryInterface $membroRepository)
    {
        $this->membroRepository = $membroRepository;
    }

    /**
     * Verifica se o usuário pode editar um membro
     */
    public function podeEditarMembro(User $user, string $matricula): bool
    {
        $membro = $this->membroRepository->findById($matricula);
        
        if (!$membro) {
            return false;
        }

        return $user->pode('editar_membro', $membro);
    }

    /**
     * Verifica se o usuário pode excluir um membro
     */
    public function podeExcluirMembro(User $user, string $matricula): bool
    {
        $membro = $this->membroRepository->findById($matricula);
        
        if (!$membro) {
            return false;
        }

        return $user->pode('excluir_membro', $membro);
    }

    /**
     * Verifica se o usuário pode ver um membro
     */
    public function podeVerMembro(User $user, string $matricula): bool
    {
        $membro = $this->membroRepository->findById($matricula);
        
        if (!$membro) {
            return false;
        }

        return $user->pode('ver_membro', $membro);
    }

    /**
     * Verifica se o usuário pode gerenciar secretários
     */
    public function podeGerenciarSecretarios(User $user): bool
    {
        return $user->pode('gerenciar_secretarios');
    }

    /**
     * Verifica se o usuário pode acessar o dashboard
     */
    public function podeAcessarDashboard(User $user): bool
    {
        return $user->pode('dashboard');
    }
}