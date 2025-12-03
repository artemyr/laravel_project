<?php

use App\Http\Middleware\SeoMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SeoMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'payment/callback'
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call('model:prune')->monthly();
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (DomainException $e) {
            return session()->previousUrl()
                ? back()
                : redirect()->route('home');
        });

        $exceptions->report(function (DomainException $e) {
            flash()->alert($e->getMessage());
        })->stop();
    })->create();
