<?php

namespace App\DTOs\Publicacao;

use App\Models\Publicacao;
use App\Models\Membro;
use Carbon\Carbon;

class PublicacaoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $conteudo,
        public readonly string $autorMatricula,
        public readonly string $autorNome,
        public readonly ?string $autorFotoUrl,
        public readonly int $curtidasCount,
        public readonly int $comentariosCount,
        public readonly bool $curtidoPorUsuario,
        public readonly string $tempoPublicacao,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly ?array $comentarios = null,
    ) {}

    /**
     * Cria DTO a partir do Model
     */
    public static function fromModel(Publicacao $publicacao, ?string $usuarioMatricula = null): self
    {
        $autor = $publicacao->autor;
        
        return new self(
            id: $publicacao->id,
            conteudo: $publicacao->conteudo,
            autorMatricula: $autor?->matricula ?? '',
            autorNome: $autor?->nome ?? 'Usuário',
            autorFotoUrl: $autor?->foto_url ?? null,
            curtidasCount: $publicacao->curtidas->count(),
            comentariosCount: $publicacao->comentarios->count(),
            curtidoPorUsuario: $usuarioMatricula ? $publicacao->isCurtidoPor(
                new Membro(['matricula' => $usuarioMatricula])
            ) : false,
            tempoPublicacao: $publicacao->created_at->diffForHumans(),
            createdAt: $publicacao->created_at->toISOString(),
            updatedAt: $publicacao->updated_at->toISOString(),
            comentarios: null, // Será preenchido separadamente se necessário
        );
    }

    /**
     * Cria DTO com comentários
     */
    public static function fromModelWithComments(Publicacao $publicacao, ?string $usuarioMatricula = null): self
    {
        $dto = self::fromModel($publicacao, $usuarioMatricula);
        
        // Adiciona comentários
        $comentarios = [];
        foreach ($publicacao->comentarios as $comentario) {
            $comentarios[] = [
                'id' => $comentario->id,
                'conteudo' => $comentario->conteudo,
                'autor_matricula' => $comentario->autor->matricula ?? '',
                'autor_nome' => $comentario->autor->nome ?? 'Usuário',
                'autor_foto_url' => $comentario->autor->foto_url ?? null,
                'tempo' => $comentario->created_at->diffForHumans(),
                'created_at' => $comentario->created_at->toISOString(),
            ];
        }
        
        // Usando reflection para definir o valor readonly
        $reflection = new \ReflectionProperty($dto, 'comentarios');
        $reflection->setAccessible(true);
        $reflection->setValue($dto, $comentarios);
        
        return $dto;
    }

    /**
     * Cria DTO a partir de array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            conteudo: $data['conteudo'] ?? '',
            autorMatricula: $data['autor_matricula'] ?? '',
            autorNome: $data['autor_nome'] ?? 'Usuário',
            autorFotoUrl: $data['autor_foto_url'] ?? null,
            curtidasCount: $data['curtidas_count'] ?? 0,
            comentariosCount: $data['comentarios_count'] ?? 0,
            curtidoPorUsuario: $data['curtido_por_usuario'] ?? false,
            tempoPublicacao: $data['tempo_publicacao'] ?? 'agora',
            createdAt: $data['created_at'] ?? Carbon::now()->toISOString(),
            updatedAt: $data['updated_at'] ?? Carbon::now()->toISOString(),
            comentarios: $data['comentarios'] ?? null,
        );
    }

    /**
     * Converte para array
     */
    public function toArray(): array
    {
        return array_merge(get_object_vars($this), [
            'autor' => [
                'matricula' => $this->autorMatricula,
                'nome' => $this->autorNome,
                'foto_url' => $this->autorFotoUrl,
            ]
        ]);
    }
}