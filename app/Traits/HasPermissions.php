<?php

namespace App\Traits;

trait HasPermissions
{
    // ⭐ CONSTANTES DE NÍVEIS
    const NIVEL_USUARIO = 'usuario';
    const NIVEL_SECRETARIO = 'secretario';
    const NIVEL_ADMIN = 'admin';

    /**
     * Verifica se o usuário é administrador
     * ✅ MÚLTIPLAS FORMAS DE VERIFICAÇÃO + CAMPO NIVEL
     */
    public function isAdmin(): bool
    {
        // 1. ⭐ CAMPO NIVEL (NOVO SISTEMA)
        if (isset($this->nivel) && $this->nivel === self::NIVEL_ADMIN) {
            return true;
        }

        // 2. Campo admin booleano (legado)
        if ($this->admin === true || $this->admin === 1 || $this->admin === '1') {
            return true;
        }

        // 3. Função no banco (legado)
        $funcaoAdmin = ['administrador', 'admin', 'Administrador', 'Admin', 'pastor', 'pastor presidente'];
        if (in_array(strtolower($this->funcao ?? ''), array_map('strtolower', $funcaoAdmin))) {
            return true;
        }

        // 4. Matrícula específica (super admin)
        if ($this->matricula === '1') {
            return true;
        }

        return false;
    }

    /**
     * Verifica se é super admin
     * ✅ MAIS FLEXÍVEL + CAMPO NIVEL
     */
    public function isSuperAdmin(): bool
    {
        // ⭐ NIVEL ADMIN JÁ É SUPER ADMIN
        if ($this->isAdmin()) {
            return true;
        }

        // Matrícula 1 OU função específica OU campo super_admin
        return $this->isAdmin() && (
            $this->matricula === '1' ||
            in_array(strtolower($this->funcao ?? ''), ['super admin', 'super_administrador', 'super administrator']) ||
            ($this->super_admin ?? false) === true
        );
    }

    /**
     * ⭐ NOVO: Verifica se o usuário é Secretário
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
     * ⭐ NOVO: Verifica se o usuário é Usuário comum
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
     * Verifica se o usuário está ativo
     * ✅ NOVO MÉTODO
     */
    public function isAtivo(): bool
    {
        $statusAtivos = ['ativo', 'membro', 'active', 'activated', '1'];
        return in_array(strtolower($this->status ?? ''), $statusAtivos);
    }

    /**
     * Verifica se tem uma permissão específica
     * ✅ COM VERIFICAÇÃO DE NÍVEL
     */
    public function hasPermission(string $permission): bool
    {
        // ✅ VERIFICA SE ESTÁ ATIVO
        if (!$this->isAtivo()) {
            return false;
        }

        // ⭐ SUPER ADMIN TEM TUDO
        if ($this->isSuperAdmin()) {
            return true;
        }

        // ⭐ ADMIN TEM PERMISSÕES ADMINISTRATIVAS
        if ($this->isAdmin()) {
            $adminPermissions = [
                'manage_users',
                'manage_members',
                'manage_posts',
                'edit_members',
                'delete_members',
                'moderate_posts',
                'view_reports',
                'manage_settings',
                'view_admin_panel',
                'export_data',
                'bulk_actions',
                'manage_comments',
                'gerenciar_secretarios',
                'excluir_membro',
                'criar_membro',
                'editar_membro',
                'dashboard',
                'ver_membro',
                'ver_membros',
            ];

            if (in_array($permission, $adminPermissions)) {
                return true;
            }
        }

        // ⭐ SECRETÁRIO TEM PERMISSÕES ESPECÍFICAS
        if ($this->isSecretario()) {
            $secretarioPermissions = [
                'criar_membro',
                'editar_membro',
                'dashboard',
                'ver_membro',
                'ver_membros',
                'exportar_membros',
                'view_reports',
                'manage_comments',
                'moderate_posts',
            ];

            if (in_array($permission, $secretarioPermissions)) {
                return true;
            }

            // Secretário NÃO pode excluir membros
            if ($permission === 'excluir_membro') {
                return false;
            }
        }

        // ⭐ USUÁRIO COMUM - PERMISSÕES BÁSICAS
        $userPermissions = $this->getUserPermissions();

        return in_array($permission, $userPermissions);
    }

    /**
     * Permissões base do usuário
     * ✅ EXTRAÍDO PARA MÉTODO
     */
    protected function getUserPermissions(): array
    {
        $base = [
            'ver_membro',
            'ver_membros',
            'view_profile',
            'view_cards',
        ];

        // SE ESTIVER ATIVO, MAIS PERMISSÕES
        if ($this->isAtivo()) {
            $base = array_merge($base, [
                'edit_own_profile',
                'create_posts',
                'edit_own_posts',
                'delete_own_posts',
                'comment_posts',
                'like_posts',
                'follow_users',
                'view_own_posts',
                'upload_photos',
            ]);
        }

        return $base;
    }

