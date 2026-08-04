<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // Registered directly rather than via `artisan install:api`, which would
        // pull in Sanctum for an app that has no authentication.
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        // Single login page for every role — admin-area guests land on the
        // same /login as everyone else rather than a separate admin form.
        $middleware->redirectGuestsTo(fn () => route('login'));

        // An already-authenticated visitor hitting /login (or any guest-only
        // route) is sent to whichever dashboard matches the guard that's
        // actually signed in, not the URL they happened to land on.
        $middleware->redirectUsersTo(
            fn () => Auth::guard('admin')->check() ? route('admin.dashboard') : route('dashboard')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
