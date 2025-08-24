<?php

namespace App\Services;

use App\Models\User;

class NotificationPreferencesService
{
    /**
     * Get all notification categories with their types
     *
     * @return array
     */
    public static function getNotificationCategories(): array
    {
        return [
            'messages' => [
                'name' => 'Tin nhắn',
                'icon' => 'fas fa-envelope',
                'color' => 'primary',
                'types' => [
                    'new_message' => 'Tin nhắn mới',
                    'seller_message' => 'Tin nhắn từ người bán',
                    'group_message' => 'Tin nhắn nhóm',
                    'message_reply' => 'Phản hồi tin nhắn',
                ]
            ],
            'forum' => [
                'name' => 'Diễn đàn',
                'icon' => 'fas fa-comments',
                'color' => 'success',
                'types' => [
                    'new_thread' => 'Thread mới',
                    'thread_reply' => 'Phản hồi thread',
                    'thread_mention' => 'Được nhắc đến',
                    'thread_like' => 'Lượt thích thread',
                    'forum_activity' => 'Hoạt động diễn đàn',
                    'thread_approved' => 'Thread được duyệt',
                    'thread_featured' => 'Thread được đề xuất',
                ]
            ],
            'marketplace' => [
                'name' => 'Thương mại',
                'icon' => 'fas fa-shopping-cart',
                'color' => 'warning',
                'types' => [
                    'product_approved' => 'Sản phẩm được duyệt',
                    'order_update' => 'Cập nhật đơn hàng',
                    'order_status' => 'Trạng thái đơn hàng',
                    'product_discount' => 'Giảm giá sản phẩm',
                    'product_out_of_stock' => 'Hết hàng',
                    'product_review' => 'Nhận đánh giá',
                    'wishlist_available' => 'Wishlist có hàng',
                    'marketplace_activity' => 'Hoạt động marketplace',
                    'commission_paid' => 'Hoa hồng đã trả',
                    'seller_promotion' => 'Khuyến mãi người bán',
                ]
            ],
            'security' => [
                'name' => 'Bảo mật',
                'icon' => 'fas fa-shield-alt',
                'color' => 'danger',
                'types' => [
                    'new_device_login' => 'Đăng nhập từ thiết bị mới',
                    'password_changed' => 'Mật khẩu đã thay đổi',
                    'security_alert' => 'Cảnh báo bảo mật',
                    'account_locked' => 'Tài khoản bị khóa',
                    'suspicious_activity' => 'Hoạt động đáng ngờ',
                ]
            ],
            'social' => [
                'name' => 'Xã hội',
                'icon' => 'fas fa-users',
                'color' => 'info',
                'types' => [
                    'new_follower' => 'Được theo dõi',
                    'new_user' => 'Người dùng mới',
                    'friend_request' => 'Lời mời kết bạn',
                    'friend_accepted' => 'Chấp nhận kết bạn',
                    'profile_view' => 'Xem hồ sơ',
                ]
            ],
            'system' => [
                'name' => 'Hệ thống',
                'icon' => 'fas fa-cogs',
                'color' => 'secondary',
                'types' => [
                    'system_maintenance' => 'Bảo trì hệ thống',
                    'feature_update' => 'Cập nhật tính năng',
                    'policy_update' => 'Cập nhật chính sách',
                    'newsletter' => 'Bản tin',
                    'marketing_email' => 'Email quảng cáo',
                ]
            ]
        ];
    }

    /**
     * Get notification delivery methods
     *
     * @return array
     */
    public static function getDeliveryMethods(): array
    {
        return [
            'email' => [
                'name' => 'Email',
                'icon' => 'fas fa-envelope',
                'description' => 'Nhận thông báo qua email'
            ],
            'push' => [
                'name' => 'Push Notification',
                'icon' => 'fas fa-bell',
                'description' => 'Thông báo đẩy trên trình duyệt'
            ],
            'sms' => [
                'name' => 'SMS',
                'icon' => 'fas fa-sms',
                'description' => 'Tin nhắn SMS (chỉ thông báo quan trọng)'
            ],
            'in_app' => [
                'name' => 'Trong ứng dụng',
                'icon' => 'fas fa-desktop',
                'description' => 'Hiển thị trong dashboard'
            ]
        ];
    }

