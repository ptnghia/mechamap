<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PermissionService;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Kiểm tra permission cụ thể của user
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Permission cần kiểm tra
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = auth()->user();

        // Kiểm tra permission
        if (!PermissionService::hasPermission($user, $permission)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        return $next($request);
    }
}
