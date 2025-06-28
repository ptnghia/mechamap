<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirectIfUnauthenticated
{
    /**
     * Handle an incoming request.
     * Custom middleware for admin routes to redirect to admin login instead of frontend login
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated using web guard (default)
        if (!Auth::check()) {
            // Generate admin login URL properly to handle /public/ prefix issue
            $adminLoginUrl = $this->generateAdminLoginUrl($request);

            return redirect($adminLoginUrl)
                ->with('error', 'Bạn cần đăng nhập để truy cập trang quản trị.');
        }

        return $next($request);
    }

    /**
     * Generate proper admin login URL to handle /public/ prefix from .htaccess rewrite
     */
    private function generateAdminLoginUrl(Request $request): string
    {
        // Check if request is coming through /public/ prefix (from root .htaccess rewrite)
        $requestUri = $request->getRequestUri();

        if (str_starts_with($requestUri, '/public/')) {
            // If request has /public/ prefix, use absolute URL without /public/
            return str_replace('/public/', '/', url('/admin/login'));
        }

        // Normal case - use route helper
        return route('admin.login');
    }
}
