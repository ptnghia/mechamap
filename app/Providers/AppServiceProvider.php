<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendWelcomeEmail;
use App\Http\View\Composers\NotificationComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register BreadcrumbService as singleton
        $this->app->singleton(\App\Services\BreadcrumbService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL compatibility
        Schema::defaultStringLength(191);

        // Force HTTPS in production and when APP_URL uses HTTPS
        if (config('app.env') === 'production' || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');

            // Additional production configurations
            if (config('app.env') === 'production') {
                $this->configureProductionSettings();
            }
        }

        // Register model observers
        \App\Models\MarketplaceProduct::observe(\App\Observers\ProductStockObserver::class);
        \App\Models\MarketplaceProduct::observe(\App\Observers\MarketplaceProductObserver::class);
        \App\Models\ProductReview::observe(\App\Observers\ProductReviewObserver::class);
        \App\Models\Message::observe(\App\Observers\MessageObserver::class);
        \App\Models\GroupMember::observe(\App\Observers\GroupMemberObserver::class);

        // Register custom Blade directives for admin permissions
        $this->registerAdminBladeDirectives();

        // Register localization Blade directives
        $this->registerLocalizationBladeDirectives();

        // Register View Composers
        $this->registerViewComposers();

        // Register event listeners
        $this->registerEventListeners();

        // Set default pagination view
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
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

    /**
     * Register custom Blade directives for localization
     */
    private function registerLocalizationBladeDirectives(): void
    {
        // @core directive
        Blade::directive('core', function ($key) {
            return "<?php echo t_core($key); ?>";
        });

        // @ui directive
        Blade::directive('ui', function ($key) {
            return "<?php echo t_ui($key); ?>";
        });

        // @content directive
        Blade::directive('content', function ($key) {
            return "<?php echo t_content($key); ?>";
        });

        // @feature directive
        Blade::directive('feature', function ($key) {
            return "<?php echo t_feature($key); ?>";
        });

        // @user directive
        Blade::directive('user', function ($key) {
            return "<?php echo t_user($key); ?>";
        });

        // @seo directive for SEO data
        Blade::directive('seo', function ($key) {
            return "<?php echo seo_value($key); ?>";
        });

        // @seo_short directive for short title
        Blade::directive('seo_short', function ($expression) {
            return "<?php echo seo_title_short($expression); ?>";
        });
    }

    /**
     * Configure production-specific settings
     */
    private function configureProductionSettings(): void
    {
        // Force root URL for production domain
        if (str_contains(config('app.url'), 'mechamap.com')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Set secure asset URL if CDN is configured and enabled
        // Note: forceAssetUrl method doesn't exist in current Laravel version
        // Use asset_url config or custom asset helper instead
        if (env('CDN_ENABLED', false) && ($cdnUrl = config('production.domain.cdn'))) {
            // Alternative approach: Set asset URL via config
            config(['app.asset_url' => $cdnUrl]);
        }

        // Configure trusted proxies for production
        $this->configureTrustedProxies();
    }

    /**
     * Configure trusted proxies for production environment
     */
    private function configureTrustedProxies(): void
    {
        // Trust common proxy headers in production
        $trustedProxies = ['*']; // Configure specific IPs in production

        if (config('production.ssl.enabled')) {
            // Additional proxy configuration for SSL termination
            $this->app['request']->setTrustedProxies(
                $trustedProxies,
                \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
            );
        }
    }

    /**
     * Register event listeners
     */
    private function registerEventListeners(): void
    {
        // Send email verification notification when user registers
        Event::listen(Registered::class, function (Registered $event) {
            if ($event->user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$event->user->hasVerifiedEmail()) {
                $event->user->sendEmailVerificationNotification();
            }
        });

        // Send welcome email after email verification
        Event::listen(Verified::class, SendWelcomeEmail::class);
    }

    /**
     * Register View Composers
     */
    private function registerViewComposers(): void
    {
        // Register NotificationComposer for layouts that need notification data
        View::composer([
            'layouts.app',
            'layouts.admin',
            'layouts.user-dashboard',
            'components.header'
        ], NotificationComposer::class);

        // Register SEO data for all views
        View::composer('*', function ($view) {
            try {
                $seoService = app(\App\Services\MultilingualSeoService::class);
                $seoData = $seoService->getSeoData(request(), app()->getLocale());

                // Xử lý title để lấy phần đầu tiên trước ký tự "|"
                $fullTitle = $seoData['title'] ?? $this->getDefaultTitle();
                $titleParts = explode('-', $fullTitle);
                $shortTitle = trim($titleParts[0]);

                // Đảm bảo có fallback values cho tất cả các trường
                $processedSeoData = array_merge([
                    'title' => $this->getDefaultTitle(),
                    'description' => $this->getDefaultDescription(),
                    'keywords' => $this->getDefaultKeywords(),
                    'og_title' => null,
                    'og_description' => null,
                    'og_image' => null,
                    'twitter_title' => null,
                    'twitter_description' => null,
                    'twitter_image' => null,
                    'canonical_url' => url()->current(),
                    'no_index' => false,
                    'extra_meta' => null,
                ], $seoData);

                $view->with([
                    'seoData' => $processedSeoData,
                    'currentSeoTitle' => $shortTitle ?: $this->getDefaultShortTitle(),
                    'currentSeoDescription' => $processedSeoData['description'],
                    'currentSeoKeywords' => $processedSeoData['keywords'],
                ]);
            } catch (\Exception $e) {
                // Fallback nếu có lỗi
                \Log::warning('SEO data loading failed: ' . $e->getMessage());
                $view->with([
                    'seoData' => [
                        'title' => $this->getDefaultTitle(),
                        'description' => $this->getDefaultDescription(),
                        'keywords' => $this->getDefaultKeywords(),
                        'canonical_url' => url()->current(),
                        'no_index' => false,
                    ],
                    'currentSeoTitle' => $this->getDefaultShortTitle(),
                    'currentSeoDescription' => $this->getDefaultDescription(),
                    'currentSeoKeywords' => $this->getDefaultKeywords(),
                ]);
            }
        });
    }

    /**
     * Get default title based on current locale
     */
    private function getDefaultTitle(): string
    {
        $locale = app()->getLocale();
        $defaultTitles = [
            'vi' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
        ];

        return $defaultTitles[$locale] ?? $defaultTitles['vi'];
    }

    /**
     * Get default short title (without site name)
     */
    private function getDefaultShortTitle(): string
    {
        $locale = app()->getLocale();
        $defaultShortTitles = [
            'vi' => 'MechaMap',
            'en' => 'MechaMap'
        ];

        return $defaultShortTitles[$locale] ?? $defaultShortTitles['vi'];
    }

    /**
     * Get default description based on current locale
     */
    private function getDefaultDescription(): string
    {
        $locale = app()->getLocale();
        $defaultDescriptions = [
            'vi' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam. Thảo luận CAD/CAM, thiết kế máy móc, công nghệ chế tạo.',
            'en' => 'Leading forum platform for Vietnam\'s mechanical engineering community. Discuss CAD/CAM, machine design, manufacturing technology.'
        ];

        return $defaultDescriptions[$locale] ?? $defaultDescriptions['vi'];
    }

    /**
     * Get default keywords
     */
    private function getDefaultKeywords(): string
    {
        $locale = app()->getLocale();
        $defaultKeywords = [
            'vi' => 'cơ khí, kỹ thuật, CAD, CAM, thiết kế máy móc, forum, cộng đồng, việt nam',
            'en' => 'mechanical engineering, CAD, CAM, machine design, forum, community, vietnam'
        ];

        return $defaultKeywords[$locale] ?? $defaultKeywords['vi'];
    }
}
