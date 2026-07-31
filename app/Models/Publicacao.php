<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacao extends Model
{
    use HasFactory;

    protected $table = 'publicacoes';  // ← OBRIGATÓRIO

    protected $fillable = ['filiado_matricula', 'conteudo', 'curtidas_count'];

    public function autor()
    {
        return $this->belongsTo(Filiado::class, 'filiado_matricula', 'matricula');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'publicacao_id')->orderBy('created_at', 'asc');
    }

    public function curtidas()
    {
        return $this->belongsToMany(Filiado::class, 'curtidas', 'publicacao_id', 'filiado_matricula')
                    ->withTimestamps();
    }

    public function isCurtidoPor(Filiado $filiado)
    {
        return $this->curtidas()->where('filiado_matricula', $filiado->matricula)->exists();
    }
}