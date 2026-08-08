<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable, HasPermissions;

    // ⭐ FORÇA O BANCO mysql
    protected $connection = 'mysql';

    // ⭐ USA A TABELA EXISTENTE filiado
    protected $table = 'filiado';

    // ⭐ CHAVE PRIMÁRIA É matricula
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'string';

    // ⭐ DESABILITA TIMESTAMPS PADRÃO
    public $timestamps = false;

    // ⭐ CAMPOS PREENCHÍVEIS
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
        'nivel',
        'status',
        'admin',
        'super_admin',
        'latitude',
        'longitude',
        'remember_token',
        'data_saida',
        'created_at',
        'updated_at',
    ];

    // ⭐ CAMPOS OCULTOS
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ⭐ CASTS - REMOVER AS DATAS
    protected $casts = [
        'admin' => 'boolean',
        'super_admin' => 'boolean',
        'privacidade' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'numero' => 'integer',
    ];

    // ============================================================
    // ⭐ CONSTANTES DE NÍVEIS
    // ============================================================
    const NIVEL_USUARIO = 'usuario';
    const NIVEL_SECRETARIO = 'secretario';
    const NIVEL_ADMIN = 'admin';

    // ============================================================
    // ⭐ MUTATORS CORRIGIDOS - GARANTEM O FORMATO Y-m-d
    // ============================================================

    /**
     * ⭐ MUTATOR - Data de Nascimento
     */
    public function setDataNascimentoAttribute($value)
    {
        $this->attributes['dataNascimento'] = $this->formatDate($value);
    }

    /**
     * ⭐ MUTATOR - Data de Cadastro
     */
    public function setDatCadastroAttribute($value)
    {
        $this->attributes['datCadastro'] = $this->formatDate($value);
    }

    /**
     * ⭐ MUTATOR - Data de Batismo
     */
    public function setDataBatismoAttribute($value)
    {
        $this->attributes['dataBatismo'] = $this->formatDate($value);
    }

    /**
     * ⭐ MUTATOR - Data de Consagração
     */
    public function setDataConsagracaoAttribute($value)
    {
        $this->attributes['data_Consagracao'] = $this->formatDate($value);
    }

    /**
     * ⭐ MUTATOR - Data de Saída
     */
    public function setDataSaidaAttribute($value)
    {
        $this->attributes['data_saida'] = $this->formatDate($value);
    }

    /**
     * ⭐ FUNÇÃO AUXILIAR - Formata qualquer data para Y-m-d
     */
    private function formatDate($value)
    {
        // Se for null ou vazio, retorna null
        if (empty($value)) {
            return null;
        }

        // Se já estiver no formato Y-m-d, mantém
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Se estiver no formato d/m/Y, converte
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Se tiver horas (YYYY-MM-DD HH:MM:SS), extrai só a data
        if (strpos($value, ' ') !== false) {
            $parts = explode(' ', $value);
            $datePart = $parts[0];
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePart)) {
                return $datePart;
            }
        }

        // Tenta converter qualquer formato válido
        try {
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ============================================================
    // ⭐ ACCESSORS - FORMATAM AS DATAS PARA EXIBIÇÃO
    // ============================================================

    public function getDataNascimentoFormatadaAttribute()
    {
        if (empty($this->dataNascimento)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->dataNascimento)->format('d/m/Y');
    }

    public function getDataBatismoFormatadaAttribute()
    {
        if (empty($this->dataBatismo)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->dataBatismo)->format('d/m/Y');
    }

    public function getDataConsagracaoFormatadaAttribute()
    {
        if (empty($this->data_Consagracao)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->data_Consagracao)->format('d/m/Y');
    }

    public function getDataCadastroFormatadaAttribute()
    {
        if (empty($this->datCadastro)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->datCadastro)->format('d/m/Y');
    }

    public function getDataSaidaFormatadaAttribute()
    {
        if (empty($this->data_saida)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->data_saida)->format('d/m/Y');
    }

    // ============================================================
    // ⭐ ACCESSORS ADICIONAIS
    // ============================================================

    public function getNomeAttribute($value): string
    {
        return $value ?? 'Usuário';
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? route('imagem.foto', ['filename' => $this->foto]) : null;
    }

    public function getFuncaoFormatadaAttribute(): string
    {
        return $this->funcao ?? 'Membro';
    }

    public function getSeguindoCountAttribute(): int
    {
        return $this->seguindo()->count();
    }

    public function getSeguidoresCountAttribute(): int
    {
        return $this->seguidores()->count();
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

    public function getIsSecretarioAttribute(): bool
    {
        return $this->isSecretario();
    }

    public function getNivelTextoAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Administrador';
        }
        if ($this->isSecretario()) {
            return 'Secretário';
        }
        return 'Membro';
    }

    public function getNivelBadgeAttribute(): string
    {
        if ($this->isAdmin()) {
            return '<span class="badge bg-danger">👑 Admin</span>';
        }
        if ($this->isSecretario()) {
            return '<span class="badge bg-warning text-dark">📋 Secretário</span>';
        }
        return '<span class="badge bg-secondary">👤 Membro</span>';
    }

    // ============================================================
    // ⭐ MÉTODOS DE VERIFICAÇÃO DE NÍVEL
    // ============================================================

    /**
     * Verifica se o usuário é Administrador
     */
    public function isAdmin(): bool
    {
        return $this->nivel === self::NIVEL_ADMIN || $this->admin === true || $this->super_admin === true;
    }

    /**
     * Verifica se o usuário é Super Admin (campo admin da tabela)
     */
    public function isSuperAdmin(): bool
    {
        return $this->admin === true || $this->super_admin === true;
    }

    /**
     * Verifica se o usuário é Secretário
     */
    public function isSecretario(): bool
    {
        return $this->nivel === self::NIVEL_SECRETARIO;
    }

    /**
     * Verifica se o usuário é Membro comum
     */
    public function isUsuario(): bool
    {
        return $this->nivel === self::NIVEL_USUARIO;
    }

    /**
     * Verifica se o usuário está ativo
     */
    public function isAtivo(): bool
    {
        return in_array(strtolower($this->status ?? ''), ['ativo', 'membro']);
    }

    // ============================================================
    // ⭐ MÉTODO PRINCIPAL DE PERMISSÃO - PODE()
    // ============================================================

    /**
     * Método mágico para verificar qualquer permissão
     * 
     * Exemplo: $user->pode('editar_membro', $membro)
     *          $user->pode('criar_membro')
     *          $user->pode('dashboard')
     *          $user->pode('excluir_membro')
     */
    public function pode(string $acao, $membro = null): bool
    {
        // ⭐ ADMIN: tem todas as permissões
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        // ⭐ SECRETÁRIO: permissões limitadas à sua congregação
        if ($this->isSecretario()) {
            return match ($acao) {
                // Pode editar membros da sua congregação
                'editar_membro' => $membro && $this->congregacao === $membro->congregacao,
                
                // Pode criar membros (na sua congregação)
                'criar_membro' => true,
                
                // Pode ver o dashboard
                'dashboard' => true,
                
                // Pode ver lista de membros
                'ver_membros' => true,
                
                // Pode ver detalhes de membros
                'ver_membro' => true,
                
                // Pode exportar dados da sua congregação
                'exportar_membros' => true,
                
                // NÃO pode excluir membros
                'excluir_membro' => false,
                
                // NÃO pode gerenciar secretários
                'gerenciar_secretarios' => false,
                
                // NÃO pode ver dados financeiros
                'ver_financeiro' => false,
                
                // NÃO pode configurar o sistema
                'configurar_sistema' => false,
                
                default => false,
            };
        }

        // ⭐ USUÁRIO COMUM: permissões básicas
        if ($this->isUsuario()) {
            return match ($acao) {
                // Só pode editar a si mesmo
                'editar_membro' => $membro && $this->matricula === $membro->matricula,
                
                // Pode ver perfis
                'ver_membro' => true,
                'ver_membros' => true,
                
                // NÃO pode criar membros
                'criar_membro' => false,
                
                // NÃO pode excluir
                'excluir_membro' => false,
                
                // NÃO pode acessar dashboard
                'dashboard' => false,
                
                // NÃO pode exportar
                'exportar_membros' => false,
                
                default => false,
            };
        }

        return false;
    }

    // ============================================================
    // ⭐ MÉTODOS DE PERMISSÃO ESPECÍFICOS (COMPATIBILIDADE)
    // ============================================================

    /**
     * Verifica se o usuário pode editar membros
     */
    public function canEditMembers(): bool
    {
        return $this->pode('editar_membro');
    }

    /**
     * Verifica se o usuário pode excluir membros
     */
    public function canDeleteMembers(): bool
    {
        return $this->pode('excluir_membro');
    }

    /**
     * Verifica se o usuário pode moderar posts
     */
    public function canModeratePosts(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    /**
     * Verifica se o usuário pode visualizar membros
     */
    public function canViewMembers(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode seguir outros
     */
    public function canFollow(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode comentar
     */
    public function canComment(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode curtir
     */
    public function canLike(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode criar posts
     */
    public function canCreatePosts(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode editar seus próprios posts
     */
    public function canEditOwnPosts(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário pode excluir seus próprios posts
     */
    public function canDeleteOwnPosts(): bool
    {
        return true;
    }

    /**
     * Verifica se o usuário tem permissão específica via trait
     */
    public function hasPermission(string $permission): bool
    {
        return $this->pode($permission);
    }

    // ============================================================
    // ⭐ RELACIONAMENTOS
    // ============================================================

    /**
     * Relacionamento de seguidores (quem segue este usuário)
     */
    public function seguidores(): BelongsToMany
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

    /**
     * Relacionamento de seguindo (quem este usuário segue)
     */
    public function seguindo(): BelongsToMany
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

    /**
     * Verifica se segue um membro específico
     */
    public function segue(User $membro): bool
    {
        return $this->seguindo()->where('matricula', $membro->matricula)->exists();
    }

    /**
     * Verifica se é seguido por um membro específico
     */
    public function isSeguidoPor(User $membro): bool
    {
        return $this->seguidores()->where('matricula', $membro->matricula)->exists();
    }

    /**
     * Publicações do usuário
     */
    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'filiado_matricula', 'matricula');
    }

    /**
     * Comentários do usuário
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'filiado_matricula', 'matricula');
    }

    /**
     * Curtidas do usuário
     */
    public function curtidas()
    {
        return $this->hasMany(Curtida::class, 'filiado_matricula', 'matricula');
    }

    // ============================================================
    // ⭐ SCOPES
    // ============================================================

    public function scopeAtivo($query)
    {
        return $query->whereIn('status', ['ativo', 'membro']);
    }

    public function scopeAdministradores($query)
    {
        return $query->where('admin', true)->orWhere('super_admin', true);
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

    /**
     * Scope para filtrar apenas admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('nivel', self::NIVEL_ADMIN);
    }

    /**
     * Scope para filtrar apenas secretários
     */
    public function scopeSecretarios($query)
    {
        return $query->where('nivel', self::NIVEL_SECRETARIO);
    }

    /**
     * Scope para filtrar apenas usuários comuns
     */
    public function scopeUsuarios($query)
    {
        return $query->where('nivel', self::NIVEL_USUARIO);
    }

    // ============================================================
    // ⭐ MÉTODOS DE AUTENTICAÇÃO
    // ============================================================

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
}