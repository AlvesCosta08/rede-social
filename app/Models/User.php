<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasPermissions;

    /**
     * ⭐ FORÇA O BANCO mysql
     */
    protected $connection = 'mysql';

    /**
     * ⭐ USA A TABELA EXISTENTE filiado
     */
    protected $table = 'filiado';

    /**
     * ⭐ CHAVE PRIMÁRIA É matricula
     */
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * ⭐ DESABILITA TIMESTAMPS PADRÃO
     */
    public $timestamps = false;

    /**
     * Campos que podem ser preenchidos
     */
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

    /**
     * Campos ocultos
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts para conversão automática
     */
    protected function casts(): array
    {
        return [
            'dataNascimento' => 'date',
            'datCadastro' => 'datetime',
            'dataBatismo' => 'date',
            'data_Consagracao' => 'date',
            'data_saida' => 'date',
            'admin' => 'boolean',
            'privacidade' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'numero' => 'integer',
        ];
    }

    /**
     * Nome do campo usado para autenticação
     */
    public function getAuthIdentifierName(): string
    {
        return 'matricula';
    }

    /**
     * Valor do identificador
     */
    public function getAuthIdentifier()
    {
        return $this->matricula;
    }

    /**
     * Senha para autenticação
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    // ============================================================
    // MÉTODOS DE PERMISSÃO (USAM A TRAIT)
    // ============================================================

    /**
     * Verifica se o usuário pode editar membros
     */
    public function canEditMembers(): bool
    {
        return $this->pode('edit_members');
    }

    /**
     * Verifica se o usuário pode excluir membros
     */
    public function canDeleteMembers(): bool
    {
        return $this->pode('delete_members');
    }

    /**
     * Verifica se o usuário pode moderar posts
     */
    public function canModeratePosts(): bool
    {
        return $this->pode('moderate_posts');
    }

    /**
     * Verifica se o usuário pode visualizar membros
     */
    public function canViewMembers(): bool
    {
        return $this->pode('view_members');
    }

    /**
     * Verifica se o usuário pode seguir outros
     */
    public function canFollow(): bool
    {
        return $this->pode('follow_users');
    }

    /**
     * Verifica se o usuário pode comentar
     */
    public function canComment(): bool
    {
        return $this->pode('comment_posts');
    }

    /**
     * Verifica se o usuário pode curtir
     */
    public function canLike(): bool
    {
        return $this->pode('like_posts');
    }

    /**
     * Verifica se o usuário pode criar posts
     */
    public function canCreatePosts(): bool
    {
        return $this->pode('create_posts');
    }

    /**
     * Verifica se o usuário pode editar seus próprios posts
     */
    public function canEditOwnPosts(): bool
    {
        return $this->pode('edit_own_posts');
    }

    /**
     * Verifica se o usuário pode excluir seus próprios posts
     */
    public function canDeleteOwnPosts(): bool
    {
        return $this->pode('delete_own_posts');
    }

    /**
     * Verifica se o usuário está ativo
     */
    public function isAtivo(): bool
    {
        return in_array(strtolower($this->status ?? ''), ['ativo', 'membro']);
    }

    // ============================================================
    // RELACIONAMENTOS
    // ============================================================

    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'filiado_matricula', 'matricula');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'filiado_matricula', 'matricula');
    }

    public function seguidores()
    {
        return $this->belongsToMany(
            self::class,
            'seguidores',
            'seguido_matricula',
            'seguidor_matricula',
            'matricula',
            'matricula'
        );
    }

    public function seguindo()
    {
        return $this->belongsToMany(
            self::class,
            'seguidores',
            'seguidor_matricula',
            'seguido_matricula',
            'matricula',
            'matricula'
        );
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getNomeAttribute($value): string
    {
        return $value ?? 'Usuário';
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? route('imagem.foto', ['filename' => $this->foto]) : null;
    }

    public function getDataCadastroFormatadaAttribute(): string
    {
        return $this->datCadastro ? \Carbon\Carbon::parse($this->datCadastro)->format('d/m/Y H:i') : '-';
    }

    public function getDataNascimentoFormatadaAttribute(): string
    {
        return $this->dataNascimento ? \Carbon\Carbon::parse($this->dataNascimento)->format('d/m/Y') : '-';
    }

    public function getFuncaoFormatadaAttribute(): string
    {
        return $this->funcao ?? 'Membro';
    }

    public function getStatusBadgeAttribute(): string
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

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->isSuperAdmin();
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