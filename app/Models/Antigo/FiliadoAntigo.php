<?php

namespace App\Models\Antigo;

use Illuminate\Database\Eloquent\Model;

class FiliadoAntigo extends Model
{
    protected $connection = 'sistema_antigo';
    protected $table = 'filiado';
    protected $primaryKey = 'matricula';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'matricula',
        'nome',
        'nome_carteira',
        'foto',
        'funcao',
        'cidade',
        'uf',
        'endereco',
        'bairro',
        'email',
        'telefone',
        'documento',
        'dataNascimento',
        'dataBatismo',
        'data_Consagracao',
        'congregacao',
        'status',
        'datCadastro',
    ];
}