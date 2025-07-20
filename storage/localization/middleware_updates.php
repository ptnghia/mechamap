<?php

/**
 * Middleware Updates for New Localization
 * Update existing Localization middleware
 */

// Add to existing middleware handle() method
public function handle($request, Closure $next)
{
    // Existing locale detection logic...

    // Set new lang path if enabled
    if (config('localization.new_structure.enabled')) {
        app()->setLocale($locale);
        
        // Update translator to use new structure
        app('translator')->setFallback([
            resource_path('lang_new/' . $locale),
            resource_path('lang/' . $locale) // fallback to old
        ]);
    }

    return $next($request);
}
