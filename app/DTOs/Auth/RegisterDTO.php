<?php

namespace App\DTOs\Auth;

use Carbon\Carbon;

class RegisterDTO
{
    public function __construct(
        public readonly string $matricula,
        public readonly string $nome,
        public readonly string $email,
        public readonly string $telefone,
        public readonly string $documento,
        public readonly string $dataNascimento,
        public readonly string $endereco,
        public readonly string $cidade,
        public readonly string $uf,
        public readonly string $congregacao,
        public readonly string $funcao,
        public readonly string $password,
        public readonly ?string $telefone2 = null,
        public readonly ?string $nome_carteira = null,
        public readonly ?string $numero = null,
        public readonly ?string $bairro = null,
        public readonly ?string $cep = null,
        public readonly ?string $mae = null,
        public readonly ?string $pai = null,
        public readonly ?string $bio = null,
        public readonly ?bool $privacidade = null,
        public readonly ?string $dataBatismo = null,
        public readonly ?string $data_Consagracao = null,
        public readonly ?string $nivel = null,
        public readonly ?string $status = 'ativo',
        public readonly ?string $logradouro = null,
        public readonly ?string $estadoCivil = null,
        public readonly ?string $arquivo = null,
        public readonly ?string $cartas = null,
        public readonly ?string $foto = null,
        public readonly ?bool $admin = false,
        public readonly ?bool $super_admin = false,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            matricula: $data['matricula'],
            nome: $data['nome'],
            email: $data['email'],
            telefone: $data['telefone'],
            documento: $data['documento'],
            dataNascimento: $data['dataNascimento'],
            endereco: $data['endereco'],
            cidade: $data['cidade'],
            uf: $data['uf'],
            congregacao: $data['congregacao'],
            funcao: $data['funcao'],
            password: bcrypt($data['password']),
            telefone2: $data['telefone2'] ?? null,
            nome_carteira: $data['nome_carteira'] ?? null,
            numero: $data['numero'] ?? null,
            bairro: $data['bairro'] ?? null,
            cep: $data['cep'] ?? null,
            mae: $data['mae'] ?? null,
            pai: $data['pai'] ?? null,
            bio: $data['bio'] ?? null,
            privacidade: $data['privacidade'] ?? false,
            dataBatismo: $data['dataBatismo'] ?? null,
            data_Consagracao: $data['data_Consagracao'] ?? null,
            nivel: $data['nivel'] ?? null,
            status: $data['status'] ?? 'ativo',
        );
    }

    public function toArray(): array
    {
        return [
            'matricula' => $this->matricula,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'documento' => $this->documento,
            'dataNascimento' => $this->dataNascimento,
            'endereco' => $this->endereco,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'congregacao' => $this->congregacao,
            'funcao' => $this->funcao,
            'password' => $this->password,
            'telefone2' => $this->telefone2,
            'nome_carteira' => $this->nome_carteira,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cep' => $this->cep,
            'mae' => $this->mae,
            'pai' => $this->pai,
            'bio' => $this->bio,
            'privacidade' => $this->privacidade,
            'dataBatismo' => $this->dataBatismo,
            'data_Consagracao' => $this->data_Consagracao,
            'nivel' => $this->nivel,
            'status' => $this->status,
            'logradouro' => $this->logradouro,
            'estadoCivil' => $this->estadoCivil,
            'arquivo' => $this->arquivo,
            'cartas' => $this->cartas,
            'foto' => $this->foto,
            'admin' => $this->admin,
            'super_admin' => $this->super_admin,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'datCadastro' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}