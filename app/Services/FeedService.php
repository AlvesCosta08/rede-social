<?php

namespace App\Services;

use App\Contracts\Repositories\PublicacaoRepositoryInterface;
use App\Contracts\Repositories\MembroRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class FeedService
{
    protected $publicacaoRepository;
    protected $membroRepository;

    public function __construct(
        PublicacaoRepositoryInterface $publicacaoRepository,
        MembroRepositoryInterface $membroRepository
    ) {
        $this->publicacaoRepository = $publicacaoRepository;
        $this->membroRepository = $membroRepository;
    }

    /**
     * Obter feed seguindo
     */
    public function getFeedSeguindo(string $matricula, int $perPage = 15): LengthAwarePaginator
    {
        $user = $this->membroRepository->findById($matricula);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        // Obtém IDs dos seguidos
        $seguindoIds = $user->seguindo()->pluck('matricula')->toArray();

        if (empty($seguindoIds)) {
            // Fallback: feed global
            return $this->publicacaoRepository->getGlobalFeed($perPage);
        }

        // Adiciona o próprio usuário
        $seguindoIds[] = $matricula;

        return $this->publicacaoRepository->getFeed($seguindoIds, $perPage);
    }

    /**
     * Obter feed global
     */
    public function getFeedGlobal(int $perPage = 20): LengthAwarePaginator
    {
        return $this->publicacaoRepository->getGlobalFeed($perPage);
    }

    /**
     * Obter publicações de um usuário
     */
    public function getPostsByUser(string $matricula, int $perPage = 15): LengthAwarePaginator
    {
        return $this->publicacaoRepository->getByUser($matricula, $perPage);
    }

    /**
     * Criar nova publicação
     */
    public function criarPublicacao(string $matricula, string $conteudo): object
    {
        $dados = [
            'filiado_matricula' => $matricula,
            'conteudo' => $conteudo,
            'created_at' => now(),
            'updated_at' => now()
        ];

        return $this->publicacaoRepository->create($dados);
    }

    /**
     * Atualizar publicação
     */
    public function atualizarPublicacao(int $id, string $conteudo, string $matricula): bool
    {
        $publicacao = $this->publicacaoRepository->findById($id);
        
        if (!$publicacao) {
            throw new \Exception('Publicação não encontrada.');
        }

        if ($publicacao->filiado_matricula !== $matricula) {
            throw new \Exception('Você não tem permissão para editar esta publicação.');
        }

        return $this->publicacaoRepository->update($id, ['conteudo' => $conteudo]);
    }

    /**
     * Deletar publicação
     */
    public function deletarPublicacao(int $id, string $matricula): bool
    {
        $publicacao = $this->publicacaoRepository->findById($id);
        
        if (!$publicacao) {
            throw new \Exception('Publicação não encontrada.');
        }

        // Apenas o dono ou admin pode deletar (verificação será feita no controller)
        return $this->publicacaoRepository->delete($id);
    }

    /**
     * Curtir publicação
     */
    public function curtirPublicacao(int $id, string $matricula): array
    {
        $publicacao = $this->publicacaoRepository->findById($id);
        
        if (!$publicacao) {
            throw new \Exception('Publicação não encontrada.');
        }

        return $this->publicacaoRepository->curtir($id, $matricula);
    }

    /**
     * Comentar publicação
     */
    public function comentarPublicacao(int $id, string $matricula, string $conteudo): object
    {
        $publicacao = $this->publicacaoRepository->findById($id);
        
        if (!$publicacao) {
            throw new \Exception('Publicação não encontrada.');
        }

        return $this->publicacaoRepository->comentar($id, $matricula, $conteudo);
    }
}