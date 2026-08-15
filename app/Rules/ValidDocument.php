<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDocument implements ValidationRule
{
    /**
     * Tipos de documento suportados
     */
    private array $types = ['cpf', 'cnpj', 'rg'];

    public function __construct(
        private ?string $type = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $limpo = preg_replace('/[^0-9]/', '', $value);

        if (empty($limpo)) {
            $fail('O documento é obrigatório.');
            return;
        }

        // Determina o tipo automaticamente se não for especificado
        $type = $this->type ?: $this->detectType($limpo);

        if (!$this->validateByType($limpo, $type)) {
            $fail("O documento informado é inválido.");
        }
    }

    /**
     * Detecta o tipo de documento pelo tamanho
     */
    private function detectType(string $document): string
    {
        $length = strlen($document);

        if ($length === 11) {
            return 'cpf';
        }

        if ($length === 14) {
            return 'cnpj';
        }

        if ($length >= 8 && $length <= 12) {
            return 'rg';
        }

        return 'cpf'; // Fallback
    }

    /**
     * Valida pelo tipo
     */
    private function validateByType(string $document, string $type): bool
    {
        return match ($type) {
            'cpf' => $this->validateCpf($document),
            'cnpj' => $this->validateCnpj($document),
            'rg' => $this->validateRg($document),
            default => false,
        };
    }

    /**
     * Valida CPF
     */
    private function validateCpf(string $cpf): bool
    {
        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida CNPJ
     */
    private function validateCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        for ($t = 12; $t < 14; $t++) {
            for ($d = 0, $m = ($t - 7), $i = 0; $i < $t; $i++) {
                $d += $cnpj[$i] * $m;
                $m = ($m == 2) ? 9 : --$m;
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$i] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida RG
     */
    private function validateRg(string $rg): bool
    {
        // Remove caracteres não numéricos
        $rg = preg_replace('/[^0-9]/', '', $rg);

        // Verifica se tem pelo menos 8 dígitos
        if (strlen($rg) < 8 || strlen($rg) > 12) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{7,}/', $rg)) {
            return false;
        }

        return true;
    }
}