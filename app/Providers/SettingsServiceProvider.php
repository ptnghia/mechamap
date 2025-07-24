<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Chỉ load settings khi database đã sẵn sàng
        if (Schema::hasTable('settings')) {
            // Chia sẻ settings cơ bản với tất cả views
            View::composer('*', function ($view) {
                $view->with([
                    'siteSettings' => $this->getSiteSettings(),
                ]);
            });
        }
    }

    /**
     * Lấy các settings cơ bản của site với caching
     */
    private function getSiteSettings(): array
    {
        return Cache::remember('global_site_settings', 3600, function () {
            $settings = Setting::whereIn('key', [
                'site_name',
                'site_slogan', 
                'site_welcome_message',
                'site_logo',
                'site_favicon',
                'site_description',
                'site_language',
                'site_timezone',
                'site_maintenance_mode',
                'site_maintenance_message'
            ])->pluck('value', 'key')->toArray();

            return [
                'name' => $settings['site_name'] ?? 'MechaMap',
                'slogan' => $settings['site_slogan'] ?? 'Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                'welcome_message' => $settings['site_welcome_message'] ?? 'Chào mừng bạn đến với MechaMap',
                'logo' => $settings['site_logo'] ?? '/images/logo.png',
                'favicon' => $settings['site_favicon'] ?? '/favicon.ico',
                'description' => $settings['site_description'] ?? 'Cộng đồng kỹ sư cơ khí Việt Nam',
                'language' => $settings['site_language'] ?? 'vi',
                'timezone' => $settings['site_timezone'] ?? 'Asia/Ho_Chi_Minh',
                'maintenance_mode' => ($settings['site_maintenance_mode'] ?? '0') === '1',
                'maintenance_message' => $settings['site_maintenance_message'] ?? 'Website đang bảo trì',
            ];
        });
    }
}
