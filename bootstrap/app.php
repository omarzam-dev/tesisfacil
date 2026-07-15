<?php

use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\ValidarLicencia;
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
            'licencia' => ValidarLicencia::class, // ya no se usa, queda por si se retoma el modelo ZSystem Platform
            'check.subscription' => CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
