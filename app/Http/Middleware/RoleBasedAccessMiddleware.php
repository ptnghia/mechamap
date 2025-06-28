<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        \Log::info('RoleBasedAccessMiddleware triggered', [
            'url' => $request->url(),
            'roles' => $roles,
            'is_admin_route' => $request->is('admin/*'),
        ]);

        // TEMPORARY: Skip this middleware for admin routes
        if ($request->is('admin/*')) {
            \Log::info('Skipping RoleBasedAccessMiddleware for admin route');
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để truy cập trang này.');
        }

        $user = auth()->user();

        // Convert roles to array and clean up
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Handle comma-separated roles like 'admin,moderator'
            if (str_contains($role, ',')) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $role));
            } else {
                $allowedRoles[] = $role;
            }
        }

        // Clean up roles (trim whitespace)
        $allowedRoles = array_map('trim', $allowedRoles);

        // Check if user has any of the required roles
        if (!in_array($user->role, $allowedRoles)) {
            // Redirect to appropriate dashboard based on role
            return $this->redirectBasedOnRole($user);
        }

        return $next($request);
    }

    /**
     * Redirect user to appropriate dashboard based on their role
     */
    private function redirectBasedOnRole($user)
    {
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

        // For regular users (Senior Member, Member, Guest)
        return redirect()->route('home')
            ->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
