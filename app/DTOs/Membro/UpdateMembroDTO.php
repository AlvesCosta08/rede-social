<?php

namespace App\DTOs\Membro;

class UpdateMembroDTO
{
    public function __construct(
        public readonly string $matricula,
        public readonly ?string $nome = null,
        public readonly ?string $nome_carteira = null,
        public readonly ?string $email = null,
        public readonly ?string $telefone = null,
        public readonly ?string $telefone2 = null,
        public readonly ?string $documento = null,
        public readonly ?string $dataNascimento = null,
        public readonly ?string $dataBatismo = null,
        public readonly ?string $dataConsagracao = null,
        public readonly ?string $endereco = null,
        public readonly ?string $numero = null,
        public readonly ?string $bairro = null,
        public readonly ?string $cep = null,
        public readonly ?string $cidade = null,
        public readonly ?string $uf = null,
        public readonly ?string $congregacao = null,
        public readonly ?string $funcao = null,
        public readonly ?string $password = null,
        public readonly ?string $status = null,
        public readonly ?string $nivel = null,
        public readonly ?string $bio = null,
        public readonly ?string $mae = null,
        public readonly ?string $pai = null,
        public readonly ?bool $privacidade = null,
        public readonly ?string $foto = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $dataSaida = null,
    ) {}

    /**
     * Cria DTO a partir de array (Request)
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            matricula: $data['matricula'],
            nome: $data['nome'] ?? null,
            nome_carteira: $data['nome_carteira'] ?? null,
            email: $data['email'] ?? null,
            telefone: $data['telefone'] ?? null,
            telefone2: $data['telefone2'] ?? null,
            documento: $data['documento'] ?? null,
            dataNascimento: $data['dataNascimento'] ?? null,
            dataBatismo: $data['dataBatismo'] ?? null,
            dataConsagracao: $data['data_Consagracao'] ?? null,
            endereco: $data['endereco'] ?? null,
            numero: $data['numero'] ?? null,
            bairro: $data['bairro'] ?? null,
            cep: $data['cep'] ?? null,
            cidade: $data['cidade'] ?? null,
            uf: $data['uf'] ?? null,
            congregacao: $data['congregacao'] ?? null,
            funcao: $data['funcao'] ?? null,
            password: $data['password'] ?? null,
            status: $data['status'] ?? null,
            nivel: $data['nivel'] ?? null,
            bio: $data['bio'] ?? null,
            mae: $data['mae'] ?? null,
            pai: $data['pai'] ?? null,
            privacidade: isset($data['privacidade']) ? (bool) $data['privacidade'] : null,
            foto: $data['foto'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            dataSaida: $data['data_saida'] ?? null,
        );
    }

    /**
     * Converte para array para atualizar no banco
     * Remove campos null para não sobrescrever com vazio
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->nome !== null) $data['nome'] = $this->nome;
        if ($this->nome_carteira !== null) $data['nome_carteira'] = $this->nome_carteira;
        if ($this->email !== null) $data['email'] = $this->email;
        if ($this->telefone !== null) $data['telefone'] = $this->telefone;
        if ($this->telefone2 !== null) $data['telefone2'] = $this->telefone2;
        if ($this->documento !== null) $data['documento'] = $this->documento;
        if ($this->dataNascimento !== null) $data['dataNascimento'] = $this->dataNascimento;
        if ($this->dataBatismo !== null) $data['dataBatismo'] = $this->dataBatismo;
        if ($this->dataConsagracao !== null) $data['data_Consagracao'] = $this->dataConsagracao;
        if ($this->endereco !== null) $data['endereco'] = $this->endereco;
        if ($this->numero !== null) $data['numero'] = $this->numero;
        if ($this->bairro !== null) $data['bairro'] = $this->bairro;
        if ($this->cep !== null) $data['cep'] = $this->cep;
        if ($this->cidade !== null) $data['cidade'] = $this->cidade;
        if ($this->uf !== null) $data['uf'] = $this->uf;
        if ($this->congregacao !== null) $data['congregacao'] = $this->congregacao;
        if ($this->funcao !== null) $data['funcao'] = $this->funcao;
        if ($this->password !== null) $data['password'] = $this->password;
        if ($this->status !== null) $data['status'] = $this->status;
        if ($this->nivel !== null) $data['nivel'] = $this->nivel;
        if ($this->bio !== null) $data['bio'] = $this->bio;
        if ($this->mae !== null) $data['mae'] = $this->mae;
        if ($this->pai !== null) $data['pai'] = $this->pai;
        if ($this->privacidade !== null) $data['privacidade'] = $this->privacidade;
        if ($this->foto !== null) $data['foto'] = $this->foto;
        if ($this->latitude !== null) $data['latitude'] = $this->latitude;
        if ($this->longitude !== null) $data['longitude'] = $this->longitude;
        if ($this->dataSaida !== null) $data['data_saida'] = $this->dataSaida;

        $data['updated_at'] = now();

        return $data;
    }

    /**
     * Verifica se tem algum campo para atualizar
     */
    public function hasChanges(): bool
    {
        return count($this->toArray()) > 1; // > 1 porque sempre tem updated_at
    }
}