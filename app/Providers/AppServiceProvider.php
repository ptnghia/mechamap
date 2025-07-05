<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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
        // Đặt độ dài mặc định cho chuỗi trong migration
        Schema::defaultStringLength(191);

        // Force HTTPS và absolute URLs
        if (config('app.env') === 'production' || config('app.url')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Sử dụng Bootstrap 5 cho phân trang
        Paginator::useBootstrapFive();

        // Register view composers for admin header
        View::composer('admin.layouts.partials.dason-header', \App\Http\View\Composers\AdminHeaderComposer::class);

        // Register Blade directives for admin permissions
        Blade::if('adminCan', function ($permission) {
            return \App\Helpers\AdminPermissionHelper::can($permission);
        });

        // Register Blade directives for versioned assets
        Blade::directive('css', function ($expression) {
            return "<?php echo '<link rel=\"stylesheet\" href=\"' . asset_versioned($expression) . '\">'; ?>";
        });

        Blade::directive('js', function ($expression) {
            return "<?php echo '<script src=\"' . asset_versioned($expression) . '\"></script>'; ?>";
        });
    }
}
