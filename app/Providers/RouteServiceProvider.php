<?php

namespace App\Providers;

use App\Contracts\RouteRegistrar;
use App\Routing\AppRegistrar;
use App\Routing\AuthRegistrar;
use App\Routing\CatalogRegistrar;
use App\Routing\ProductRegistrar;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    protected array $registrars = [
        AppRegistrar::class, AuthRegistrar::class, CatalogRegistrar::class, ProductRegistrar::class,
    ];

    public function boot(Registrar $router): void
    {
        $this->configureRateLimiting();

        $this->mapRoutes($router, $this->registrars);
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(500)
                ->by($request->user()?->id() ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response('Too many requests.', Response::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id() ?: $request->ip());
        });
    }

    protected function mapRoutes(Registrar $router, array $registrars): void
    {
        foreach ($registrars as $registrar) {
            if (! class_exists($registrar) || ! is_subclass_of($registrar, RouteRegistrar::class)) {
                throw new RuntimeException(sprintf(
                    'Cannot map routes \'%s\', it is not a valid route class',
                    $registrar
                ));
            }

            (new $registrar())->map($router);
        }
    }
}
