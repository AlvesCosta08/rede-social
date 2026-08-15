<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Membro extends Authenticatable
{
    use Notifiable, HasPermissions, HasFactory;

    // CONEXÃO E TABELA
    protected $connection = 'mysql';
    protected $table = 'filiado';
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    // CAMPOS PREENCHÍVEIS
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

    // CAMPOS OCULTOS
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // CASTS
    protected $casts = [
        'admin' => 'boolean',
        'super_admin' => 'boolean',
        'privacidade' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'numero' => 'integer',
        'dataNascimento' => 'date:Y-m-d',
        'datCadastro' => 'date:Y-m-d',
        'dataBatismo' => 'date:Y-m-d',
        'data_Consagracao' => 'date:Y-m-d',
        'data_saida' => 'date:Y-m-d',
    ];

    // CONSTANTES DE NÍVEIS
    const NIVEL_USUARIO = 'usuario';
    const NIVEL_SECRETARIO = 'secretario';
    const NIVEL_ADMIN = 'admin';

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_PENDENTE = 'pendente';
    const STATUS_TRANSFERIDO = 'transferido';
    const STATUS_SAIDA = 'saida';

    // ============================================================
    // MUTATORS - FORMATAM DATAS PARA O BANCO
    // ============================================================

    public function setDataNascimentoAttribute($value)
    {
        $this->attributes['dataNascimento'] = $this->formatDateForDb($value);
    }

    public function setDatCadastroAttribute($value)
    {
        $this->attributes['datCadastro'] = $this->formatDateForDb($value);
    }

    public function setDataBatismoAttribute($value)
    {
        $this->attributes['dataBatismo'] = $this->formatDateForDb($value);
    }

    public function setDataConsagracaoAttribute($value)
    {
        $this->attributes['data_Consagracao'] = $this->formatDateForDb($value);
    }

    public function setDataSaidaAttribute($value)
    {
        $this->attributes['data_saida'] = $this->formatDateForDb($value);
    }

    private function formatDateForDb($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            try {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ============================================================
    // ACCESSORS - FORMATAM DATAS PARA EXIBIÇÃO
    // ============================================================

    public function getDataNascimentoFormatadaAttribute(): string
    {
        return $this->dataNascimento ? Carbon::parse($this->dataNascimento)->format('d/m/Y') : 'Não informado';
    }

    public function getDatCadastroFormatadaAttribute(): string
    {
        return $this->datCadastro ? Carbon::parse($this->datCadastro)->format('d/m/Y') : 'Não informado';
    }

    public function getDataBatismoFormatadaAttribute(): string
    {
        return $this->dataBatismo ? Carbon::parse($this->dataBatismo)->format('d/m/Y') : 'Não informado';
    }

    public function getDataConsagracaoFormatadaAttribute(): string
    {
        return $this->data_Consagracao ? Carbon::parse($this->data_Consagracao)->format('d/m/Y') : 'Não informado';
    }

    public function getDataSaidaFormatadaAttribute(): string
    {
        return $this->data_saida ? Carbon::parse($this->data_saida)->format('d/m/Y') : 'Não informado';
    }

    // ============================================================
    // ACCESSORS ADICIONAIS
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

    public function getPublicacoesCountAttribute(): int
    {
        return $this->publicacoes()->count();
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
    // MÉTODOS DE VERIFICAÇÃO DE NÍVEL
    // ============================================================

    public function isAdmin(): bool
    {
        return $this->nivel === self::NIVEL_ADMIN || 
               $this->admin === true || 
               $this->super_admin === true;
    }

    public function isSuperAdmin(): bool
    {
        return $this->admin === true || $this->super_admin === true;
    }

    public function isSecretario(): bool
    {
        return $this->nivel === self::NIVEL_SECRETARIO;
    }

    public function isUsuario(): bool
    {
        return $this->nivel === self::NIVEL_USUARIO || 
               ($this->nivel === null && !$this->isAdmin() && !$this->isSecretario());
    }

    public function isAtivo(): bool
    {
        return in_array(strtolower($this->status ?? ''), ['ativo', 'membro']);
    }

    public function isPendente(): bool
    {
        return strtolower($this->status ?? '') === 'pendente';
    }

    public function isInativo(): bool
    {
        return strtolower($this->status ?? '') === 'inativo';
    }

    public function isTransferido(): bool
    {
        return strtolower($this->status ?? '') === 'transferido';
    }

    public function isSaida(): bool
    {
        return strtolower($this->status ?? '') === 'saida';
    }

    // ============================================================
    // MÉTODO PRINCIPAL DE PERMISSÃO
    // ============================================================

    public function pode(string $acao, $membro = null): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isSecretario()) {
            return match ($acao) {
                'editar_membro' => $membro && $this->congregacao === $membro->congregacao,
                'criar_membro' => true,
                'dashboard' => true,
                'ver_membros' => true,
                'ver_membro' => true,
                'exportar_membros' => true,
                'excluir_membro' => $membro && $this->congregacao === $membro->congregacao,
                'excluir_propria_conta' => false,
                'gerenciar_secretarios' => false,
                'ver_financeiro' => false,
                'configurar_sistema' => false,
                default => false,
            };
        }

        if ($this->isUsuario()) {
            return match ($acao) {
                'editar_membro' => $membro && $this->matricula === $membro->matricula,
                'ver_membro' => true,
                'ver_membros' => true,
                'excluir_propria_conta' => true,
                'criar_membro' => false,
                'excluir_membro' => false,
                'dashboard' => false,
                'exportar_membros' => false,
                default => false,
            };
        }

        return false;
    }

    // ============================================================
    // MÉTODO PARA VERIFICAR SE PODE EXCLUIR UM MEMBRO ESPECÍFICO
    // ============================================================

    public function podeExcluirMembro($membro): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isSecretario()) {
            return $membro && $this->congregacao === $membro->congregacao;
        }

        return false;
    }

    // ============================================================
    // MÉTODO PARA VERIFICAR SE PODE EXCLUIR A PRÓPRIA CONTA
    // ============================================================

    public function podeExcluirPropriaConta(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isSecretario()) {
            return false;
        }

        if ($this->isUsuario()) {
            return true;
        }

        return false;
    }

    // ============================================================
    // RELACIONAMENTOS
    // ============================================================

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

    public function segue(Membro $membro): bool
    {
        return $this->seguindo()->where('matricula', $membro->matricula)->exists();
    }

    public function isSeguidoPor(Membro $membro): bool
    {
        return $this->seguidores()->where('matricula', $membro->matricula)->exists();
    }

    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'filiado_matricula', 'matricula');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'filiado_matricula', 'matricula');
    }

    public function curtidas()
    {
        return $this->hasMany(Curtida::class, 'filiado_matricula', 'matricula');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeAtivo($query)
    {
        return $query->whereIn('status', ['ativo', 'membro']);
    }

    public function scopeInativo($query)
    {
        return $query->where('status', 'inativo');
    }

    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeAtivos($query)
    {
        return $query->whereIn('status', ['ativo', 'membro']);
    }

    public function scopeAdministradores($query)
    {
        return $query->where('nivel', self::NIVEL_ADMIN)
                     ->orWhere('admin', true)
                     ->orWhere('super_admin', true);
    }

    public function scopeSecretarios($query)
    {
        return $query->where('nivel', self::NIVEL_SECRETARIO);
    }

    public function scopeUsuarios($query)
    {
        return $query->where('nivel', self::NIVEL_USUARIO);
    }

    public function scopePorFuncao($query, $funcao)
    {
        return $query->where('funcao', $funcao);
    }

    public function scopePorCongregacao($query, $congregacao)
    {
        return $query->where('congregacao', $congregacao);
    }

    public function scopePorCidade($query, $cidade)
    {
        return $query->where('cidade', $cidade);
    }

    public function scopePorUf($query, $uf)
    {
        return $query->where('uf', $uf);
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

    public function scopeProximos($query, float $lat, float $lng, float $distanciaKm = 10)
    {
        return $query->selectRaw("*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distancia", [$lat, $lng, $lat])
            ->having('distancia', '<', $distanciaKm)
            ->orderBy('distancia');
    }

    // ============================================================
    // MÉTODOS DE AUTENTICAÇÃO
    // ============================================================

    public function getAuthIdentifierName(): string
    {
        return 'matricula';
    }

    public function getAuthIdentifier()
    {
        return $this->matricula;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }
}