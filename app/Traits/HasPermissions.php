<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Verifica se o usuário é administrador
     */
    public function isAdmin(): bool
    {
        return (bool) $this->admin;
    }

    /**
     * Verifica se é super admin (apenas matrícula 1)
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->matricula === '1';
    }

    /**
     * Verifica se tem uma permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin tem tudo
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin tem permissões administrativas
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
                'view_admin_panel'
            ];
            return in_array($permission, $adminPermissions);
        }

        // Usuário comum tem permissões básicas
        $userPermissions = [
            'view_members',
            'view_profile',
            'edit_own_profile',
            'create_posts',
            'edit_own_posts',
            'delete_own_posts',
            'comment_posts',
            'like_posts',
            'follow_users',
            'view_cards'
        ];
        return in_array($permission, $userPermissions);
    }

    /**
     * Verifica se tem uma função específica
     */
    public function hasRole(string $role): bool
    {
        $roles = [
            'admin' => $this->isAdmin(),
            'super-admin' => $this->isSuperAdmin(),
            'user' => !$this->isAdmin(),
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
}