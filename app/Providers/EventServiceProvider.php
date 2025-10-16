<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Events and Listeners
use App\Events\ReclamationCreated;
use App\Listeners\SendAdminNotificationOnReclamation;
use App\Events\PaymentCreated;
use App\Listeners\SendAdminNotification;

// 🎯 NEW: Observer for auto-assignment to promotions
use App\Models\Inscription;
use App\Observers\InscriptionObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReclamationCreated::class => [
            SendAdminNotificationOnReclamation::class,
        ],
        PaymentCreated::class => [
            SendAdminNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // 🎯 Register Inscription Observer for auto-promotion assignment
        Inscription::observe(InscriptionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}