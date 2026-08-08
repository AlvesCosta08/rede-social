<?php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface PublicacaoRepositoryInterface
{
    public function findById(int $id): ?object;
    
    public function getFeed(array $seguindoIds, int $perPage = 15): LengthAwarePaginator;
    
    public function getGlobalFeed(int $perPage = 20): LengthAwarePaginator;
    
    public function getByUser(string $matricula, int $perPage = 15): LengthAwarePaginator;
    
    public function create(array $data): object;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function curtir(int $publicacaoId, string $matricula): array;
    
    public function comentar(int $publicacaoId, string $matricula, string $conteudo): object;
}