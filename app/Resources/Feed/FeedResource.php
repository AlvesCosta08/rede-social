<?php

namespace App\Http\Resources\Feed;

use App\DTOs\Feed\FeedDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Se for DTO, usa diretamente
        if ($this->resource instanceof FeedDTO) {
            return $this->resource->toArray();
        }

        // Se for paginator, converte para DTO e depois para array
        $dto = FeedDTO::fromPaginator($this->resource);
        return $dto->toArray();
    }

    /**
     * Adiciona dados adicionais à resposta
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'meta' => [
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}