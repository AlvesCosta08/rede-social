<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filiado extends Model
{
    use HasPermissions;

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
        'nivel',
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

    // ⭐ CASTS - REMOVER as datas daqui para usar mutators
    protected $casts = [
        'privacidade' => 'boolean',
        'admin' => 'boolean',
        'nivel' => 'string',
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

    public function getDatCadastroFormatadaAttribute()
    {
        if (empty($this->datCadastro)) {
            return 'Não informado';
        }
        return \Carbon\Carbon::parse($this->datCadastro)->format('d/m/Y');
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
     * Retorna o nível do membro em texto
     */
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

    /**
     * Retorna o nível do membro com badge HTML
     */
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
    // ⭐ MÉTODOS DE PERMISSÃO
    // ============================================================

    /**
     * Verifica se o membro é administrador
     */
    public function isAdmin(): bool
    {
        // Verifica pelo campo nivel (novo sistema)
        if (isset($this->nivel) && $this->nivel === self::NIVEL_ADMIN) {
            return true;
        }
        
        // Verifica pelo campo admin (legado)
        if ($this->admin === true || $this->admin === 1 || $this->admin === '1') {
            return true;
        }

        // Verifica pela função (legado)
        $funcaoAdmin = ['administrador', 'admin', 'Administrador', 'Admin', 'pastor', 'pastor presidente'];
        if (in_array(strtolower($this->funcao ?? ''), array_map('strtolower', $funcaoAdmin))) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o membro é Secretário
     */
    public function isSecretario(): bool
    {
        // Verifica pelo campo nivel
        if (isset($this->nivel) && $this->nivel === self::NIVEL_SECRETARIO) {
            return true;
        }

        // Verifica pela função (legado)
        $funcaoSecretario = ['secretario', 'secretário', 'Secretario', 'Secretário'];
        if (in_array(strtolower($this->funcao ?? ''), array_map('strtolower', $funcaoSecretario))) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o membro é Usuário comum
     */
    public function isUsuario(): bool
    {
        // Se for admin ou secretario, não é usuario comum
        if ($this->isAdmin() || $this->isSecretario()) {
            return false;
        }

        // Verifica pelo campo nivel
        if (isset($this->nivel) && $this->nivel === self::NIVEL_USUARIO) {
            return true;
        }

        // Por padrão, todo mundo que não é admin nem secretario é usuario
        return true;
    }

    /**
     * Verifica se o membro está ativo
     */
    public function isAtivo(): bool
    {
        $statusAtivos = ['ativo', 'membro', 'active', 'activated'];
        return in_array(strtolower($this->status ?? ''), $statusAtivos);
    }

    /**
     * ⭐ MÉTODO PRINCIPAL DE PERMISSÃO
     */
    public function pode(string $acao, $membro = null): bool
    {
        // ⭐ ADMIN: tem todas as permissões
        if ($this->isAdmin()) {
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
    // ⭐ RELACIONAMENTOS
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
     * Verifica se é seguido por um membro específico
     */
    public function isSeguidoPor(Filiado $membro): bool
    {
        return $this->seguidores()->where('matricula', $membro->matricula)->exists();
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

    /**
     * Curtidas do membro
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
    // ⭐ MÉTODO PARA AUTENTICAÇÃO
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