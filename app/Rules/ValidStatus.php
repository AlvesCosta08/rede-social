<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStatus implements ValidationRule
{
    public function __construct(
        private array $allowed = ['ativo', 'inativo', 'pendente', 'transferido', 'saida']
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('O status é obrigatório.');
            return;
        }

        if (!in_array($value, $this->allowed)) {
            $fail("O status deve ser: " . implode(', ', $this->allowed));
        }
    }
}