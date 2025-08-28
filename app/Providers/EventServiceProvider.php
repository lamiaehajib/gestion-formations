<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// NEW: Importez vos events et listeners
use App\Events\ReclamationCreated;
use App\Listeners\SendAdminNotificationOnReclamation;
use App\Events\PaymentCreated;
use App\Listeners\SendAdminNotification;

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
        // C'est ICI que tu ajoutes la liaison entre les événements et les listeners
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
        //
    }
}