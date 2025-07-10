<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

use App\Models\SocialAccount;

class AuthController extends Controller
{
    /**
     * Handle a login request to the API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Kiểm tra xem request có chứa email hay username
            if ($request->has('email')) {
                $request->validate([
                    'email' => 'required|string|email',
                    'password' => 'required|string',
                    'remember_me' => 'boolean',
                ]);

                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password
                ];

                // Tìm user theo email
                $user = User::where('email', $request->email)->first();
            } elseif ($request->has('username')) {
                $request->validate([
                    'username' => 'required|string',
                    'password' => 'required|string',
                    'remember_me' => 'boolean',
                ]);

                $credentials = [
                    'username' => $request->username,
                    'password' => $request->password
                ];

                // Tìm user theo username
                $user = User::where('username', $request->username)->first();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp email hoặc tên đăng nhập.'
                ], 422);
            }

            // Kiểm tra xem user có tồn tại không
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thông tin đăng nhập không chính xác.'
                ], 401);
            }

            // Attempt to authenticate
            if (!Auth::attempt($credentials, $request->boolean('remember_me'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thông tin đăng nhập không chính xác.'
                ], 401);
            }

            // Lấy user đã xác thực
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Create token (method từ HasApiTokens trait của Laravel Sanctum)
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công.',
                'data' => [
                    'user' => $user,
                    'tokens' => [
                        'access_token' => $token,
                        'refresh_token' => Str::random(60),
                        'token_type' => 'Bearer',
                        'expires_in' => config('sanctum.expiration') * 60,
                    ]
                ]
            ]);
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
     * Handle a registration request to the API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class, 'alpha_dash'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'terms_accepted' => ['required', 'accepted'],
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member',
            ]);

            event(new Registered($user));

            // Create token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công.',
                'data' => [
                    'user' => $user,
                    'tokens' => [
                        'access_token' => $token,
                        'refresh_token' => Str::random(60),
                        'token_type' => 'Bearer',
                        'expires_in' => config('sanctum.expiration') * 60,
                    ]
                ]
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
     * Handle a logout request to the API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke all tokens
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đăng xuất.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            // Thêm thông tin bổ sung
            $user->unread_alerts_count = $user->unreadNotifications()->count();

            // Thêm thông tin tin nhắn chưa đọc (nếu có)
            // $user->unread_messages_count = $user->unreadMessages()->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user
                ],
                'message' => 'Lấy thông tin người dùng thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $request->validate([
                'refresh_token' => 'required|string',
            ]);

            // Trong thực tế, bạn cần lưu refresh token trong database và kiểm tra
            // Đây là một triển khai đơn giản, bạn cần mở rộng để xử lý refresh token đúng cách

            $user = $request->user();

            // Revoke all tokens
            $user->tokens()->delete();

            // Create new token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'tokens' => [
                        'access_token' => $token,
                        'refresh_token' => Str::random(60),
                        'token_type' => 'Bearer',
                        'expires_in' => config('sanctum.expiration') * 60,
                    ]
                ],
                'message' => 'Làm mới token thành công.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi làm mới token.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));

                    // Send password changed notification
                    \App\Services\NotificationService::sendPasswordChangedNotification($user, $request->ip());
                }
            );

            if ($status === Password::PASSWORD_RESET) {
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
                'message' => 'Không thể đặt lại mật khẩu.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đặt lại mật khẩu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            // Trong thực tế, bạn cần triển khai logic xác minh email
            // Đây là một triển khai đơn giản

            return response()->json([
                'success' => true,
                'message' => 'Xác minh email thành công.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token xác minh không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xác minh email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle social login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialLogin(Request $request, $provider)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'secret' => 'nullable|string', // Một số provider yêu cầu secret
            ]);

            // Kiểm tra provider hợp lệ
            if (!in_array($provider, ['google', 'facebook', 'github'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider không hợp lệ.'
                ], 400);
            }

            // Trong thực tế, bạn cần triển khai logic để xác thực token từ mạng xã hội
            // Đây là một triển khai đơn giản, giả lập thông tin người dùng

            // Giả lập thông tin người dùng từ mạng xã hội
            $socialUser = (object) [
                'id' => 'social_' . Str::random(10),
                'name' => 'Social User',
                'email' => 'social_user_' . Str::random(5) . '@example.com',
                'avatar' => avatar_placeholder('Social User'),
                'token' => $request->token,
                'refreshToken' => null,
            ];

            // Thêm các phương thức getter
            $socialUser->getId = function () use ($socialUser) {
                return $socialUser->id;
            };
            $socialUser->getName = function () use ($socialUser) {
                return $socialUser->name;
            };
            $socialUser->getEmail = function () use ($socialUser) {
                return $socialUser->email;
            };
            $socialUser->getAvatar = function () use ($socialUser) {
                return $socialUser->avatar;
            };

            // Kiểm tra xem email đã tồn tại chưa
            $user = User::where('email', $socialUser->getEmail())->first();

            // Nếu user đã tồn tại, cập nhật thông tin social account
            if ($user) {
                // Đảm bảo email_verified_at được cập nhật
                if ($user->email_verified_at === null) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            } else {
                // Tạo username từ email hoặc name
                $username = Str::slug(explode('@', $socialUser->getEmail())[0]);

                // Kiểm tra xem username đã tồn tại chưa
                $usernameExists = User::where('username', $username)->exists();
                if ($usernameExists) {
                    $username = $username . Str::random(5);
                }

                // Tạo mật khẩu ngẫu nhiên
                $plainPassword = Str::random(10);

                // Tạo user mới
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'username' => $username,
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make($plainPassword),
                    'role' => 'member',
                ]);

                // Gửi email thông báo (có thể thực hiện bất đồng bộ)
                // Mail::to($user->email)->send(new WelcomeSocialUser($user, $plainPassword, $provider));
            }

            // Kiểm tra xem social account đã tồn tại chưa
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$socialAccount) {
                // Tạo social account mới
                $user->socialAccounts()->create([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_avatar' => $socialUser->getAvatar(),
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken ?? null,
                ]);
            } else {
                // Cập nhật social account
                $socialAccount->update([
                    'provider_avatar' => $socialUser->getAvatar(),
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken ?? null,
                ]);
            }

            // Create token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
            $refreshToken = Str::random(60);

            // Lưu refresh token (trong thực tế, bạn nên lưu vào database)
            // $user->update(['refresh_token' => $refreshToken]);

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập bằng ' . $provider . ' thành công.',
                'data' => [
                    'user' => $user,
                    'tokens' => [
                        'access_token' => $token,
                        'refresh_token' => $refreshToken,
                        'token_type' => 'Bearer',
                        'expires_in' => config('sanctum.expiration') * 60,
                    ]
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đăng nhập bằng ' . $provider . '.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
