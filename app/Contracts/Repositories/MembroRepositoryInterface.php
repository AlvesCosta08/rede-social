<?php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface MembroRepositoryInterface
{
    public function findById(string $matricula): ?object;
    
    public function findByEmail(string $email): ?object;
    
    public function findByDocumento(string $documento): ?object;
    
    public function search(array $filters, int $perPage = 20): LengthAwarePaginator;
    
    public function create(array $data): object;
    
    public function update(string $matricula, array $data): bool;
    
    public function delete(string $matricula): bool;
    
    public function getActiveMembers(): array;
    
    public function getMembersByCongregacao(string $congregacao): array;
    
    public function getMembersByFuncao(string $funcao): array;
    
    public function getStatistics(): array;
    
    public function getAniversariantesDoMes(): array;
}