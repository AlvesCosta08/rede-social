<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Filiado extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'filiado';
    protected $primaryKey = 'matricula';
    public $timestamps = false;

    protected $fillable = [
        'matricula', 'congregacao', 'nome', 'nome_carteira',
        'logradouro', 'endereco', 'numero', 'bairro', 'cep',
        'email', 'cidade', 'uf', 'documento', 'telefone',
        'estadoCivil', 'dataNascimento', 'mae', 'pai',
        'datCadastro', 'dataBatismo', 'data_Consagracao',
        'arquivo', 'cartas', 'funcao', 'status', 'bio',
        'foto', 'password', 'privacidade'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'dataNascimento' => 'date',
        'datCadastro' => 'date',
        'dataBatismo' => 'date',
        'data_Consagracao' => 'date',
        'privacidade' => 'boolean'
    ];

    // Acessores básicos
    public function getNomeCarteiraAttribute($value)
    {
        return $value ?: $this->nome;
    }

    public function getFuncaoFormatadaAttribute()
    {
        return $this->funcao ? strtoupper($this->funcao) : 'MEMBRO';
    }

    public function getStatusIconAttribute()
    {
        $status = strtolower($this->status ?? 'inativo');
        $cores = [
            'ativo' => '#4caf50',
            'inativo' => '#f44336',
            'visitante' => '#ff9800',
        ];
        return $cores[$status] ?? '#999';
    }

    public function getNumeroCartaoAttribute()
    {
        return str_pad($this->matricula, 6, '0', STR_PAD_LEFT);
    }

    public function getValidadeAttribute()
    {
        $data = $this->datCadastro ?: now();
        return $data->copy()->addYears(5)->format('m/y');
    }

    public function getCvvAttribute()
    {
        return substr(str_pad($this->matricula, 6, '0'), -3);
    }

    public function getEnderecoCompletoAttribute()
    {
        $parts = [];
        if ($this->logradouro) $parts[] = $this->logradouro;
        if ($this->endereco) $parts[] = $this->endereco;
        if ($this->numero) $parts[] = $this->numero;
        if ($this->bairro) $parts[] = $this->bairro;
        if ($this->cidade) $parts[] = $this->cidade;
        if ($this->uf) $parts[] = $this->uf;
        return implode(', ', $parts);
    }

    public function getIniciaisAttribute()
    {
        $palavras = explode(' ', $this->nome);
        $iniciais = '';
        foreach ($palavras as $palavra) {
            if (strlen($palavra) > 0) {
                $iniciais .= strtoupper($palavra[0]);
            }
        }
        return substr($iniciais, 0, 2);
    }

    // Método de autenticação
    public function getAuthIdentifierName()
    {
        return 'matricula';
    }

    public function getAuthIdentifier()
    {
        return $this->matricula;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}