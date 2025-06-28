<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            // Redirect to admin login for admin routes
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            
            // Redirect to frontend login for other routes
            return route('login');
        }
        
        return null;
    }
}
