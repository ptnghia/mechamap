<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user has required role(s) - Updated for new role system
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Redirect to admin login for admin routes
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Bạn cần đăng nhập để truy cập trang quản trị.');
            }

            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để truy cập trang này.');
        }

        $user = auth()->user();

        // Check if user has any of the required roles using simple role column
        if (!in_array($user->role, $roles)) {
            // Redirect to appropriate dashboard or home
            return $this->redirectBasedOnRole($user);
        }

        return $next($request);
    }

    /**
     * Redirect user based on their role - Updated for new role system
     */
    private function redirectBasedOnRole($user)
    {
        // System Management roles go to admin dashboard
        $systemRoles = ['super_admin', 'system_admin', 'content_admin'];
        if (in_array($user->role, $systemRoles)) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản trị hệ thống.');
        }

        // Community Management roles go to admin dashboard
        $communityRoles = ['content_moderator', 'marketplace_moderator', 'community_moderator'];
        if (in_array($user->role, $communityRoles)) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản trị cộng đồng.');
        }

        // Business roles go to their respective dashboards
        if ($user->role === 'supplier') {
            return redirect()->route('supplier.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản lý nhà cung cấp.');
        }

        if ($user->role === 'manufacturer') {
            return redirect()->route('manufacturer.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản lý nhà sản xuất.');
        }

        if ($user->role === 'brand') {
            return redirect()->route('brand.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản lý thương hiệu.');
        }

        if ($user->role === 'verified_partner') {
            return redirect()->route('partner.dashboard')
                ->with('info', 'Bạn đã được chuyển đến trang quản lý đối tác.');
        }

        // Regular users go to home
        return redirect()->route('home')
            ->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
