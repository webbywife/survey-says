<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        // Offline sync endpoint uses respondent_token for deduplication, not CSRF
        $middleware->validateCsrfTokens(except: [
            '/s/*/sync',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
