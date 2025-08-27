<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Registration wizard rate limiters
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('field-validation', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('username-check', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('auto-save', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Custom route model bindings
        $this->configureRouteModelBindings();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        });
    }

    /**
     * Configure custom route model bindings.
     */
    protected function configureRouteModelBindings(): void
    {
        // Custom Thread binding - support both slug and ID
        Route::bind('thread', function ($value) {
            // Try to find by slug first (primary route key)
            $thread = \App\Models\Thread::where('slug', $value)->first();

            // If not found by slug and value looks like an ID, try by ID
            if (!$thread && is_numeric($value)) {
                $thread = \App\Models\Thread::find($value);
            }

            // If still not found, throw 404
            if (!$thread) {
                abort(404, 'Thread not found');
            }

            return $thread;
        });
    }
}
