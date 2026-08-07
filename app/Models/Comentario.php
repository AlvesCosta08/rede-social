<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    // ⭐ FORÇA O BANCO NOVO
    protected $connection = 'mysql';
    
    // ⭐ ESPECIFICA O NOME CORRETO DA TABELA
    protected $table = 'comentarios';  // <-- NOME CORRETO
    
    protected $fillable = [
        'publicacao_id',
        'filiado_matricula',
        'conteudo'
    ];

    public function autor()
    {
        return $this->belongsTo(Filiado::class, 'filiado_matricula', 'matricula');
    }

    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'publicacao_id');
    }

    protected static function booted()
    {
        static::creating(function ($comentario) {
            if (!Filiado::on('mysql')->where('matricula', $comentario->filiado_matricula)->exists()) {
                throw new \Exception('Matrícula inválida: ' . $comentario->filiado_matricula);
            }
        });
    }
}