<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $permission
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Kiểm tra authentication
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        // Kiểm tra quyền admin cơ bản
        if (!$user->canAccessAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        // Nếu có permission cụ thể được chỉ định
        if ($permission) {
            if (!$user->hasPermission($permission)) {
                // Log unauthorized access attempt
                \Log::warning('Unauthorized admin access attempt', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_permission' => $permission,
                    'route' => $request->route()->getName(),
                    'url' => $request->url(),
                    'ip' => $request->ip(),
                ]);

                abort(403, 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        // Kiểm tra quyền truy cập route dựa trên route name
        $routeName = $request->route()->getName();
        if ($routeName && !$user->canAccessRoute($routeName)) {
            \Log::warning('Unauthorized route access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'route_name' => $routeName,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
