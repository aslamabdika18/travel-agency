<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        // Configure web routes with CORS and CSRF protection
        $middleware->web([
            \App\Http\Middleware\CorsMiddleware::class,
            \App\Http\Middleware\HandleCsrfToken::class,
        ]);

        // Enable session middleware for API routes to support cookie-based auth
        $middleware->api([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
        
        // Configure authentication redirect
        $middleware->redirectGuestsTo('/auth');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
