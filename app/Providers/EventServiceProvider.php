<?php

namespace App\Providers;

use App\Events\AfterSessionRegenerated;
use Domain\Cart\CartManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
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
        Event::listen(AfterSessionRegenerated::class, function (AfterSessionRegenerated $event) {
            app(CartManager::class)->updateStorageId(
                $event->old,
                $event->current
            );
        });
    }
}
