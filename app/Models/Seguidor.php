<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguidor extends Model
{
    // ⭐ FORÇA O BANCO NOVO
    protected $connection = 'mysql';
    
    // ⭐ ESPECIFICA O NOME CORRETO DA TABELA
    protected $table = 'seguidores';  // <-- NOME CORRETO
    
    protected $fillable = [
        'seguidor_matricula',
        'seguido_matricula'
    ];

    public $timestamps = true;

    public function seguidor()
    {
        return $this->belongsTo(Filiado::class, 'seguidor_matricula', 'matricula');
    }

    public function seguido()
    {
        return $this->belongsTo(Filiado::class, 'seguido_matricula', 'matricula');
    }
}