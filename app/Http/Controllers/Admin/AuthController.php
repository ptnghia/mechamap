<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập admin
     */
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Xử lý đăng nhập admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Xác định trường đăng nhập (email hoặc username)
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Tạo credentials
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        // Kiểm tra xem người dùng có tồn tại không
        $user = User::where($loginField, $request->login)->first();

        // Nếu không tìm thấy bằng email/username, thử tìm bằng cách khác
        if (!$user && $loginField === 'email') {
            $user = User::where('username', $request->login)->first();
            if ($user) {
                $credentials = ['username' => $request->login, 'password' => $request->password];
            }
        } elseif (!$user && $loginField === 'username') {
            $user = User::where('email', $request->login)->first();
            if ($user) {
                $credentials = ['email' => $request->login, 'password' => $request->password];
            }
        }

        // Kiểm tra quyền admin hoặc moderator
        if (!$user) {
            return back()->withErrors([
                'login' => 'Không tìm thấy tài khoản với ' . ($loginField == 'email' ? 'email' : 'username') . ' này.',
            ])->onlyInput('login');
        }

        if (!$user->canAccessAdmin()) {
            return back()->withErrors([
                'login' => 'Bạn không có quyền truy cập vào trang quản trị. Chỉ Admin và Moderator mới có quyền truy cập.',
            ])->onlyInput('login');
        }

        // Thử đăng nhập
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Đăng nhập thành công
            $request->session()->regenerate();

            // Detect and track device for security
            $authenticatedUser = Auth::user();
            \App\Services\DeviceDetectionService::detectAndTrackDevice($authenticatedUser, $request);

            // Ghi log hoạt động đăng nhập (có thể thêm sau này)

            // Thông báo thành công và chuyển hướng
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Đăng nhập thành công. Chào mừng ' . $authenticatedUser->name . '!');
        }

        // Đăng nhập thất bại
        return back()->withErrors([
            'login' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('login');
    }

    /**
     * Đăng xuất khỏi trang admin
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * GET route để logout (redirect đến POST)
     */
    public function logoutGet(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất thành công.');
    }
}
