<?php

namespace App\Services;

use App\Contracts\Repositories\MembroRepositoryInterface;
use App\Contracts\Repositories\PublicacaoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MembroService
{
    protected $membroRepository;
    protected $publicacaoRepository;

    public function __construct(
        MembroRepositoryInterface $membroRepository,
        PublicacaoRepositoryInterface $publicacaoRepository
    ) {
        $this->membroRepository = $membroRepository;
        $this->publicacaoRepository = $publicacaoRepository;
    }

    /**
     * Buscar membros com filtros
     */
    public function buscarMembros(array $filters): LengthAwarePaginator
    {
        return $this->membroRepository->search($filters, $filters['per_page'] ?? 20);
    }

    /**
     * Buscar membro por matrícula
     */
    public function buscarPorMatricula(string $matricula): ?object
    {
        return $this->membroRepository->findById($matricula);
    }

    /**
     * Buscar membro por email
     */
    public function buscarPorEmail(string $email): ?object
    {
        return $this->membroRepository->findByEmail($email);
    }

    /**
     * Criar novo membro
     */
    public function criarMembro(array $dados, string $senha): object
    {
        $dados['password'] = Hash::make($senha);
        $dados['datCadastro'] = now();
        $dados['created_at'] = now();
        $dados['updated_at'] = now();

        return $this->membroRepository->create($dados);
    }

    /**
     * Atualizar membro
     */
    public function atualizarMembro(string $matricula, array $dados): bool
    {
        $dados['updated_at'] = now();

        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $dados['password'] = Hash::make($dados['senha']);
            unset($dados['senha']);
        }

        return $this->membroRepository->update($matricula, $dados);
    }

    /**
     * Deletar membro (com verificação)
     */
    public function deletarMembro(string $matricula, string $adminMatricula): bool
    {
        // Não pode deletar o próprio admin
        if ($matricula === $adminMatricula) {
            throw new \Exception('Você não pode deletar sua própria conta.');
        }

        // Verifica se o membro existe
        $membro = $this->membroRepository->findById($matricula);
        if (!$membro) {
            throw new \Exception('Membro não encontrado.');
        }

        // Remove foto se existir
        if ($membro->foto) {
            $this->removerFoto($membro->foto);
        }

        return $this->membroRepository->delete($matricula);
    }

    /**
     * Remover foto do membro
     */
    public function removerFoto(string $nome): void
    {
        $paths = [
            storage_path('app/public/fotos/' . $nome),
            public_path('storage/fotos/' . $nome),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Obter estatísticas
     */
    public function getEstatisticas(): array
    {
        return $this->membroRepository->getStatistics();
    }

    /**
     * Obter aniversariantes do mês
     */
    public function getAniversariantes(): array
    {
        return $this->membroRepository->getAniversariantesDoMes();
    }

    /**
     * Migrar membro do banco antigo
     */
    public function migrarDoBancoAntigo(object $userAntigo, string $senha): object
    {
        $dados = [
            'matricula' => $userAntigo->matricula,
            'nome' => $userAntigo->nome ?? 'Usuário',
            'password' => Hash::make($senha),
            'funcao' => $userAntigo->funcao ?? 'Membro',
            'status' => 'ativo',
            'created_at' => now(),
            'updated_at' => now(),
            'datCadastro' => $userAntigo->datCadastro ?? now(),
        ];

        $campos = ['email', 'telefone', 'documento', 'cidade', 'uf', 'endereco', 'foto', 'congregacao'];
        foreach ($campos as $campo) {
            if (!empty($userAntigo->$campo)) {
                $dados[$campo] = $campo === 'documento' 
                    ? preg_replace('/[^0-9]/', '', $userAntigo->$campo) 
                    : $userAntigo->$campo;
            }
        }

        return $this->membroRepository->create($dados);
    }
}