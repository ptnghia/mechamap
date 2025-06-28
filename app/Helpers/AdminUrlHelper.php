<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class AdminUrlHelper
{
    /**
     * Generate admin URL that handles /public/ prefix correctly
     */
    public static function adminUrl(string $path = '', Request $request = null): string
    {
        $request = $request ?: request();
        $requestUri = $request->getRequestUri();
        
        // Remove leading slash from path if present
        $path = ltrim($path, '/');
        
        // Check if request is coming through /public/ prefix
        if (str_starts_with($requestUri, '/public/')) {
            return url("/admin/{$path}");
        }
        
        // Normal case
        return url("/admin/{$path}");
    }
    
    /**
     * Generate admin login URL that handles /public/ prefix correctly
     */
    public static function adminLoginUrl(Request $request = null): string
    {
        return self::adminUrl('login', $request);
    }
    
    /**
     * Generate admin dashboard URL that handles /public/ prefix correctly
     */
    public static function adminDashboardUrl(Request $request = null): string
    {
        return self::adminUrl('', $request);
    }
}
