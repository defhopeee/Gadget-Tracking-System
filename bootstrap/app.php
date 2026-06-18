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
    ->withMiddleware(function (Middleware $middleware): void {
        // Devices report their location without a web session / CSRF token;
        // they authenticate with their per-gadget API key instead.
        $middleware->validateCsrfTokens(except: [
            'api/reportlocation',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
