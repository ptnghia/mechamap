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
        // Debug logging
        \Log::info('AdminAuthenticate middleware triggered', [
            'url' => $request->url(),
            'auth_check' => Auth::check(),
            'session_id' => session()->getId(),
        ]);

        // Kiểm tra xem người dùng đã đăng nhập chưa (sử dụng web guard)
        if (!Auth::check()) {
            \Log::warning('User not authenticated, redirecting to admin login', [
                'url' => $request->url(),
                'session_id' => session()->getId(),
            ]);
            return redirect()->route('admin.login');
        }

        // Nếu đã đăng nhập, kiểm tra quyền truy cập
        $user = Auth::user();

        \Log::info('User authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'role_group' => $user->role_group,
            'can_access_admin' => $user->canAccessAdmin(),
        ]);

        // Kiểm tra quyền truy cập admin
        if (!$user || !$user->canAccessAdmin()) {
            \Log::warning('User does not have admin access', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'role_group' => $user->role_group,
            ]);
            // Người dùng không có quyền admin hoặc moderator
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập vào trang quản trị. Chỉ Admin và Moderator mới có quyền truy cập.');
        }

        // Cập nhật thời gian hoạt động cuối cùng
        $user->last_seen_at = now();
        $user->save();

        \Log::info('AdminAuthenticate middleware passed', [
            'user_id' => $user->id,
            'url' => $request->url(),
        ]);

        return $next($request);
    }
}
