<?php
// database/factories/MembroFactory.php

namespace Database\Factories;

use App\Models\Membro;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MembroFactory extends Factory
{
    protected $model = Membro::class;

    public function definition(): array
    {
        return [
            'matricula' => (string) fake()->unique()->numberBetween(1000, 99999),
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->phoneNumber(),
            'documento' => fake()->unique()->numerify('###########'),
            'password' => Hash::make('password123'),
            'status' => 'ativo',
            'nivel' => Membro::NIVEL_USUARIO,
            'funcao' => 'Membro',
            'cidade' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'endereco' => fake()->streetAddress(),
            'congregacao' => fake()->company(),
            'dataNascimento' => fake()->date('Y-m-d', '2000-01-01'),
            'datCadastro' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin(): self
    {
        return $this->state([
            'nivel' => Membro::NIVEL_ADMIN,
            'admin' => true,
        ]);
    }

    public function secretario(): self
    {
        return $this->state([
            'nivel' => Membro::NIVEL_SECRETARIO,
        ]);
    }

    public function usuario(): self
    {
        return $this->state([
            'nivel' => Membro::NIVEL_USUARIO,
        ]);
    }

    public function inativo(): self
    {
        return $this->state([
            'status' => 'inativo',
        ]);
    }

    public function pendente(): self
    {
        return $this->state([
            'status' => 'pendente',
        ]);
    }
}