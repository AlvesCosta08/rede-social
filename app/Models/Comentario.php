<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';  // ← OBRIGATÓRIO

    protected $fillable = ['filiado_matricula', 'publicacao_id', 'conteudo'];

    public function autor()
    {
        return $this->belongsTo(Filiado::class, 'filiado_matricula', 'matricula');
    }

    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class);
    }
}