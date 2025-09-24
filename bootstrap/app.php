<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/frontend')->group(function () {
                require base_path('routes/frontend.php');
            });
        }
    )
    ->withMiddleware(function ($middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
