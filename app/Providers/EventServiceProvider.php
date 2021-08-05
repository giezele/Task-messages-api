<?php

namespace App\Providers;

use App\Events\MessageWasViewed;
use App\Events\MessageCreated;
use App\Events\MessageUpdated;
use App\Events\MessageDeleted;
use App\Listeners\MessageDeletedListener;
use App\Listeners\MessageUpdatedListener;
use App\Listeners\MessageCreatedListener;
use App\Listeners\MessageWasViewedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageWasViewed::class => [
            MessageWasViewedListener::class,
        ],
        MessageCreated::class => [
            MessageCreatedListener::class,
        ],
        MessageUpdated::class => [
            MessageUpdatedListener::class,
        ],
        MessageDeleted::class => [
            MessageDeletedListener::class,
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
