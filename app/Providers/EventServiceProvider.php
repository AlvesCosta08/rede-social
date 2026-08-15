<?php

namespace App\Providers;

use App\Events\MembroRegistered;
use App\Events\MembroUpdated;
use App\Events\PublicacaoCreated;
use App\Events\PublicacaoDeleted;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\LogMemberActivity;
use App\Listeners\NotifyFollowers;
use App\Listeners\UpdateMemberStats;
use App\Listeners\ClearUserCache;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        // ⭐ EVENTOS DO LARAVEL
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // ⭐ EVENTOS PERSONALIZADOS - MEMBRO
        MembroRegistered::class => [
            SendWelcomeEmail::class,
            LogMemberActivity::class,
            ClearUserCache::class,
        ],
        MembroUpdated::class => [
            LogMemberActivity::class,
            ClearUserCache::class,
        ],

        // ⭐ EVENTOS PERSONALIZADOS - PUBLICAÇÃO
        PublicacaoCreated::class => [
            LogMemberActivity::class,
            NotifyFollowers::class,
            UpdateMemberStats::class,
        ],
        PublicacaoDeleted::class => [
            UpdateMemberStats::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}