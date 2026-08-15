<?php

namespace App\Http\Resources\Auth;

use App\DTOs\Membro\MembroDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => $this->resource['message'] ?? 'Login realizado com sucesso!',
            'data' => [
                'user' => $this->resource['user'] instanceof MembroDTO 
                    ? $this->resource['user']->toArray() 
                    : MembroDTO::fromModel($this->resource['user'])->toArray(),
                'token' => $this->resource['token'] ?? null,
                'token_type' => 'Bearer',
            ],
        ];
    }

    /**
     * Adiciona dados adicionais à resposta
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}