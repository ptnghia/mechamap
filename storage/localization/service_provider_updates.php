<?php

/**
 * Service Provider Updates
 * Add to app/Providers/AppServiceProvider.php
 */

public function boot()
{
    // Set new localization path
    if (config('localization.new_structure.enabled', true)) {
        $this->app['translator']->addNamespace('lang_new', resource_path('lang_new'));
    }

    // Register Blade directives
    $this->registerBladeDirectives();

    // Register helper functions
    require_once app_path('Helpers/TranslationHelper.php');
}

private function registerBladeDirectives()
{
    // Include blade directives here
    // See blade_directives.php for implementation
}
