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
     * Update notification preferences (AJAX)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'email_notifications_enabled' => 'boolean',
                'preferences' => 'array',
                'preferences.*.enabled' => 'boolean',
                'preferences.*.email' => 'boolean',
                'preferences.*.push' => 'boolean',
            ]);

            // Update email notifications global setting
            if (isset($validated['email_notifications_enabled'])) {
                $user->email_notifications_enabled = $validated['email_notifications_enabled'];
            }

            // Update individual preferences
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
}
