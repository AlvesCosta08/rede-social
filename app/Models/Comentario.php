<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    protected $connection = 'mysql';
    protected $table = 'comentarios';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'publicacao_id',
        'filiado_matricula',
        'conteudo',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================================
    // RELACIONAMENTOS
    // ============================================================

    public function publicacao(): BelongsTo
    {
        return $this->belongsTo(Publicacao::class, 'publicacao_id', 'id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filiado_matricula', 'matricula');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getTempoComentarioAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}