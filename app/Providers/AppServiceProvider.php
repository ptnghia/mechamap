<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\CacheInvalidationObserver;
use App\Observers\MarketplaceOrderObserver;
use App\Models\Thread;
use App\Models\Post;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register LayoutHelper
        require_once app_path('Helpers/LayoutHelper.php');
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

        Blade::if('adminCanAny', function ($permissions) {
            return \App\Helpers\AdminPermissionHelper::canAny($permissions);
        });

        Blade::if('adminCanAll', function ($permissions) {
            return \App\Helpers\AdminPermissionHelper::canAll($permissions);
        });

        Blade::if('isAdmin', function () {
            return \App\Helpers\AdminPermissionHelper::isAdmin();
        });

        Blade::if('isModerator', function () {
            return \App\Helpers\AdminPermissionHelper::isModerator();
        });

        // Register cache invalidation observers for database optimization
        Thread::observe(CacheInvalidationObserver::class);
        Post::observe(CacheInvalidationObserver::class);
        MarketplaceProduct::observe(CacheInvalidationObserver::class);
        User::observe(CacheInvalidationObserver::class);
        Category::observe(CacheInvalidationObserver::class);
        Forum::observe(CacheInvalidationObserver::class);
        Notification::observe(CacheInvalidationObserver::class);

        // Register marketplace order observer for download access
        MarketplaceOrder::observe(CacheInvalidationObserver::class);
        MarketplaceOrder::observe(MarketplaceOrderObserver::class);
    }
}
