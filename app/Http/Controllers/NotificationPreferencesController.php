<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationPreferencesController extends Controller
{
    /**
     * Display notification preferences page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get current preferences or set defaults
        $preferences = $user->notification_preferences ?? $this->getDefaultPreferences();

        // Available notification types
        $notificationTypes = $this->getNotificationTypes();

        return view('notifications.preferences', compact('preferences', 'notificationTypes'));
    }

    /**
     * Get current user's notification preferences (API)
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = auth()->user();
        $preferences = $user->notification_preferences ?? $this->getDefaultPreferences();

        return response()->json([
            'success' => true,
            'data' => [
                'preferences' => $preferences,
                'available_types' => $this->getAvailableNotificationTypes(),
                'available_frequencies' => $this->getAvailableFrequencies(),
                'available_digest_days' => $this->getAvailableDigestDays(),
            ],
        ]);
    }

    /**
     * Update notification preferences (AJAX)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'email_notifications_enabled' => 'boolean',
                'notification_types' => 'array',
                'notification_types.*' => 'string|in:' . implode(',', array_keys($this->getAvailableNotificationTypes())),
                'frequency_limits' => 'array',
                'frequency_limits.*.type' => 'string',
                'frequency_limits.*.max_per_day' => 'integer|min:0|max:100',
                'quiet_hours' => 'array',
                'quiet_hours.enabled' => 'boolean',
                'quiet_hours.start' => 'integer|min:0|max:23',
                'quiet_hours.end' => 'integer|min:0|max:23',
                'weekly_digest' => 'boolean',
                'digest_day' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'digest_time' => 'string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',
                'batch_notifications' => 'boolean',
                'real_time_notifications' => 'boolean',
                'preferences' => 'array',
                'preferences.*.enabled' => 'boolean',
                'preferences.*.email' => 'boolean',
                'preferences.*.push' => 'boolean',
            ]);

            // Update email notifications global setting
            if (isset($validated['email_notifications_enabled'])) {
                $user->email_notifications_enabled = $validated['email_notifications_enabled'];
            }

            // Update new-style preferences
            $currentPreferences = $user->notification_preferences ?? $this->getDefaultPreferences();

            $newPreferences = array_merge($currentPreferences, array_filter([
                'email_notifications_enabled' => $validated['email_notifications_enabled'] ?? null,
                'notification_types' => $validated['notification_types'] ?? null,
                'frequency_limits' => $this->processFrequencyLimits($validated['frequency_limits'] ?? []),
                'quiet_hours' => $validated['quiet_hours'] ?? null,
                'weekly_digest' => $validated['weekly_digest'] ?? null,
                'digest_day' => $validated['digest_day'] ?? null,
                'digest_time' => $validated['digest_time'] ?? null,
                'batch_notifications' => $validated['batch_notifications'] ?? null,
                'real_time_notifications' => $validated['real_time_notifications'] ?? null,
            ], function ($value) {
                return $value !== null;
            }));

            // Update individual preferences (legacy support)
            if (isset($validated['preferences'])) {
                $currentPreferences = $user->notification_preferences ?? $this->getDefaultPreferences();

                foreach ($validated['preferences'] as $type => $settings) {
                    if (isset($currentPreferences[$type])) {
                        $currentPreferences[$type] = array_merge($currentPreferences[$type], $settings);
                    }
                }

                $user->notification_preferences = $currentPreferences;
            }

            $user->save();

            Log::info('Notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => $user->notification_preferences,
                'email_enabled' => $user->email_notifications_enabled
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cài đặt thông báo đã được cập nhật thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Update notification preferences failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật cài đặt'
            ], 500);
        }
    }

    /**
     * Reset preferences to default (AJAX)
     */
    public function reset(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $user->notification_preferences = $this->getDefaultPreferences();
            $user->email_notifications_enabled = true;
            $user->save();

            Log::info('Notification preferences reset to default', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cài đặt thông báo đã được khôi phục về mặc định',
                'preferences' => $user->notification_preferences
            ]);

        } catch (\Exception $e) {
            Log::error('Reset notification preferences failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khôi phục cài đặt'
            ], 500);
        }
    }

    /**
     * Get default notification preferences
     */
    private function getDefaultPreferences(): array
    {
        return [
            // Forum notifications
            'thread_created' => [
                'enabled' => true,
                'email' => false,
                'push' => true,
                'label' => 'Thread mới',
                'description' => 'Khi có thread mới trong forum bạn quan tâm'
            ],
            'thread_replied' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Reply thread',
                'description' => 'Khi có reply mới trong thread bạn theo dõi'
            ],
            'comment_mention' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Được nhắc đến',
                'description' => 'Khi bạn được @mention trong bình luận'
            ],

            // Security notifications
            'login_from_new_device' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Đăng nhập thiết bị mới',
                'description' => 'Cảnh báo bảo mật khi đăng nhập từ thiết bị mới'
            ],
            'password_changed' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Đổi mật khẩu',
                'description' => 'Xác nhận khi mật khẩu được thay đổi'
            ],

            // Marketplace notifications
            'product_out_of_stock' => [
                'enabled' => true,
                'email' => false,
                'push' => true,
                'label' => 'Hết hàng',
                'description' => 'Khi sản phẩm bạn quan tâm hết hàng'
            ],
            'price_drop_alert' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Giảm giá',
                'description' => 'Khi sản phẩm bạn quan tâm giảm giá'
            ],
            'wishlist_available' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Wishlist có hàng',
                'description' => 'Khi sản phẩm trong wishlist có sẵn'
            ],
            'review_received' => [
                'enabled' => true,
                'email' => false,
                'push' => true,
                'label' => 'Nhận đánh giá',
                'description' => 'Khi nhận được đánh giá cho sản phẩm'
            ],
            'seller_message' => [
                'enabled' => true,
                'email' => true,
                'push' => true,
                'label' => 'Tin nhắn seller',
                'description' => 'Tin nhắn từ người bán'
            ],

            // Social notifications
            'user_followed' => [
                'enabled' => true,
                'email' => false,
                'push' => true,
                'label' => 'Được theo dõi',
                'description' => 'Khi có người theo dõi bạn'
            ],
            'achievement_unlocked' => [
                'enabled' => true,
                'email' => false,
                'push' => true,
                'label' => 'Thành tựu mới',
                'description' => 'Khi mở khóa thành tựu mới'
            ],

            // System notifications
            'weekly_digest' => [
                'enabled' => true,
                'email' => true,
                'push' => false,
                'label' => 'Tổng hợp tuần',
                'description' => 'Email tổng hợp hoạt động hàng tuần'
            ],
        ];
    }

    /**
     * Get notification types grouped by category
     */
    private function getNotificationTypes(): array
    {
        return [
            'forum' => [
                'label' => 'Thông báo Forum',
                'icon' => 'fas fa-comments',
                'types' => ['thread_created', 'thread_replied', 'comment_mention']
            ],
            'security' => [
                'label' => 'Thông báo Bảo mật',
                'icon' => 'fas fa-shield-alt',
                'types' => ['login_from_new_device', 'password_changed']
            ],
            'marketplace' => [
                'label' => 'Thông báo Marketplace',
                'icon' => 'fas fa-shopping-cart',
                'types' => ['product_out_of_stock', 'price_drop_alert', 'wishlist_available', 'review_received', 'seller_message']
            ],
            'social' => [
                'label' => 'Thông báo Xã hội',
                'icon' => 'fas fa-users',
                'types' => ['user_followed', 'achievement_unlocked']
            ],
            'system' => [
                'label' => 'Thông báo Hệ thống',
                'icon' => 'fas fa-cog',
                'types' => ['weekly_digest']
            ]
        ];
    }

    /**
     * Get available notification types (enhanced)
     */
    private function getAvailableNotificationTypes(): array
    {
        return [
            'thread_created' => [
                'name' => 'Chủ đề mới',
                'description' => 'Thông báo khi có chủ đề mới trong diễn đàn bạn theo dõi',
                'category' => 'forum',
            ],
            'thread_replied' => [
                'name' => 'Phản hồi chủ đề',
                'description' => 'Thông báo khi có phản hồi mới trong chủ đề bạn quan tâm',
                'category' => 'forum',
            ],
            'comment_mention' => [
                'name' => 'Nhắc đến',
                'description' => 'Thông báo khi bạn được nhắc đến trong bình luận',
                'category' => 'social',
            ],
            'user_followed' => [
                'name' => 'Người theo dõi mới',
                'description' => 'Thông báo khi có người theo dõi bạn',
                'category' => 'social',
            ],
            'achievement_unlocked' => [
                'name' => 'Thành tựu mới',
                'description' => 'Thông báo khi bạn mở khóa thành tựu mới',
                'category' => 'achievement',
            ],
            'product_out_of_stock' => [
                'name' => 'Hết hàng',
                'description' => 'Thông báo khi sản phẩm bạn quan tâm hết hàng',
                'category' => 'marketplace',
            ],
            'order_status_changed' => [
                'name' => 'Trạng thái đơn hàng',
                'description' => 'Thông báo khi trạng thái đơn hàng thay đổi',
                'category' => 'marketplace',
            ],
            'review_received' => [
                'name' => 'Đánh giá mới',
                'description' => 'Thông báo khi nhận được đánh giá',
                'category' => 'marketplace',
            ],
            'wishlist_available' => [
                'name' => 'Sản phẩm yêu thích có sẵn',
                'description' => 'Thông báo khi sản phẩm trong danh sách yêu thích có sẵn',
                'category' => 'marketplace',
            ],
            'seller_message' => [
                'name' => 'Tin nhắn từ người bán',
                'description' => 'Thông báo tin nhắn từ người bán',
                'category' => 'marketplace',
            ],
            'login_from_new_device' => [
                'name' => 'Đăng nhập từ thiết bị mới',
                'description' => 'Thông báo bảo mật khi đăng nhập từ thiết bị mới',
                'category' => 'security',
            ],
            'password_changed' => [
                'name' => 'Thay đổi mật khẩu',
                'description' => 'Thông báo bảo mật khi mật khẩu được thay đổi',
                'category' => 'security',
            ],
            'weekly_digest' => [
                'name' => 'Tóm tắt tuần',
                'description' => 'Tóm tắt hoạt động hàng tuần',
                'category' => 'digest',
            ],
        ];
    }

    /**
     * Get available frequencies
     */
    private function getAvailableFrequencies(): array
    {
        return [
            'immediate' => 'Ngay lập tức',
            'hourly' => 'Mỗi giờ',
            'daily' => 'Hàng ngày',
            'weekly' => 'Hàng tuần',
            'never' => 'Không bao giờ',
        ];
    }

    /**
     * Get available digest days
     */
    private function getAvailableDigestDays(): array
    {
        return [
            'monday' => 'Thứ Hai',
            'tuesday' => 'Thứ Ba',
            'wednesday' => 'Thứ Tư',
            'thursday' => 'Thứ Năm',
            'friday' => 'Thứ Sáu',
            'saturday' => 'Thứ Bảy',
            'sunday' => 'Chủ Nhật',
        ];
    }

    /**
     * Process frequency limits
     */
    private function processFrequencyLimits(array $limits): array
    {
        $processed = [];
        foreach ($limits as $limit) {
            if (isset($limit['type']) && isset($limit['max_per_day'])) {
                $processed[$limit['type']] = [
                    'max_per_day' => (int) $limit['max_per_day'],
                ];
            }
        }
        return $processed;
    }

    /**
     * Reset preferences to default (API)
     */
    public function resetPreferences(Request $request): JsonResponse
    {
        $user = auth()->user();
        $defaultPreferences = $this->getDefaultPreferences();

        $user->update([
            'notification_preferences' => $defaultPreferences,
            'email_notifications_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt thông báo đã được đặt lại về mặc định',
            'data' => [
                'preferences' => $defaultPreferences,
            ],
        ]);
    }

    /**
     * Update specific notification type (API)
     */
    public function updateNotificationType(Request $request, string $type): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'max_per_day' => 'integer|min:0|max:100',
        ]);

        $user = auth()->user();
        $preferences = $user->notification_preferences ?? $this->getDefaultPreferences();
        $enabledTypes = $preferences['notification_types'] ?? array_keys($this->getAvailableNotificationTypes());
        $frequencyLimits = $preferences['frequency_limits'] ?? [];

        // Update enabled types
        if ($validated['enabled']) {
            if (!in_array($type, $enabledTypes)) {
                $enabledTypes[] = $type;
            }
        } else {
            $enabledTypes = array_filter($enabledTypes, function ($t) use ($type) {
                return $t !== $type;
            });
        }

        // Update frequency limit if provided
        if (isset($validated['max_per_day'])) {
            $frequencyLimits[$type] = [
                'max_per_day' => $validated['max_per_day'],
            ];
        }

        $preferences['notification_types'] = array_values($enabledTypes);
        $preferences['frequency_limits'] = $frequencyLimits;

        $user->update(['notification_preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt loại thông báo đã được cập nhật',
            'data' => [
                'type' => $type,
                'enabled' => $validated['enabled'],
                'max_per_day' => $validated['max_per_day'] ?? null,
            ],
        ]);
    }
}
