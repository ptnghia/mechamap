<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSeller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    /**
     * Display supplier settings
     */
    public function index(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        return view('supplier.settings.index', compact('seller'));
    }

    /**
     * Update business information
     */
    public function updateBusiness(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:individual,company,partnership',
            'business_registration_number' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'business_description' => 'nullable|string',
            'contact_person_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'website_url' => 'nullable|url|max:255',
            'business_address' => 'required|array',
            'business_address.street' => 'required|string|max:255',
            'business_address.city' => 'required|string|max:255',
            'business_address.state' => 'required|string|max:255',
            'business_address.postal_code' => 'required|string|max:20',
            'business_address.country' => 'required|string|max:255',
        ]);

        $seller->update([
            'business_name' => $validated['business_name'],
            'business_type' => $validated['business_type'],
            'business_registration_number' => $validated['business_registration_number'],
            'tax_identification_number' => $validated['tax_identification_number'],
            'business_description' => $validated['business_description'],
            'contact_person_name' => $validated['contact_person_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'website_url' => $validated['website_url'],
            'business_address' => $validated['business_address'],
        ]);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Thông tin doanh nghiệp đã được cập nhật.');
    }

    /**
     * Update store information
     */
    public function updateStore(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_slug' => 'required|string|max:255|unique:marketplace_sellers,store_slug,' . $seller->id,
            'store_description' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'store_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'industry_categories' => 'nullable|array',
            'specializations' => 'nullable|array',
        ]);

        $updateData = [
            'store_name' => $validated['store_name'],
            'store_slug' => $validated['store_slug'],
            'store_description' => $validated['store_description'],
            'industry_categories' => $validated['industry_categories'] ?? [],
            'specializations' => $validated['specializations'] ?? [],
        ];

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo
            if ($seller->store_logo) {
                Storage::disk('public')->delete($seller->store_logo);
            }

            $logoPath = $request->file('store_logo')->store('sellers/logos', 'public');
            $updateData['store_logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('store_banner')) {
            // Delete old banner
            if ($seller->store_banner) {
                Storage::disk('public')->delete($seller->store_banner);
            }

            $bannerPath = $request->file('store_banner')->store('sellers/banners', 'public');
            $updateData['store_banner'] = $bannerPath;
        }

        $seller->update($updateData);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Thông tin cửa hàng đã được cập nhật.');
    }

    /**
     * Update shipping settings
     */
    public function updateShipping(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'processing_time_days' => 'required|integer|min:1|max:30',
            'shipping_methods' => 'required|array',
            'shipping_methods.*.name' => 'required|string|max:255',
            'shipping_methods.*.cost' => 'required|numeric|min:0',
            'shipping_methods.*.estimated_days' => 'required|integer|min:1|max:30',
            'return_policy' => 'nullable|array',
            'return_policy.accepts_returns' => 'boolean',
            'return_policy.return_period_days' => 'nullable|integer|min:1|max:365',
            'return_policy.return_conditions' => 'nullable|string',
        ]);

        $seller->update([
            'processing_time_days' => $validated['processing_time_days'],
            'shipping_methods' => $validated['shipping_methods'],
            'return_policy' => $validated['return_policy'] ?? [],
        ]);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Cài đặt vận chuyển đã được cập nhật.');
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'payment_methods' => 'required|array',
            'payment_methods.bank_transfer' => 'boolean',
            'payment_methods.paypal' => 'boolean',
            'payment_methods.stripe' => 'boolean',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'paypal_email' => 'nullable|email|max:255',
            'auto_approve_orders' => 'boolean',
        ]);

        $seller->update([
            'payment_methods' => $validated['payment_methods'],
            'bank_account_name' => $validated['bank_account_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_name' => $validated['bank_name'],
            'bank_branch' => $validated['bank_branch'],
            'paypal_email' => $validated['paypal_email'],
            'auto_approve_orders' => $validated['auto_approve_orders'] ?? false,
        ]);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Cài đặt thanh toán đã được cập nhật.');
    }

    /**
     * Update terms and conditions
     */
    public function updateTerms(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'terms_conditions' => 'required|array',
            'terms_conditions.terms_of_service' => 'required|string',
            'terms_conditions.privacy_policy' => 'nullable|string',
            'terms_conditions.warranty_policy' => 'nullable|string',
            'terms_conditions.refund_policy' => 'nullable|string',
        ]);

        $seller->update([
            'terms_conditions' => $validated['terms_conditions'],
        ]);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Điều khoản và điều kiện đã được cập nhật.');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'notification_settings' => 'required|array',
            'notification_settings.email_new_orders' => 'boolean',
            'notification_settings.email_order_updates' => 'boolean',
            'notification_settings.email_low_stock' => 'boolean',
            'notification_settings.email_reviews' => 'boolean',
            'notification_settings.sms_new_orders' => 'boolean',
            'notification_settings.sms_urgent_updates' => 'boolean',
        ]);

        $storeSettings = $seller->store_settings ?? [];
        $storeSettings['notifications'] = $validated['notification_settings'];

        $seller->update([
            'store_settings' => $storeSettings,
        ]);

        return redirect()->route('supplier.settings.index')
            ->with('success', 'Cài đặt thông báo đã được cập nhật.');
    }
}
