<?php

use App\Http\Middleware\PetMiddleware;
use App\Http\Middleware\ShelterMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
                ->withRouting(
                        web: __DIR__ . '/../routes/web.php', // ✅ Ensure this path is correct!
                        commands: __DIR__ . '/../routes/console.php',
                        health: '/up',
                )
                ->withMiddleware(function (Middleware $middleware) {
                    // Append middleware to the global stack
                    $middleware->append(PetMiddleware::class);
                    $middleware->append(ShelterMiddleware::class);
                    //$middleware->append(AdminMiddleware::class);
                    $middleware->append(\App\Http\Middleware\CheckUserRestricted::class);

                    // Register middleware aliases
                    $middleware->alias([
                        'pet' => PetMiddleware::class,
                        'shelter' => ShelterMiddleware::class,
                        'admin' => AdminMiddleware::class,
                        'check.restricted' => \App\Http\Middleware\CheckUserRestricted::class,
                    ]);
                })
                ->withExceptions(function (Exceptions $exceptions) {
                    // Exception handling configuration
                })
                ->create();

// ✅ Register scheduled tasks separately
app()->resolving(Schedule::class, function (Schedule $schedule) {
    $schedule->command('appointments:expire')->daily(); // Run daily at midnight
});
