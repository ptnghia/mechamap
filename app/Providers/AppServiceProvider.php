<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
    }
}
