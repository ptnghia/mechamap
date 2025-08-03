<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendWelcomeEmail;

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

        // Register custom Blade directives for admin permissions
        $this->registerAdminBladeDirectives();

        // Register localization Blade directives
        $this->registerLocalizationBladeDirectives();

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
}