    /**
     * Verifica se tem uma função específica
     * ✅ CORRIGIDO
     */
    public function hasRole(string $role): bool
    {
        $roles = [
            'super-admin' => $this->isSuperAdmin(),
            'admin' => $this->isAdmin(),
            'secretario' => $this->isSecretario(),
            'usuario' => $this->isUsuario(),
            'user' => $this->isUsuario() && $this->isAtivo(),
            'inactive' => !$this->isAtivo(),
        ];

        return $roles[$role] ?? false;
    }

    /**
     * Método curto para verificar permissão
     */
    public function pode(string $permission): bool
    {
        return $this->hasPermission($permission);
    }

    /**
     * ⭐ NOVO: Verifica se pode fazer algo em um recurso específico
     * COM SUPORTE PARA NÍVEIS
     */
    public function podeEm(string $action, $resource = null): bool
    {
        // Super admin pode tudo
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin pode ações administrativas
        if ($this->isAdmin()) {
            $adminActions = ['edit', 'delete', 'update', 'moderate', 'ban', 'excluir'];
            if (in_array($action, $adminActions)) {
                return true;
            }
        }

        // Secretário pode editar mas não excluir
        if ($this->isSecretario()) {
            if ($action === 'delete' || $action === 'excluir') {
                return false;
            }
            if (in_array($action, ['edit', 'update', 'moderate'])) {
                return true;
            }
        }

        // Verifica ações específicas
        switch ($action) {
            case 'view':
                return $this->pode('ver_membro');
            
            case 'create':
                return $this->pode('criar_membro') || $this->pode('create_posts');
            
            case 'edit':
            case 'update':
                if ($resource && method_exists($resource, 'getOwnerMatricula')) {
                    return $this->matricula === $resource->getOwnerMatricula();
                }
                return $this->pode('editar_membro') || $this->pode('edit_own_posts');
            
            case 'delete':
            case 'excluir':
                if ($resource && method_exists($resource, 'getOwnerMatricula')) {
                    return $this->matricula === $resource->getOwnerMatricula();
                }
                return $this->pode('excluir_membro');
            
            case 'comment':
                return $this->pode('comment_posts');
            
            case 'like':
                return $this->pode('like_posts');
            
            case 'follow':
                return $this->pode('follow_users');
            
            default:
                return $this->pode($action);
        }
    }

    /**
     * ⭐ NOVO: Verifica se pode ver um membro específico (privacidade)
     */
    public function podeVerMembro($membro): bool
    {
        // Admin pode ver todos
        if ($this->isAdmin()) {
            return true;
        }

        // Secretário pode ver membros da sua congregação
        if ($this->isSecretario() && isset($this->congregacao) && $this->congregacao === $membro->congregacao) {
            return true;
        }

        // Ver a si mesmo
        if ($this->matricula === $membro->matricula) {
            return true;
        }

        // Se o membro tem privacidade ativada
        if ($membro->privacidade) {
            // Apenas seguidores podem ver
            return $this->segue($membro);
        }

        // Público: todos podem ver
        return true;
    }

    /**
     * ⭐ NOVO: Verifica se pode editar um membro específico
     */
    public function podeEditarMembro($membro): bool
    {
        // Admin pode editar todos
        if ($this->isAdmin()) {
            return true;
        }

        // Secretário pode editar membros da sua congregação
        if ($this->isSecretario() && isset($this->congregacao) && $this->congregacao === $membro->congregacao) {
            return true;
        }

        // Usuário pode editar a si mesmo
        return $this->matricula === $membro->matricula;
    }

    /**
     * ⭐ NOVO: Verifica se pode excluir um membro específico
     */
    public function podeExcluirMembro($membro): bool
    {
        // Apenas admin pode excluir
        return $this->isAdmin();
    }

    /**
     * ⭐ NOVO: Verifica se pode criar um membro
     */
    public function podeCriarMembro(): bool
    {
        // Admin ou Secretário podem criar
        return $this->isAdmin() || $this->isSecretario();
    }

    /**
     * ⭐ NOVO: Verifica permissões em massa
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * ⭐ NOVO: Verifica se tem todas as permissões
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * ⭐ NOVO: Retorna o nível do usuário em texto
     */
    public function getNivelTexto(): string
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
     * ⭐ NOVO: Retorna o nível do usuário com badge HTML
     */
    public function getNivelBadge(): string
    {
        if ($this->isAdmin()) {
            return '<span class="badge bg-danger">👑 Admin</span>';
        }
        if ($this->isSecretario()) {
            return '<span class="badge bg-warning text-dark">📋 Secretário</span>';
        }
        return '<span class="badge bg-secondary">👤 Membro</span>';
    }
}