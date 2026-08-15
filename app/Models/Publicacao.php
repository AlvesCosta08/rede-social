<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publicacao extends Model
{
    protected $connection = 'mysql';
    protected $table = 'publicacoes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
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

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Membro::class, 'filiado_matricula', 'matricula');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'publicacao_id', 'id');
    }

    public function curtidas(): HasMany
    {
        return $this->hasMany(Curtida::class, 'publicacao_id', 'id');
    }

    // ============================================================
    // MÉTODOS AUXILIARES
    // ============================================================

    public function isCurtidoPor(Membro $user): bool
    {
        return $this->curtidas()
            ->where('filiado_matricula', $user->matricula)
            ->exists();
    }

    public function getCurtidasCountAttribute(): int
    {
        return $this->curtidas()->count();
    }

    public function getComentariosCountAttribute(): int
    {
        return $this->comentarios()->count();
    }

    public function getTempoPublicacaoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeDoUsuario($query, Membro $user)
    {
        return $query->where('filiado_matricula', $user->matricula);
    }

    public function scopeDosSeguidos($query, Membro $user)
    {
        $seguindoIds = $user->seguindo()->pluck('matricula')->toArray();
        $seguindoIds[] = $user->matricula;
        
        return $query->whereIn('filiado_matricula', $seguindoIds);
    }

    public function scopeGlobal($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}