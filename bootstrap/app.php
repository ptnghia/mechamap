<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Do not append as global middleware - these should only be for web routes
        // $middleware->append(\App\Http\Middleware\TrackUserActivity::class);
        // $middleware->append(\App\Http\Middleware\ApplySeoSettings::class);

        // Override middleware groups to ensure clean API middleware
        $middleware->group('api', [
            \App\Http\Middleware\ApiRateLimit::class,
            \App\Http\Middleware\StandardizeApiResponse::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\Localization::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\TrackUserActivity::class,
            \App\Http\Middleware\ApplySeoSettings::class,
        ]);

        // Đăng ký alias cho middleware
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminAccess::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.permission' => \App\Http\Middleware\CheckAdminPermission::class,
            'admin.redirect' => \App\Http\Middleware\AdminRedirectIfUnauthenticated::class,
            'verified.social' => \App\Http\Middleware\EnsureEmailIsVerifiedOrSocialLogin::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'forum.cache' => \App\Http\Middleware\ForumCacheMiddleware::class,
            'download.access' => \App\Http\Middleware\VerifyDownloadAccess::class,
            'role' => \App\Http\Middleware\RoleBasedAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
