<?php

/**
 * Blade Directives for New Localization
 * Add these to app/Providers/AppServiceProvider.php boot() method
 */

use Illuminate\Support\Facades\Blade;

// Core translations: @core('auth.login.title')
Blade::directive('core', function ($expression) {
    return "<?php echo __('core.' . $expression); ?>";
});

// UI translations: @ui('buttons.save')
Blade::directive('ui', function ($expression) {
    return "<?php echo __('ui.' . $expression); ?>";
});

// Content translations: @content('home.hero.title')
Blade::directive('content', function ($expression) {
    return "<?php echo __('content.' . $expression); ?>";
});

// Feature translations: @feature('forum.threads.create')
Blade::directive('feature', function ($expression) {
    return "<?php echo __('features.' . $expression); ?>";
});

// User translations: @user('profile.edit.title')
Blade::directive('user', function ($expression) {
    return "<?php echo __('user.' . $expression); ?>";
});

// Admin translations: @admin('dashboard.overview.title')
Blade::directive('admin', function ($expression) {
    return "<?php echo __('admin.' . $expression); ?>";
});
