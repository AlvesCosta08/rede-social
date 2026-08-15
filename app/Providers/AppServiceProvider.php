<?php

namespace App\Providers;

use App\Models\Membro;
use App\Observers\MembroObserver;
use App\Contracts\Repositories\MembroRepositoryInterface;
use App\Contracts\Repositories\PublicacaoRepositoryInterface;
use App\Repositories\MembroRepository;
use App\Repositories\PublicacaoRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ⭐ REPOSITORY PATTERN - BINDING DAS INTERFACES COM IMPLEMENTAÇÕES
        $this->app->bind(
            MembroRepositoryInterface::class,
            MembroRepository::class
        );

        $this->app->bind(
            PublicacaoRepositoryInterface::class,
            PublicacaoRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ⭐ REGISTRA O OBSERVER PARA O MODEL MEMBRO
        Membro::observe(MembroObserver::class);

        // ⭐ FORÇA HTTPS EM PRODUÇÃO
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}