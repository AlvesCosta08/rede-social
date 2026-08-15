<?php

namespace App\DTOs\Membro;

use App\Models\Membro;
use Carbon\Carbon;

class MembroDTO
{
    public function __construct(
        public readonly string $matricula,
        public readonly string $nome,
        public readonly ?string $nome_carteira,
        public readonly ?string $email,
        public readonly ?string $telefone,
        public readonly ?string $telefone2,
        public readonly ?string $documento,
        public readonly ?string $dataNascimento,
        public readonly ?string $dataNascimentoFormatada,
        public readonly ?string $dataBatismo,
        public readonly ?string $dataBatismoFormatada,
        public readonly ?string $dataConsagracao,
        public readonly ?string $dataConsagracaoFormatada,
        public readonly ?string $dataCadastro,
        public readonly ?string $dataCadastroFormatada,
        public readonly ?string $dataSaida,
        public readonly ?string $dataSaidaFormatada,
        public readonly ?string $endereco,
        public readonly ?string $numero,
        public readonly ?string $bairro,
        public readonly ?string $cep,
        public readonly ?string $cidade,
        public readonly ?string $uf,
        public readonly ?string $congregacao,
        public readonly ?string $funcao,
        public readonly string $funcaoFormatada,
        public readonly string $status,
        public readonly string $statusBadge,
        public readonly string $nivel,
        public readonly string $nivelTexto,
        public readonly string $nivelBadge,
        public readonly bool $isAdmin,
        public readonly bool $isSecretario,
        public readonly bool $isUsuario,
        public readonly bool $isAtivo,
        public readonly ?string $foto,
        public readonly ?string $fotoUrl,
        public readonly ?string $bio,
        public readonly bool $privacidade,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly int $seguindoCount,
        public readonly int $seguidoresCount,
        public readonly int $publicacoesCount,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    /**
     * Cria DTO a partir do Model
     */
    public static function fromModel(Membro $membro): self
    {
        return new self(
            matricula: $membro->matricula,
            nome: $membro->nome,
            nome_carteira: $membro->nome_carteira,
            email: $membro->email,
            telefone: $membro->telefone,
            telefone2: $membro->telefone2,
            documento: $membro->documento,
            dataNascimento: $membro->dataNascimento,
            dataNascimentoFormatada: $membro->dataNascimento_formatada ?? null,
            dataBatismo: $membro->dataBatismo,
            dataBatismoFormatada: $membro->dataBatismo_formatada ?? null,
            dataConsagracao: $membro->data_Consagracao,
            dataConsagracaoFormatada: $membro->dataConsagracao_formatada ?? null,
            dataCadastro: $membro->datCadastro,
            dataCadastroFormatada: $membro->datCadastro_formatada ?? null,
            dataSaida: $membro->data_saida,
            dataSaidaFormatada: $membro->dataSaida_formatada ?? null,
            endereco: $membro->endereco,
            numero: $membro->numero,
            bairro: $membro->bairro,
            cep: $membro->cep,
            cidade: $membro->cidade,
            uf: $membro->uf,
            congregacao: $membro->congregacao,
            funcao: $membro->funcao,
            funcaoFormatada: $membro->funcao_formatada ?? 'Membro',
            status: $membro->status ?? 'inativo',
            statusBadge: $membro->status_badge ?? '<span class="badge bg-secondary">Inativo</span>',
            nivel: $membro->nivel ?? Membro::NIVEL_USUARIO,
            nivelTexto: $membro->nivel_texto ?? 'Membro',
            nivelBadge: $membro->nivel_badge ?? '<span class="badge bg-secondary">👤 Membro</span>',
            isAdmin: $membro->isAdmin(),
            isSecretario: $membro->isSecretario(),
            isUsuario: $membro->isUsuario(),
            isAtivo: $membro->isAtivo(),
            foto: $membro->foto,
            fotoUrl: $membro->foto_url,
            bio: $membro->bio,
            privacidade: (bool) $membro->privacidade,
            latitude: $membro->latitude ? (float) $membro->latitude : null,
            longitude: $membro->longitude ? (float) $membro->longitude : null,
            seguindoCount: $membro->seguindo->count(),
            seguidoresCount: $membro->seguidores->count(),
            publicacoesCount: $membro->publicacoes->count(),
            createdAt: $membro->created_at?->toISOString() ?? Carbon::now()->toISOString(),
            updatedAt: $membro->updated_at?->toISOString() ?? Carbon::now()->toISOString(),
        );
    }

    /**
     * Cria DTO a partir de array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            matricula: $data['matricula'] ?? '',
            nome: $data['nome'] ?? '',
            nome_carteira: $data['nome_carteira'] ?? null,
            email: $data['email'] ?? null,
            telefone: $data['telefone'] ?? null,
            telefone2: $data['telefone2'] ?? null,
            documento: $data['documento'] ?? null,
            dataNascimento: $data['dataNascimento'] ?? null,
            dataNascimentoFormatada: $data['dataNascimento_formatada'] ?? null,
            dataBatismo: $data['dataBatismo'] ?? null,
            dataBatismoFormatada: $data['dataBatismo_formatada'] ?? null,
            dataConsagracao: $data['data_Consagracao'] ?? null,
            dataConsagracaoFormatada: $data['dataConsagracao_formatada'] ?? null,
            dataCadastro: $data['datCadastro'] ?? null,
            dataCadastroFormatada: $data['datCadastro_formatada'] ?? null,
            dataSaida: $data['data_saida'] ?? null,
            dataSaidaFormatada: $data['dataSaida_formatada'] ?? null,
            endereco: $data['endereco'] ?? null,
            numero: $data['numero'] ?? null,
            bairro: $data['bairro'] ?? null,
            cep: $data['cep'] ?? null,
            cidade: $data['cidade'] ?? null,
            uf: $data['uf'] ?? null,
            congregacao: $data['congregacao'] ?? null,
            funcao: $data['funcao'] ?? null,
            funcaoFormatada: $data['funcao_formatada'] ?? 'Membro',
            status: $data['status'] ?? 'inativo',
            statusBadge: $data['status_badge'] ?? '<span class="badge bg-secondary">Inativo</span>',
            nivel: $data['nivel'] ?? Membro::NIVEL_USUARIO,
            nivelTexto: $data['nivel_texto'] ?? 'Membro',
            nivelBadge: $data['nivel_badge'] ?? '<span class="badge bg-secondary">👤 Membro</span>',
            isAdmin: $data['isAdmin'] ?? false,
            isSecretario: $data['isSecretario'] ?? false,
            isUsuario: $data['isUsuario'] ?? true,
            isAtivo: $data['isAtivo'] ?? false,
            foto: $data['foto'] ?? null,
            fotoUrl: $data['foto_url'] ?? null,
            bio: $data['bio'] ?? null,
            privacidade: (bool) ($data['privacidade'] ?? true),
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            seguindoCount: $data['seguindo_count'] ?? 0,
            seguidoresCount: $data['seguidores_count'] ?? 0,
            publicacoesCount: $data['publicacoes_count'] ?? 0,
            createdAt: $data['created_at'] ?? Carbon::now()->toISOString(),
            updatedAt: $data['updated_at'] ?? Carbon::now()->toISOString(),
        );
    }

    /**
     * Converte para array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}