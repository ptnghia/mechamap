<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Services\BreadcrumbService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Share Breadcrumbs Middleware
 * 
 * Automatically shares breadcrumbs with all views
 */
class ShareBreadcrumbs
{
    /**
     * The breadcrumb service instance.
     */
    protected $breadcrumbService;

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
        try {
            // Generate breadcrumbs for current request
            $breadcrumbs = $this->breadcrumbService->generate($request);
            
            // Share breadcrumbs with all views
            View::share('breadcrumbs', $breadcrumbs);
            
        } catch (\Exception $e) {
            // Fallback to empty breadcrumbs if service fails
            \Log::warning('ShareBreadcrumbs middleware failed: ' . $e->getMessage());
            View::share('breadcrumbs', []);
        }

        return $next($request);
    }
}
