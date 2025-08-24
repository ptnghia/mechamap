<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use App\Services\NotificationPreferencesService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Settings Controller cho Dashboard
 *
 * Quản lý cài đặt tài khoản của user
 */
class SettingsController extends BaseController
{
    /**
     * Hiển thị trang settings
     */
    public function index(Request $request)
    {
        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Settings', 'route' => 'dashboard.settings']
        ]);

        // Get notification preferences data
        $notificationCategories = NotificationPreferencesService::getNotificationCategories();
        $deliveryMethods = NotificationPreferencesService::getDeliveryMethods();
        $userPreferences = NotificationPreferencesService::getUserPreferences($this->user);
        $frequencyOptions = NotificationPreferencesService::getFrequencyOptions();

        return $this->dashboardResponse('dashboard.common.settings.index', [
            'breadcrumb' => $breadcrumb,
            'notificationCategories' => $notificationCategories,
            'deliveryMethods' => $deliveryMethods,
            'userPreferences' => $userPreferences,
            'frequencyOptions' => $frequencyOptions
        ]);
    }

    /**
     * Cập nhật preferences
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => 'required|string|in:vi,en',
            'timezone' => 'required|string|max:50',
            'theme' => 'required|string|in:light,dark,auto',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'forum_notifications' => 'boolean',
            'marketplace_notifications' => 'boolean',
        ]);

        $preferences = $request->only([
            'locale', 'timezone', 'theme', 'email_notifications',
            'push_notifications', 'marketing_emails', 'forum_notifications',
            'marketplace_notifications'
        ]);

        // Update user preferences
        $this->user->update([
            'locale' => $preferences['locale'],
            'timezone' => $preferences['timezone'],
            'preferences' => array_merge($this->user->preferences ?? [], $preferences)
        ]);

        // Update session locale
        session(['locale' => $preferences['locale']]);

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'Preferences updated successfully.');
    }

    /**
     * Cập nhật notification settings
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        // Validate global settings
        $request->validate([
            'global.email_enabled' => 'boolean',
            'global.push_enabled' => 'boolean',
            'global.sms_enabled' => 'boolean',
            'global.in_app_enabled' => 'boolean',
        ]);

        // Build notification preferences from request
        $preferences = [
            'global' => [
                'email_enabled' => $request->boolean('global.email_enabled'),
                'push_enabled' => $request->boolean('global.push_enabled'),
                'sms_enabled' => $request->boolean('global.sms_enabled'),
                'in_app_enabled' => $request->boolean('global.in_app_enabled'),
            ],
            'categories' => [],
            'delivery_methods' => []
        ];

        // Process category preferences
        $categories = NotificationPreferencesService::getNotificationCategories();
        foreach ($categories as $categoryKey => $category) {
            $preferences['categories'][$categoryKey] = [
                'enabled' => $request->boolean("categories.{$categoryKey}.enabled"),
                'types' => []
            ];

            foreach ($category['types'] as $typeKey => $typeName) {
                $preferences['categories'][$categoryKey]['types'][$typeKey] = [
                    'email' => $request->boolean("categories.{$categoryKey}.types.{$typeKey}.email"),
                    'push' => $request->boolean("categories.{$categoryKey}.types.{$typeKey}.push"),
                    'sms' => $request->boolean("categories.{$categoryKey}.types.{$typeKey}.sms"),
                    'in_app' => $request->boolean("categories.{$categoryKey}.types.{$typeKey}.in_app"),
                ];
            }
        }

        // Process delivery method preferences
        $deliveryMethods = NotificationPreferencesService::getDeliveryMethods();
        foreach ($deliveryMethods as $methodKey => $method) {
            $preferences['delivery_methods'][$methodKey] = [
                'enabled' => $request->boolean("delivery_methods.{$methodKey}.enabled"),
                'frequency' => $request->input("delivery_methods.{$methodKey}.frequency", 'immediate'),
                'quiet_hours' => [
                    'enabled' => $request->boolean("delivery_methods.{$methodKey}.quiet_hours.enabled"),
                    'start' => $request->input("delivery_methods.{$methodKey}.quiet_hours.start", '22:00'),
                    'end' => $request->input("delivery_methods.{$methodKey}.quiet_hours.end", '08:00'),
                ]
            ];
        }

        // Update user preferences
        NotificationPreferencesService::updateUserPreferences($this->user, $preferences);

        return redirect()->route('dashboard.settings.index')
            ->with('success', __('settings.notifications.updated_successfully'));
    }

    /**
     * Cập nhật privacy settings
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_visibility' => 'required|string|in:public,private,friends',
            'show_online_status' => 'boolean',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'allow_messages' => 'required|string|in:everyone,friends,none',
            'allow_friend_requests' => 'boolean',
            'show_activity' => 'boolean',
            'indexable_profile' => 'boolean',
        ]);

        $privacySettings = $request->only([
            'profile_visibility', 'show_online_status', 'show_email', 'show_phone',
            'allow_messages', 'allow_friend_requests', 'show_activity', 'indexable_profile'
        ]);

        // Update privacy preferences
        $preferences = $this->user->preferences ?? [];
        $preferences['privacy'] = $privacySettings;

        $this->user->update(['preferences' => $preferences]);

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'Privacy settings updated successfully.');
    }

    /**
     * Cập nhật security settings
     */
    public function updateSecurity(Request $request): RedirectResponse
    {
        $request->validate([
            'two_factor_enabled' => 'boolean',
            'login_notifications' => 'boolean',
            'session_timeout' => 'required|integer|min:15|max:1440', // 15 minutes to 24 hours
            'require_password_change' => 'boolean',
        ]);

        $securitySettings = $request->only([
            'two_factor_enabled', 'login_notifications', 'session_timeout',
            'require_password_change'
        ]);

        // Update security preferences
        $preferences = $this->user->preferences ?? [];
        $preferences['security'] = $securitySettings;

        $this->user->update(['preferences' => $preferences]);

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'Security settings updated successfully.');
    }

    /**
     * Export user data
     */
    public function exportData(Request $request)
    {
        $data = [
            'user' => $this->user->toArray(),
            'threads' => $this->user->threads()->with(['comments', 'category', 'forum'])->get()->toArray(),
            'comments' => $this->user->comments()->with(['thread'])->get()->toArray(),
            'bookmarks' => $this->user->bookmarks()->with(['thread'])->get()->toArray(),
            'notifications' => $this->user->userNotifications()->get()->toArray(),
        ];

        // Add marketplace data if user has permissions
        if ($this->user->hasAnyMarketplacePermission()) {
            $data['marketplace_orders'] = $this->user->marketplaceOrders()->with(['items'])->get()->toArray();
            $data['marketplace_products'] = $this->user->marketplaceProducts()->get()->toArray();
        }

        $filename = 'user_data_' . $this->user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Deactivate account
     */
    public function deactivateAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
            'reason' => 'required|string|max:500',
        ]);

        // Mark account as deactivated
        $this->user->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        // Logout user
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Your account has been deactivated successfully.');
    }

    /**
     * Reset all settings to default
     */
    public function resetToDefaults(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        // Reset preferences to default
        $this->user->update([
            'preferences' => null,
            'locale' => config('app.locale'),
            'timezone' => config('app.timezone'),
        ]);

        return redirect()->route('dashboard.settings.index')
            ->with('success', 'All settings have been reset to defaults.');
    }

    /**
     * Download account data
     */
    public function downloadData(Request $request)
    {
        // Generate comprehensive user data export
        $userData = [
            'account_info' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'username' => $this->user->username,
                'role' => $this->user->role,
                'created_at' => $this->user->created_at,
                'last_login_at' => $this->user->last_login_at,
            ],
            'activity_summary' => $this->getDashboardStats(),
            'export_date' => now()->toISOString(),
        ];

        $filename = 'mechamap_account_data_' . $this->user->username . '_' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($userData) {
            echo json_encode($userData, JSON_PRETTY_PRINT);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}
