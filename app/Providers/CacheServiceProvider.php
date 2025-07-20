<?php

namespace App\Providers;

use App\Events\ModelCreatedEvent;
use App\Events\ModelDeletedEvent;
use App\Events\ModelUpdatedEvent;
use App\Listeners\FlushModelCacheListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(ModelCreatedEvent::class, FlushModelCacheListener::class);
        Event::listen(ModelUpdatedEvent::class, FlushModelCacheListener::class);
        Event::listen(ModelDeletedEvent::class, FlushModelCacheListener::class);
    }
}
