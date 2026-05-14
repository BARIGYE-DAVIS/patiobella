<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\StoreMiddleware;
use App\Http\Middleware\DirectorMiddleware;
use App\Http\Middleware\ProcurementMiddleware;
use App\Http\Middleware\ManagementMiddleware;
use App\Http\Middleware\KitchenMiddleware;
use App\Http\Middleware\CashierMiddleware;
use App\Http\Middleware\RestaurantMiddleware; // ← add this import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'store'       => StoreMiddleware::class,
            'procurement' => ProcurementMiddleware::class,
            'management'  => ManagementMiddleware::class,
            'director'    => DirectorMiddleware::class,
            'kitchen'     => KitchenMiddleware::class,
            'restaurant'  => RestaurantMiddleware::class, // ← use the import
            'cashier'     => CashierMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
