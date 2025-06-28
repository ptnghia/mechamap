<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     * Kiểm tra role của user có phù hợp với route không
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Danh sách roles được phép truy cập
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = auth()->user();

        // Kiểm tra role của user có trong danh sách được phép không
        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
