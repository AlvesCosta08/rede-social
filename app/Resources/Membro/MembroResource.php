<?php

namespace App\Http\Resources\Membro;

use App\DTOs\Membro\MembroDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Se for DTO, usa diretamente
        if ($this->resource instanceof MembroDTO) {
            return $this->resource->toArray();
        }

        // Se for Model, converte para DTO e depois para array
        $dto = MembroDTO::fromModel($this->resource);
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