<?php

namespace App\DTOs\Feed;

use App\DTOs\Publicacao\PublicacaoDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedDTO
{
    public function __construct(
        public readonly array $publicacoes,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly string $nextPageUrl,
        public readonly string $prevPageUrl,
        public readonly bool $hasMorePages,
    ) {}

    /**
     * Cria DTO a partir do paginator
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        $publicacoes = [];
        foreach ($paginator->items() as $item) {
            $publicacoes[] = PublicacaoDTO::fromModel($item);
        }

        return new self(
            publicacoes: $publicacoes,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            nextPageUrl: $paginator->nextPageUrl() ?? '',
            prevPageUrl: $paginator->previousPageUrl() ?? '',
            hasMorePages: $paginator->hasMorePages(),
        );
    }

    /**
     * Cria DTO a partir do paginator com comentários
     */
    public static function fromPaginatorWithComments(LengthAwarePaginator $paginator): self
    {
        $publicacoes = [];
        foreach ($paginator->items() as $item) {
            $publicacoes[] = PublicacaoDTO::fromModelWithComments($item);
        }

        return new self(
            publicacoes: $publicacoes,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            nextPageUrl: $paginator->nextPageUrl() ?? '',
            prevPageUrl: $paginator->previousPageUrl() ?? '',
            hasMorePages: $paginator->hasMorePages(),
        );
    }

    /**
     * Converte para array
     */
    public function toArray(): array
    {
        return [
            'publicacoes' => array_map(fn($p) => $p->toArray(), $this->publicacoes),
            'pagination' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
                'next_page_url' => $this->nextPageUrl,
                'prev_page_url' => $this->prevPageUrl,
                'has_more_pages' => $this->hasMorePages,
            ],
        ];
    }
}