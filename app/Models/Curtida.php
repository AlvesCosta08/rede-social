<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curtida extends Model
{
    protected $connection = 'mysql';
    protected $table = 'curtidas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'publicacao_id',
        'filiado_matricula',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ============================================================
    // RELACIONAMENTOS
    // ============================================================

    public function publicacao(): BelongsTo
    {
        return $this->belongsTo(Publicacao::class, 'publicacao_id', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filiado_matricula', 'matricula');
    }
}