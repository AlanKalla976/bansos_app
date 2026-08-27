<?php

use Illuminate\Http\Request;
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

        // Middleware Alias
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AuthAdmin::class,
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Redirect ketika session/login habis
        $middleware->redirectGuestsTo(function (Request $request) {

            // Jika membuka halaman admin
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            // Selain admin diarahkan ke login user
            return route('user.login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();