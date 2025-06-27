<?php

namespace Domain\Catalog\Providers;

use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(
            ActionsServiceProvider::class
        );
    }

    public function boot(): void
    {
    }
}
