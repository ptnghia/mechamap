<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký middleware toàn cục
        $middleware->append(\App\Http\Middleware\TrackUserActivity::class);
        $middleware->append(\App\Http\Middleware\ApplySeoSettings::class);
        // Removed HandleCors from here - it's already in Kernel.php

        // Đăng ký alias cho middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAccess::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'verified.social' => \App\Http\Middleware\EnsureEmailIsVerifiedOrSocialLogin::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
