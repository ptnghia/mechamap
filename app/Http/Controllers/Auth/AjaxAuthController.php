<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AjaxAuthController extends Controller
{
    /**
     * Handle an AJAX login request.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            // Determine if login is email or username
            $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            // Attempt to authenticate
            if (Auth::attempt([$loginField => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
                $request->session()->regenerate();

                // Detect and track device for security
                $user = Auth::user();
                \App\Services\DeviceDetectionService::detectAndTrackDevice($user, $request);

                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công.',
                    'redirect' => route('dashboard')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 422);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đăng nhập.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle an AJAX registration request.
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class, 'alpha_dash'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'account_type' => ['required', 'string', 'in:member,student,manufacturer,supplier,brand'],
            ]);

            // Xác định role_group dựa trên account_type
            $roleGroup = $this->getRoleGroup($request->account_type);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->account_type,
                'role_group' => $roleGroup,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công.',
                'redirect' => route('dashboard')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký không thành công.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đăng ký.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle an AJAX forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => __($status)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __($status)
                ], 422);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi liên kết đặt lại mật khẩu.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi gửi liên kết đặt lại mật khẩu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xác định role_group dựa trên account_type
     *
     * @param string $accountType
     * @return string
     */
    private function getRoleGroup(string $accountType): string
    {
        return match ($accountType) {
            'member', 'student' => 'community_members',
            'manufacturer', 'supplier', 'brand' => 'business_partners',
            default => 'community_members'
        };
    }
}
