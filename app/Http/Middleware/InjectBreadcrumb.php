<?php

namespace App\Http\Middleware;

use App\Services\BreadcrumbService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class InjectBreadcrumb
{
    /**
     * The breadcrumb service instance.
     */
    protected BreadcrumbService $breadcrumbService;

    /**
     * Create a new middleware instance.
     */
    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip breadcrumb injection for certain routes
        if ($this->shouldSkipBreadcrumb($request)) {
            return $next($request);
        }

        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generate($request);

        // Share breadcrumbs with all views
        View::share('breadcrumbs', $breadcrumbs);

        return $next($request);
    }

    /**
     * Determine if breadcrumb should be skipped for this request
     */
    private function shouldSkipBreadcrumb(Request $request): bool
    {
        $routeName = $request->route()?->getName();
        $path = $request->path();

        // Skip for admin routes
        if (str_starts_with($path, 'admin/') || str_starts_with($routeName ?? '', 'admin.')) {
            return true;
        }

        // Skip for API routes
        if (str_starts_with($path, 'api/') || str_starts_with($routeName ?? '', 'api.')) {
            return true;
        }

        // Skip for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        // Skip for specific routes that don't need breadcrumbs
        $skipRoutes = [
            'login',
            'register',
            'password.request',
            'password.reset',
            'verification.notice',
            'verification.verify',
            'logout',
            'avatar.generate',
            'avatar.clear-cache',
            'language.switch',
            'theme.dark-mode',
            'theme.original-view',
        ];

        if (in_array($routeName, $skipRoutes)) {
            return true;
        }

        // Skip for file downloads and uploads
        if (str_contains($path, '/download/') || str_contains($path, '/upload/')) {
            return true;
        }

        // Skip for webhook routes
        if (str_contains($path, 'webhook') || str_contains($path, 'callback')) {
            return true;
        }

        return false;
    }
}
