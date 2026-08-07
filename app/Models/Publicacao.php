<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacao extends Model
{
    // ⭐ FORÇA O BANCO NOVO
    protected $connection = 'mysql';
    
    // ⭐ ESPECIFICA O NOME CORRETO DA TABELA (IMPORTANTE!)
    protected $table = 'publicacoes';  // <-- NOME CORRETO
    
    protected $fillable = [
        'filiado_matricula',
        'conteudo'
    ];

    // ⭐ RELACIONAMENTOS COM OS NOMES CORRETOS DAS TABELAS
    public function autor()
    {
        return $this->belongsTo(Filiado::class, 'filiado_matricula', 'matricula');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'publicacao_id');
    }

    public function curtidas()
    {
        return $this->belongsToMany(
            Filiado::class,
            'curtidas',           // Nome da tabela pivot
            'publicacao_id',      // Chave estrangeira na tabela pivot
            'filiado_matricula',  // Chave relacionada na tabela pivot
            'id',                 // Chave local
            'matricula'           // Chave relacionada
        );
    }

    public function isCurtidoPor($membro)
    {
        return $this->curtidas()->where('filiado_matricula', $membro->matricula)->exists();
    }

    public function getCurtidasCountAttribute()
    {
        return $this->curtidas()->count();
    }

    // ⭐ VALIDAÇÃO AO CRIAR
    protected static function booted()
    {
        static::creating(function ($publicacao) {
            if (!Filiado::on('mysql')->where('matricula', $publicacao->filiado_matricula)->exists()) {
                throw new \Exception('Matrícula inválida: ' . $publicacao->filiado_matricula);
            }
        });
    }
}