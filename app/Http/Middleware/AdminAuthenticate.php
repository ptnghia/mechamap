<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem người dùng đã đăng nhập vào trang admin chưa
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Nếu đã đăng nhập, kiểm tra quyền truy cập
        $user = Auth::guard('admin')->user();

        // Lấy model User từ database
        $userModel = User::find($user->id);

        // Kiểm tra quyền truy cập
        if (!$userModel || !$userModel->canAccessAdmin()) {
            // Người dùng không có quyền admin hoặc moderator
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->with('error', 'Bạn không có quyền truy cập vào trang quản trị. Chỉ Admin và Moderator mới có quyền truy cập.');
        }

        // Cập nhật thời gian hoạt động cuối cùng
        $userModel = User::find($user->id);
        if ($userModel) {
            $userModel->last_seen_at = now();
            $userModel->save();
        }

        return $next($request);
    }
}
