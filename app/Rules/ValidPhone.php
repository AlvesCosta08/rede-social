<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhone implements ValidationRule
{
    /**
     * DDDs válidos do Brasil
     */
    private array $dddsValidos = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19',
        '21', '22', '24', '27', '28',
        '31', '32', '33', '34', '35', '37', '38',
        '41', '42', '43', '44', '45', '46', '47', '48', '49',
        '51', '53', '54', '55',
        '61', '62', '63', '64', '65', '66', '67', '68', '69',
        '71', '73', '74', '75', '77', '79',
        '81', '82', '83', '84', '85', '86', '87', '88', '89',
        '91', '92', '93', '94', '95', '96', '97', '98', '99'
    ];

    public function __construct(
        private bool $required = false
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Se não for obrigatório e estiver vazio, passa
        if (!$this->required && empty($value)) {
            return;
        }

        $limpo = preg_replace('/[^0-9]/', '', $value);

        if (empty($limpo)) {
            $fail('O telefone é obrigatório.');
            return;
        }

        $length = strlen($limpo);

        // Verifica tamanho (10 dígitos = fixo, 11 dígitos = celular)
        if ($length < 10 || $length > 11) {
            $fail('O telefone deve ter entre 10 e 11 dígitos.');
            return;
        }

        // Verifica DDD
        $ddd = substr($limpo, 0, 2);
        if (!in_array($ddd, $this->dddsValidos)) {
            $fail('DDD inválido.');
            return;
        }

        // Verifica primeiro dígito
        $primeiroDigito = substr($limpo, 2, 1);

        // Celular (11 dígitos): primeiro dígito deve ser 9
        if ($length === 11 && $primeiroDigito !== '9') {
            $fail('Celular deve começar com 9.');
            return;
        }

        // Telefone fixo (10 dígitos): primeiro dígito deve ser 2-5
        if ($length === 10 && !in_array($primeiroDigito, ['2', '3', '4', '5'])) {
            $fail('Telefone fixo deve começar com 2, 3, 4 ou 5.');
            return;
        }
    }
}