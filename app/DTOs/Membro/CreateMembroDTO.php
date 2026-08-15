<?php

namespace App\DTOs\Membro;

class CreateMembroDTO
{
    public function __construct(
        public readonly string $matricula,
        public readonly string $nome,
        public readonly ?string $nome_carteira,
        public readonly string $email,
        public readonly string $telefone,
        public readonly ?string $telefone2,
        public readonly string $documento,
        public readonly string $dataNascimento,
        public readonly ?string $dataBatismo,
        public readonly ?string $dataConsagracao,
        public readonly string $endereco,
        public readonly ?string $numero,
        public readonly string $bairro,
        public readonly string $cep,
        public readonly string $cidade,
        public readonly string $uf,
        public readonly string $congregacao,
        public readonly string $funcao,
        public readonly string $password,
        public readonly string $status = 'ativo',
        public readonly string $nivel = 'usuario',
        public readonly ?string $bio = null,
        public readonly ?string $mae = null,
        public readonly ?string $pai = null,
        public readonly bool $privacidade = true,
    ) {}

    /**
     * Cria DTO a partir de array (Request)
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            matricula: $data['matricula'],
            nome: $data['nome'],
            nome_carteira: $data['nome_carteira'] ?? null,
            email: $data['email'],
            telefone: $data['telefone'],
            telefone2: $data['telefone2'] ?? null,
            documento: $data['documento'],
            dataNascimento: $data['dataNascimento'],
            dataBatismo: $data['dataBatismo'] ?? null,
            dataConsagracao: $data['data_Consagracao'] ?? null,
            endereco: $data['endereco'],
            numero: $data['numero'] ?? null,
            bairro: $data['bairro'],
            cep: $data['cep'],
            cidade: $data['cidade'],
            uf: $data['uf'],
            congregacao: $data['congregacao'],
            funcao: $data['funcao'],
            password: $data['password'],
            status: $data['status'] ?? 'ativo',
            nivel: $data['nivel'] ?? 'usuario',
            bio: $data['bio'] ?? null,
            mae: $data['mae'] ?? null,
            pai: $data['pai'] ?? null,
            privacidade: (bool) ($data['privacidade'] ?? true),
        );
    }

    /**
     * Converte para array para criar no banco
     */
    public function toArray(): array
    {
        return [
            'matricula' => $this->matricula,
            'nome' => $this->nome,
            'nome_carteira' => $this->nome_carteira,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'telefone2' => $this->telefone2,
            'documento' => $this->documento,
            'dataNascimento' => $this->dataNascimento,
            'dataBatismo' => $this->dataBatismo,
            'data_Consagracao' => $this->dataConsagracao,
            'endereco' => $this->endereco,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cep' => $this->cep,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'congregacao' => $this->congregacao,
            'funcao' => $this->funcao,
            'password' => $this->password,
            'status' => $this->status,
            'nivel' => $this->nivel,
            'bio' => $this->bio,
            'mae' => $this->mae,
            'pai' => $this->pai,
            'privacidade' => $this->privacidade,
            'datCadastro' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}