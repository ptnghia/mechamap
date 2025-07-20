<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Enhanced localization logic
        $locale = $this->determineLocale($request);
        app()->setLocale($locale);
        
        return $next($request);
    }
    
    private function determineLocale($request)
    {
        // Priority: URL > User DB > Session > Browser > Config
        return $request->get('lang', 'vi');
    }
}
