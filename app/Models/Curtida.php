<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curtida extends Model
{
    // ⭐ FORÇA O BANCO NOVO
    protected $connection = 'mysql';
    
    // ⭐ ESPECIFICA O NOME CORRETO DA TABELA
    protected $table = 'curtidas';  // <-- NOME CORRETO
    
    protected $fillable = [
        'publicacao_id',
        'filiado_matricula'
    ];

    public function filiado()
    {
        return $this->belongsTo(Filiado::class, 'filiado_matricula', 'matricula');
    }

    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'publicacao_id');
    }
}