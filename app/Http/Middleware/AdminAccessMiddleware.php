<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     * Only Admin and Moderator can access /admin routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AdminAccessMiddleware triggered', [
            'url' => $request->url(),
            'auth_check' => auth()->check(),
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            \Log::warning('User not authenticated in AdminAccessMiddleware');
            return redirect()->route('admin.login')
                ->with('error', 'Bạn cần đăng nhập để truy cập trang quản trị.');
        }

        $user = auth()->user();

        \Log::info('AdminAccessMiddleware user check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'can_access_admin' => $user->can('access-admin'),
        ]);

        // Use Laravel native Gate for admin access check
        if (!$user->can('access-admin')) {
            \Log::warning('User cannot access admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);
            // Redirect to appropriate dashboard based on role
            return $this->redirectToUserDashboard($user);
        }

        \Log::info('AdminAccessMiddleware passed', [
            'user_id' => $user->id,
            'url' => $request->url(),
        ]);

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    private function redirectToUserDashboard($user)
    {
        // Check user roles and redirect accordingly using simple role column
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

        // For regular users (Senior Member, Member, Guest)
        return redirect()->route('home')
            ->with('error', 'Bạn không có quyền truy cập trang quản trị.');
    }
}
