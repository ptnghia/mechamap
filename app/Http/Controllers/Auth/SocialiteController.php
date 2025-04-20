<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeSocialUser;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect to provider for authentication
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle provider callback
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập bằng ' . $provider . ' không thành công. Vui lòng thử lại.');
        }

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

            // Gửi email thông báo
            Mail::to($user->email)->send(new WelcomeSocialUser($user, $plainPassword, $provider));
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
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);
        } else {
            // Cập nhật social account
            $socialAccount->update([
                'provider_avatar' => $socialUser->getAvatar(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);
        }

        // Đăng nhập
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
