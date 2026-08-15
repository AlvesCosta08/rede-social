<?php

namespace App\Providers;

use App\Models\Membro;
use App\Models\Publicacao;
use App\Models\Comentario;
use App\Policies\MembroPolicy;
use App\Policies\PublicacaoPolicy;
use App\Policies\ComentarioPolicy;
use App\Policies\SeguidorPolicy;
use App\Policies\PerfilPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Membro::class => MembroPolicy::class,
        Publicacao::class => PublicacaoPolicy::class,
        Comentario::class => ComentarioPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ⭐ GATE PARA ADMIN
        Gate::define('admin', function (Membro $user) {
            return $user->isAdmin();
        });

        // ⭐ GATE PARA SECRETÁRIO
        Gate::define('secretario', function (Membro $user) {
            return $user->isSecretario();
        });

        // ⭐ GATE PARA PERMISSÕES DINÂMICAS
        Gate::define('permissao', function (Membro $user, string $permission, $target = null) {
            return $user->pode($permission, $target);
        });

        // ⭐ GATE PARA VER MEMBRO
        Gate::define('ver-membro', function (Membro $user, Membro $target) {
            return $user->pode('ver_membro', $target);
        });

        // ⭐ GATE PARA EDITAR MEMBRO
        Gate::define('editar-membro', function (Membro $user, Membro $target) {
            return $user->pode('editar_membro', $target);
        });

        // ⭐ GATE PARA EXCLUIR MEMBRO
        Gate::define('excluir-membro', function (Membro $user, Membro $target) {
            return $user->pode('excluir_membro', $target);
        });

        // ⭐ GATE PARA CRIAR MEMBRO
        Gate::define('criar-membro', function (Membro $user) {
            return $user->pode('criar_membro');
        });
    }
}