    /**
     * Get user's notification preferences
     *
     * @param User $user
     * @return array
     */
    public static function getUserPreferences(User $user): array
    {
        $preferences = $user->preferences['notifications'] ?? [];
        $categories = self::getNotificationCategories();
        $deliveryMethods = self::getDeliveryMethods();

        $result = [
            'global' => [
                'email_enabled' => $preferences['email_enabled'] ?? true,
                'push_enabled' => $preferences['push_enabled'] ?? true,
                'sms_enabled' => $preferences['sms_enabled'] ?? false,
                'in_app_enabled' => $preferences['in_app_enabled'] ?? true,
            ],
            'categories' => [],
            'delivery_methods' => []
        ];

        // Initialize category preferences
        foreach ($categories as $categoryKey => $category) {
            $result['categories'][$categoryKey] = [
                'enabled' => $preferences['categories'][$categoryKey]['enabled'] ?? true,
                'types' => []
            ];

            foreach ($category['types'] as $typeKey => $typeName) {
                $result['categories'][$categoryKey]['types'][$typeKey] = [
                    'email' => $preferences['categories'][$categoryKey]['types'][$typeKey]['email'] ?? true,
                    'push' => $preferences['categories'][$categoryKey]['types'][$typeKey]['push'] ?? true,
                    'sms' => $preferences['categories'][$categoryKey]['types'][$typeKey]['sms'] ?? false,
                    'in_app' => $preferences['categories'][$categoryKey]['types'][$typeKey]['in_app'] ?? true,
                ];
            }
        }

        // Initialize delivery method preferences
        foreach ($deliveryMethods as $methodKey => $method) {
            $result['delivery_methods'][$methodKey] = [
                'enabled' => $preferences['delivery_methods'][$methodKey]['enabled'] ?? ($methodKey !== 'sms'),
                'frequency' => $preferences['delivery_methods'][$methodKey]['frequency'] ?? 'immediate',
                'quiet_hours' => $preferences['delivery_methods'][$methodKey]['quiet_hours'] ?? [
                    'enabled' => false,
                    'start' => '22:00',
                    'end' => '08:00'
                ]
            ];
        }

        return $result;
    }

    /**
     * Update user's notification preferences
     *
     * @param User $user
     * @param array $preferences
     * @return bool
     */
    public static function updateUserPreferences(User $user, array $preferences): bool
    {
        $currentPreferences = $user->preferences ?? [];
        $currentPreferences['notifications'] = $preferences;

        return $user->update(['preferences' => $currentPreferences]);
    }

    /**
     * Get notification frequency options
     *
     * @return array
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'immediate' => 'Ngay lập tức',
            'hourly' => 'Mỗi giờ',
            'daily' => 'Hàng ngày',
            'weekly' => 'Hàng tuần',
            'never' => 'Không bao giờ'
        ];
    }

    /**
     * Check if user should receive notification
     *
     * @param User $user
     * @param string $category
     * @param string $type
     * @param string $method
     * @return bool
     */
    public static function shouldReceiveNotification(User $user, string $category, string $type, string $method = 'email'): bool
    {
        $preferences = self::getUserPreferences($user);

        // Check global settings
        if (!$preferences['global'][$method . '_enabled']) {
            return false;
        }

        // Check category settings
        if (!$preferences['categories'][$category]['enabled']) {
            return false;
        }

        // Check specific type settings
        return $preferences['categories'][$category]['types'][$type][$method] ?? false;
    }

    /**
     * Get default preferences for new users
     *
     * @return array
     */
    public static function getDefaultPreferences(): array
    {
        $categories = self::getNotificationCategories();
        $deliveryMethods = self::getDeliveryMethods();

        $defaults = [
            'global' => [
                'email_enabled' => true,
                'push_enabled' => true,
                'sms_enabled' => false,
                'in_app_enabled' => true,
            ],
            'categories' => [],
            'delivery_methods' => []
        ];

        // Set default category preferences
        foreach ($categories as $categoryKey => $category) {
            $defaults['categories'][$categoryKey] = [
                'enabled' => true,
                'types' => []
            ];

            foreach ($category['types'] as $typeKey => $typeName) {
                $defaults['categories'][$categoryKey]['types'][$typeKey] = [
                    'email' => in_array($categoryKey, ['security', 'marketplace']), // Only important categories by email
                    'push' => true,
                    'sms' => $categoryKey === 'security', // Only security via SMS
                    'in_app' => true,
                ];
            }
        }

        // Set default delivery method preferences
        foreach ($deliveryMethods as $methodKey => $method) {
            $defaults['delivery_methods'][$methodKey] = [
                'enabled' => $methodKey !== 'sms',
                'frequency' => 'immediate',
                'quiet_hours' => [
                    'enabled' => $methodKey === 'push',
                    'start' => '22:00',
                    'end' => '08:00'
                ]
            ];
        }

        return $defaults;
    }
}
