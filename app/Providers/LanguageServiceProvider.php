<?php

namespace App\Providers;

use App\Services\LanguageService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Đăng ký LanguageService như singleton
        $this->app->singleton(LanguageService::class, function ($app) {
            return new LanguageService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Chia sẻ dữ liệu ngôn ngữ với tất cả views
        View::composer('*', function ($view) {
            $view->with([
                'currentLocale' => LanguageService::getCurrentLocale(),
                'currentLanguage' => LanguageService::getCurrentLanguageInfo(),
                'supportedLanguages' => LanguageService::getSupportedLocales(),
                'otherLanguages' => LanguageService::getOtherLanguages(),
                'isVietnamese' => LanguageService::getCurrentLocale() === 'vi',
                'isEnglish' => LanguageService::getCurrentLocale() === 'en',
            ]);
        });

        // Thiết lập locale cho Carbon (để format ngày tháng)
        if (class_exists(\Carbon\Carbon::class)) {
            \Carbon\Carbon::setLocale(App::getLocale());
        }

        // Thiết lập locale cho PHP
        $locale = App::getLocale();
        $localeMap = [
            'vi' => ['vi_VN.UTF-8', 'vi_VN', 'vietnamese'],
            'en' => ['en_US.UTF-8', 'en_US', 'english'],
        ];

        if (isset($localeMap[$locale])) {
            setlocale(LC_TIME, $localeMap[$locale]);
            setlocale(LC_MONETARY, $localeMap[$locale]);
            setlocale(LC_NUMERIC, $localeMap[$locale]);
        }
    }
}
