<?php

namespace App\Services;

class NotificationCategoryService
{
    /**
     * Mapping của 43 loại thông báo thành 6 danh mục chính
     */
    public static function getCategoryMapping(): array
    {
        return [
            'messages' => [
                'message_received',
                'seller_message',
            ],
            'forum' => [
                'thread_created',
                'thread_replied',
                'comment_mention',
                'forum_activity',
            ],
            'marketplace' => [
                'product_approved',
                'order_update',
                'order_status_changed',
                'price_drop_alert',
                'product_out_of_stock',
                'review_received',
                'wishlist_available',
                'marketplace_activity',
                'commission_paid',
            ],
            'security' => [
                'login_from_new_device',
                'password_changed',
                'security_alert',
            ],
            'social' => [
                'user_followed',
                'user_registered',
                'achievement_unlocked',
                'business_verified',
            ],
            'system' => [
                'system_announcement',
            ],
        ];
    }

    /**
     * Lấy category của một notification type
     */
    public static function getCategory(string $type): string
    {
        $mapping = self::getCategoryMapping();
        
        foreach ($mapping as $category => $types) {
            if (in_array($type, $types)) {
                return $category;
            }
        }
        
        return 'system'; // Default category
    }

    /**
     * Lấy tất cả categories với metadata
     */
    public static function getCategories(): array
    {
        return [
            'all' => [
                'name' => 'All Notifications',
                'name_vi' => 'Tất cả thông báo',
                'icon' => 'fas fa-bell',
                'color' => 'primary',
                'description' => 'All notification types',
                'description_vi' => 'Tất cả loại thông báo',
            ],
            'messages' => [
                'name' => 'Messages',
                'name_vi' => 'Tin nhắn',
                'icon' => 'fas fa-envelope',
                'color' => 'info',
                'description' => 'Direct messages and seller communications',
                'description_vi' => 'Tin nhắn trực tiếp và liên lạc với seller',
            ],
            'forum' => [
                'name' => 'Forum',
                'name_vi' => 'Diễn đàn',
                'icon' => 'fas fa-comments',
                'color' => 'success',
                'description' => 'Thread replies, mentions and forum activities',
                'description_vi' => 'Phản hồi thread, nhắc đến và hoạt động diễn đàn',
            ],
            'marketplace' => [
                'name' => 'Marketplace',
                'name_vi' => 'Thương mại',
                'icon' => 'fas fa-shopping-cart',
                'color' => 'warning',
                'description' => 'Orders, products, reviews and marketplace activities',
                'description_vi' => 'Đơn hàng, sản phẩm, đánh giá và hoạt động thương mại',
            ],
            'security' => [
                'name' => 'Security',
                'name_vi' => 'Bảo mật',
                'icon' => 'fas fa-shield-alt',
                'color' => 'danger',
                'description' => 'Login alerts, password changes and security notifications',
                'description_vi' => 'Cảnh báo đăng nhập, thay đổi mật khẩu và thông báo bảo mật',
            ],
            'social' => [
                'name' => 'Social',
                'name_vi' => 'Xã hội',
                'icon' => 'fas fa-users',
                'color' => 'purple',
                'description' => 'Followers, achievements and social activities',
                'description_vi' => 'Người theo dõi, thành tựu và hoạt động xã hội',
            ],
            'system' => [
                'name' => 'System',
                'name_vi' => 'Hệ thống',
                'icon' => 'fas fa-cog',
                'color' => 'secondary',
                'description' => 'System announcements and administrative notifications',
                'description_vi' => 'Thông báo hệ thống và thông báo quản trị',
            ],
        ];
    }

    /**
     * Lấy icon cho notification type
     */
    public static function getTypeIcon(string $type): string
    {
        $icons = [
            // Messages
            'message_received' => 'fas fa-envelope',
            'seller_message' => 'fas fa-store',
            
            // Forum
            'thread_created' => 'fas fa-plus-circle',
            'thread_replied' => 'fas fa-reply',
            'comment_mention' => 'fas fa-at',
            'forum_activity' => 'fas fa-comments',
            
            // Marketplace
            'product_approved' => 'fas fa-check-circle',
            'order_update' => 'fas fa-box',
            'order_status_changed' => 'fas fa-truck',
            'price_drop_alert' => 'fas fa-arrow-down',
            'product_out_of_stock' => 'fas fa-exclamation-triangle',
            'review_received' => 'fas fa-star',
            'wishlist_available' => 'fas fa-heart',
            'marketplace_activity' => 'fas fa-shopping-cart',
            'commission_paid' => 'fas fa-dollar-sign',
            
            // Security
            'login_from_new_device' => 'fas fa-mobile-alt',
            'password_changed' => 'fas fa-key',
            'security_alert' => 'fas fa-shield-alt',
            
            // Social
            'user_followed' => 'fas fa-user-plus',
            'user_registered' => 'fas fa-user-check',
            'achievement_unlocked' => 'fas fa-trophy',
            'business_verified' => 'fas fa-certificate',
            
            // System
            'system_announcement' => 'fas fa-bullhorn',
        ];
        
        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Lấy color cho priority level
     */
    public static function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'primary',
            'low' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Đếm notifications theo category cho user
     */
    public static function getCategoryCounts(int $userId): array
    {
        $counts = [];
        $categories = array_keys(self::getCategoryMapping());
        
        // Thêm 'all' category
        $categories[] = 'all';
        
        foreach ($categories as $category) {
            if ($category === 'all') {
                $counts[$category] = \App\Models\Notification::where('user_id', $userId)->count();
            } else {
                $types = self::getCategoryMapping()[$category];
                $counts[$category] = \App\Models\Notification::where('user_id', $userId)
                    ->whereIn('type', $types)
                    ->count();
            }
        }
        
        return $counts;
    }

    /**
     * Đếm unread notifications theo category cho user
     */
    public static function getUnreadCategoryCounts(int $userId): array
    {
        $counts = [];
        $categories = array_keys(self::getCategoryMapping());
        
        // Thêm 'all' category
        $categories[] = 'all';
        
        foreach ($categories as $category) {
            if ($category === 'all') {
                $counts[$category] = \App\Models\Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->count();
            } else {
                $types = self::getCategoryMapping()[$category];
                $counts[$category] = \App\Models\Notification::where('user_id', $userId)
                    ->whereIn('type', $types)
                    ->where('is_read', false)
                    ->count();
            }
        }
        
        return $counts;
    }
}
