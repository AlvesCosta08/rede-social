<?php

namespace App\Rules;

use App\Models\Membro;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidMatricula implements ValidationRule
{
    public function __construct(
        private ?string $except = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('A matrícula é obrigatória.');
            return;
        }

        if (!is_numeric($value)) {
            $fail('A matrícula deve conter apenas números.');
            return;
        }

        if (strlen($value) < 1 || strlen($value) > 10) {
            $fail('A matrícula deve ter entre 1 e 10 dígitos.');
            return;
        }

        // Verifica se já existe (exceto quando for excluir)
        $query = Membro::where('matricula', $value);
        if ($this->except) {
            $query->where('matricula', '!=', $this->except);
        }

        if ($query->exists()) {
            $fail('Esta matrícula já está em uso.');
        }
    }
}