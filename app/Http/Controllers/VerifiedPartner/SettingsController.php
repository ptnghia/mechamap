<?php

namespace App\Http\Controllers\VerifiedPartner;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSeller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

/**
 * 🎯 MechaMap Verified Partner Settings Controller
 * 
 * Controller cho verified_partner role (L11) - Cài đặt tài khoản
 * Quản lý thông tin cá nhân và cài đặt kinh doanh
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:verified_partner']);
    }

    /**
     * Display settings page for verified partner
     */
    public function index(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        return view('verified-partner.settings.index', compact('user', 'seller'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:500',
        ]);

        $updateData = $request->only(['name', 'email', 'phone', 'bio']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && Storage::exists('public/avatars/' . basename($user->avatar))) {
                Storage::delete('public/avatars/' . basename($user->avatar));
            }

            $avatarPath = $request->file('avatar')->store('public/avatars');
            $updateData['avatar'] = Storage::url($avatarPath);
        }

        $user->update($updateData);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }

    /**
     * Update business information
     */
    public function updateBusiness(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Không tìm thấy thông tin đối tác.');
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_description' => 'nullable|string|max:1000',
            'business_address' => 'nullable|string|max:500',
            'business_phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = $request->only([
            'business_name',
            'business_description', 
            'business_address',
            'business_phone',
            'business_email',
            'business_website',
            'tax_number',
            'business_license'
        ]);

        // Handle business logo upload
        if ($request->hasFile('business_logo')) {
            // Delete old logo
            if ($seller->business_logo && Storage::exists('public/business-logos/' . basename($seller->business_logo))) {
                Storage::delete('public/business-logos/' . basename($seller->business_logo));
            }

            $logoPath = $request->file('business_logo')->store('public/business-logos');
            $updateData['business_logo'] = Storage::url($logoPath);
        }

        $seller->update($updateData);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Thông tin doanh nghiệp đã được cập nhật thành công.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Mật khẩu đã được cập nhật thành công.');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'order_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'security_notifications' => 'boolean',
        ]);

        // Update user notification preferences
        $preferences = [
            'email_notifications' => $request->boolean('email_notifications'),
            'order_notifications' => $request->boolean('order_notifications'),
            'marketing_notifications' => $request->boolean('marketing_notifications'),
            'security_notifications' => $request->boolean('security_notifications'),
        ];

        $user->update([
            'notification_preferences' => $preferences
        ]);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Cài đặt thông báo đã được cập nhật thành công.');
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Không tìm thấy thông tin đối tác.');
        }

        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'paypal_email' => 'nullable|email|max:255',
            'preferred_payment_method' => 'required|in:bank_transfer,paypal,stripe',
        ]);

        $paymentSettings = [
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'bank_branch' => $request->bank_branch,
            'paypal_email' => $request->paypal_email,
            'preferred_payment_method' => $request->preferred_payment_method,
        ];

        $seller->update([
            'payment_settings' => $paymentSettings
        ]);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Cài đặt thanh toán đã được cập nhật thành công.');
    }

    /**
     * Update shipping settings
     */
    public function updateShipping(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Không tìm thấy thông tin đối tác.');
        }

        $request->validate([
            'shipping_policy' => 'nullable|string|max:1000',
            'return_policy' => 'nullable|string|max:1000',
            'processing_time' => 'nullable|integer|min:1|max:30',
            'domestic_shipping_fee' => 'nullable|numeric|min:0',
            'international_shipping_fee' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        $shippingSettings = [
            'shipping_policy' => $request->shipping_policy,
            'return_policy' => $request->return_policy,
            'processing_time' => $request->processing_time,
            'domestic_shipping_fee' => $request->domestic_shipping_fee,
            'international_shipping_fee' => $request->international_shipping_fee,
            'free_shipping_threshold' => $request->free_shipping_threshold,
        ];

        $seller->update([
            'shipping_settings' => $shippingSettings
        ]);

        return redirect()->route('partner.settings.index')
            ->with('success', 'Cài đặt vận chuyển đã được cập nhật thành công.');
    }

    /**
     * Deactivate account
     */
    public function deactivateAccount(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|current_password',
            'reason' => 'nullable|string|max:500',
        ]);

        // Mark user as inactive
        $user->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        // Mark seller as inactive
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();
        if ($seller) {
            $seller->update(['status' => 'inactive']);
        }

        // Logout user
        auth()->logout();

        return redirect()->route('home')
            ->with('success', 'Tài khoản đã được vô hiệu hóa thành công.');
    }
}
