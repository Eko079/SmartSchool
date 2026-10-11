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
        // Percayai proxy Cloudflare/VPS agar skema https dari X-Forwarded-Proto terbaca.
        $middleware->trustProxies(at: '*');
        // Callback server-ke-server Midtrans tidak membawa token CSRF.
        $middleware->validateCsrfTokens(except: ['midtrans/callback']);
        $middleware->alias([
            'wali' => \App\Http\Middleware\EnsureWali::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'super' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
