<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful; // هذا هو المسار الصحيح

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // تأكد من وجود هذا السطر
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
         $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class, // أضف هذا السطر
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
          $middleware->alias([
        'user.type' => \App\Http\Middleware\CheckUserType::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
