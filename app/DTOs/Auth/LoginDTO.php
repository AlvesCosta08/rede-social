<?php

namespace App\DTOs\Auth;

class LoginDTO
{
    public function __construct(
        public readonly string $matricula,
        public readonly string $password,
        public readonly bool $remember = false,
    ) {}

    /**
     * Cria DTO a partir de array (Request)
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            matricula: $data['matricula'],
            password: $data['password'],
            remember: (bool) ($data['remember'] ?? false),
        );
    }

    /**
     * Converte para array
     */
    public function toArray(): array
    {
        return [
            'matricula' => $this->matricula,
            'password' => $this->password,
            'remember' => $this->remember,
        ];
    }
}