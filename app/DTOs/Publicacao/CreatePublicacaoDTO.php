<?php

namespace App\DTOs\Publicacao;

class CreatePublicacaoDTO
{
    public function __construct(
        public readonly string $filiado_matricula,
        public readonly string $conteudo,
    ) {}

    /**
     * Cria DTO a partir de array (Request)
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            filiado_matricula: $data['filiado_matricula'],
            conteudo: $data['conteudo'],
        );
    }

    /**
     * Converte para array para criar no banco
     */
    public function toArray(): array
    {
        return [
            'filiado_matricula' => $this->filiado_matricula,
            'conteudo' => $this->conteudo,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}