<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialAccountTypeRequest;
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

        // Nếu user đã tồn tại, đăng nhập trực tiếp
        if ($user) {
            // Đảm bảo email_verified_at được cập nhật
            if ($user->email_verified_at === null) {
                $user->email_verified_at = now();
                $user->save();
            }

            // Cập nhật hoặc tạo social account
            $this->updateOrCreateSocialAccount($user, $provider, $socialUser);

            // Đăng nhập
            Auth::login($user);

            return redirect()->route('dashboard');
        }

        // User chưa tồn tại - lưu thông tin social vào session và chuyển đến trang chọn account type
        return $this->handleNewSocialUser($provider, $socialUser);
    }

    /**
     * Handle new social user - redirect to account type selection
     *
     * @param string $provider
     * @param mixed $socialUser
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleNewSocialUser($provider, $socialUser)
    {
        // Lưu thông tin social user vào session
        session([
            'social_registration' => [
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'created_at' => now()->toISOString()
            ]
        ]);

        // Chuyển hướng đến trang chọn account type
        return redirect()->route('auth.social.account-type')
            ->with('success', 'Thông tin từ ' . ucfirst($provider) . ' đã được lấy thành công. Vui lòng chọn loại tài khoản và hoàn thiện thông tin.');
    }

    /**
     * Update or create social account for existing user
     *
     * @param User $user
     * @param string $provider
     * @param mixed $socialUser
     * @return void
     */
    protected function updateOrCreateSocialAccount(User $user, string $provider, $socialUser): void
    {
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
    }

    /**
     * Show account type selection page for social registration
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showAccountTypeSelection()
    {
        // Kiểm tra xem có thông tin social registration trong session không
        $socialData = session('social_registration');

        if (!$socialData) {
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập social đã hết hạn. Vui lòng thử lại.');
        }

        // Kiểm tra thời gian hết hạn (30 phút)
        $createdAt = \Carbon\Carbon::parse($socialData['created_at']);
        if ($createdAt->diffInMinutes(now()) > 30) {
            session()->forget('social_registration');
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập social đã hết hạn. Vui lòng thử lại.');
        }

        return view('auth.social.account-type', compact('socialData'));
    }

    /**
     * Process account type selection and complete registration
     *
     * @param SocialAccountTypeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processAccountTypeSelection(SocialAccountTypeRequest $request)
    {

        // Kiểm tra social data trong session
        $socialData = session('social_registration');

        if (!$socialData) {
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập social đã hết hạn. Vui lòng thử lại.');
        }

        // Tạo mật khẩu ngẫu nhiên
        $plainPassword = Str::random(12);

        try {
            // Tạo user mới
            $user = User::create([
                'name' => $socialData['name'],
                'username' => $request->username,
                'email' => $socialData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($plainPassword),
                'role' => $request->account_type,
            ]);

            // Tạo social account
            $user->socialAccounts()->create([
                'provider' => $socialData['provider'],
                'provider_id' => $socialData['provider_id'],
                'provider_avatar' => $socialData['avatar'],
                'provider_token' => $socialData['token'],
                'provider_refresh_token' => $socialData['refresh_token'],
            ]);

            // Gửi email thông báo
            Mail::to($user->email)->send(new WelcomeSocialUser($user, $plainPassword, $socialData['provider']));

            // Xóa social data khỏi session
            session()->forget('social_registration');

            // Đăng nhập user
            Auth::login($user);

            // Chuyển hướng dựa trên loại tài khoản
            $redirectUrl = $this->getRedirectUrlByRole($user->role);

            return redirect($redirectUrl)
                ->with('success', 'Tài khoản đã được tạo thành công! Chào mừng bạn đến với MechaMap.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo tài khoản. Vui lòng thử lại.');
        }
    }

    /**
     * Get redirect URL based on user role
     *
     * @param string $role
     * @return string
     */
    protected function getRedirectUrlByRole(string $role): string
    {
        return match($role) {
            'manufacturer', 'supplier', 'brand' => route('business.dashboard'),
            'verified_partner' => route('partner.dashboard'),
            default => route('dashboard')
        };
    }
}
