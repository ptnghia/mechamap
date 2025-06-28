<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        // Kiểm tra quyền admin cơ bản
        if (!in_array($user->role, ['admin', 'moderator'])) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Nếu có permission cụ thể được chỉ định
        if ($permission) {
            // Admin có tất cả quyền
            if ($user->role === 'admin') {
                return $next($request);
            }

            // Kiểm tra quyền cụ thể cho moderator
            if ($user->role === 'moderator') {
                $moderatorPermissions = [
                    'view_users',
                    'edit_users',
                    'view_threads',
                    'edit_threads',
                    'delete_threads',
                    'view_comments',
                    'edit_comments',
                    'delete_comments',
                    'view_reports',
                    'view-reports', // New format
                    'manage_reports',
                    'manage-reports', // New format
                    'handle_reports',
                    'moderate-content', // New format
                    'view_showcases',
                    'edit_showcases',
                    'view_settings_general',
                    'view_settings_forum',
                    'view_settings_user',
                ];

                if (!in_array($permission, $moderatorPermissions)) {
                    abort(403, 'Bạn không có quyền thực hiện hành động này.');
                }
            }
        }

        return $next($request);
    }
}
