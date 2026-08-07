<?php

namespace App\Models;

use App\Traits\HasPermissions;  // ✅ ADICIONADO
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filiado extends Model
{
    use HasPermissions;  // ✅ ADICIONADO

    // ⭐ FORÇA O BANCO NOVO
    protected $connection = 'mysql';
    
    // ⭐ ESPECIFICA O NOME CORRETO DA TABELA
    protected $table = 'filiado';
    
    // ⭐ DESABILITA OS TIMESTAMPS (created_at e updated_at)
    public $timestamps = false;
    
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'matricula',
        'congregacao',
        'nome',
        'nome_carteira',
        'logradouro',
        'endereco',
        'numero',
        'bairro',
        'cep',
        'email',
        'cidade',
        'uf',
        'documento',
        'telefone',
        'telefone2',
        'estadoCivil',
        'dataNascimento',
        'mae',
        'pai',
        'datCadastro',
        'dataBatismo',
        'data_Consagracao',
        'arquivo',
        'cartas',
        'bio',
        'privacidade',
        'foto',
        'password',
        'funcao',
        'status',
        'admin',
        'latitude',
        'longitude',
        'remember_token',
        'data_saida',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ⭐ CASTS PARA CONVERTER DATAS AUTOMATICAMENTE
    protected $casts = [
        'dataNascimento' => 'date',
        'datCadastro' => 'date',
        'dataBatismo' => 'date',
        'data_Consagracao' => 'date',
        'data_saida' => 'date',
        'privacidade' => 'boolean',
        'admin' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'numero' => 'integer',
    ];

    // ============================================================
    // RELACIONAMENTOS
    // ============================================================

    /**
     * Relacionamento de seguidores (quem segue este membro)
     */
    public function seguidores(): BelongsToMany
    {
        return $this->belongsToMany(
            Filiado::class,
            'seguidores',
            'seguido_matricula',
            'seguidor_matricula',
            'matricula',
            'matricula'
        );
    }

    /**
     * Relacionamento de seguindo (quem este membro segue)
     */
    public function seguindo(): BelongsToMany
    {
        return $this->belongsToMany(
            Filiado::class,
            'seguidores',
            'seguidor_matricula',
            'seguido_matricula',
            'matricula',
            'matricula'
        );
    }

    /**
     * Verifica se segue um membro específico
     */
    public function segue(Filiado $membro): bool
    {
        return $this->seguindo()->where('matricula', $membro->matricula)->exists();
    }

    /**
     * Publicações do membro
     */
    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'filiado_matricula', 'matricula');
    }

    /**
     * Comentários do membro
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'filiado_matricula', 'matricula');
    }

    // ============================================================
    // ⚠️ MÉTODOS DE PERMISSÃO REMOVIDOS (USAM A TRAIT)
    // ============================================================
    // 
    // Os métodos isAdmin(), isSuperAdmin() e isAtivo() 
    // agora são fornecidos pela Trait HasPermissions
    //
    // Para verificar permissões use:
    // $filiado->isAdmin()
    // $filiado->isSuperAdmin()
    // $filiado->hasPermission('edit_members')
    // $filiado->pode('edit_members')
    // $filiado->hasRole('admin')
    //

    // ============================================================
    // ACCESSORS & MUTATORS
    // ============================================================

    /**
     * Formata a função do membro
     */
    public function getFuncaoFormatadaAttribute()
    {
        return $this->funcao ?? 'Membro';
    }

    /**
     * Garante que o nome nunca seja nulo
     */
    public function getNomeAttribute($value)
    {
        return $value ?? 'Usuário';
    }

    /**
     * Retorna a URL da foto de perfil
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto ? route('imagem.foto', ['filename' => $this->foto]) : null;
    }

    /**
     * Retorna a data de cadastro formatada
     */
    public function getDataCadastroFormatadaAttribute()
    {
        if ($this->datCadastro) {
            return \Carbon\Carbon::parse($this->datCadastro)->format('d/m/Y');
        }
        return '-';
    }

    /**
     * Retorna a data de nascimento formatada
     */
    public function getDataNascimentoFormatadaAttribute()
    {
        if ($this->dataNascimento) {
            return \Carbon\Carbon::parse($this->dataNascimento)->format('d/m/Y');
        }
        return '-';
    }

    /**
     * Retorna a data de batismo formatada
     */
    public function getDataBatismoFormatadaAttribute()
    {
        if ($this->dataBatismo) {
            return \Carbon\Carbon::parse($this->dataBatismo)->format('d/m/Y');
        }
        return '-';
    }

    /**
     * Retorna a data de consagração formatada
     */
    public function getDataConsagracaoFormatadaAttribute()
    {
        if ($this->data_Consagracao) {
            return \Carbon\Carbon::parse($this->data_Consagracao)->format('d/m/Y');
        }
        return '-';
    }

    /**
     * Retorna o status com badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'ativo' => 'success',
            'membro' => 'success',
            'inativo' => 'danger',
            'pendente' => 'warning',
            'transferido' => 'info',
            'saida' => 'secondary',
        ];

        $status = strtolower($this->status ?? 'inativo');
        $color = $colors[$status] ?? 'secondary';

        return "<span class='badge bg-{$color}'>{$this->status}</span>";
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeAtivo($query)
    {
        return $query->whereIn('status', ['ativo', 'membro']);
    }

    public function scopeAdministradores($query)
    {
        return $query->where('admin', true);
    }

    public function scopePorFuncao($query, $funcao)
    {
        return $query->where('funcao', $funcao);
    }

    public function scopePorCongregacao($query, $congregacao)
    {
        return $query->where('congregacao', $congregacao);
    }

    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
              ->orWhere('matricula', 'LIKE', "%{$termo}%")
              ->orWhere('email', 'LIKE', "%{$termo}%")
              ->orWhere('telefone', 'LIKE', "%{$termo}%")
              ->orWhere('documento', 'LIKE', "%{$termo}%")
              ->orWhere('cidade', 'LIKE', "%{$termo}%");
        });
    }
}