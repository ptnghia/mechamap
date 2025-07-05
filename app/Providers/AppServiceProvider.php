<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL compatibility
        Schema::defaultStringLength(191);

        // Register custom Blade directives for admin permissions
        $this->registerAdminBladeDirectives();
    }

    /**
     * Register custom Blade directives for admin permission checking
     */
    private function registerAdminBladeDirectives(): void
    {
        // @adminCan directive
        Blade::directive('adminCan', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission($permission)): ?>";
        });

        // @endadminCan directive
        Blade::directive('endadminCan', function () {
            return "<?php endif; ?>";
        });

        // @adminCanAny directive
        Blade::directive('adminCanAny', function ($permissions) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyPermission($permissions)): ?>";
        });

        // @endadminCanAny directive
        Blade::directive('endadminCanAny', function () {
            return "<?php endif; ?>";
        });

        // @adminCannot directive (opposite of adminCan)
        Blade::directive('adminCannot', function ($permission) {
            return "<?php if(!auth()->check() || !auth()->user()->hasPermission($permission)): ?>";
        });

        // @endadminCannot directive
        Blade::directive('endadminCannot', function () {
            return "<?php endif; ?>";
        });
    }
}
