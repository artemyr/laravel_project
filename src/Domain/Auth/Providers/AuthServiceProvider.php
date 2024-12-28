<?php

namespace Domain\Auth\Providers;


use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
