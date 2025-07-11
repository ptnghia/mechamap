<?php

namespace App\Services;

use App\Models\User;

/**
 * 👤 Guest Permission Service
 *
 * Quản lý permissions cho Guest users theo đề xuất:
 * - Guest cần đăng ký như Member
 * - Có quyền xem nội dung và follow
 * - Không có quyền tạo nội dung
 */
class GuestPermissionService
{
    /**
     * Get all permissions for guest role
     */
    public static function getGuestPermissions(): array
    {
        return [
            // ✅ ALLOWED - Viewing & Browsing
            'view-content' => true,
            'browse-forums' => true,
            'browse-showcases' => true,
            'view-threads' => true,
            'view-comments' => true,
            'view-user-profiles' => true,

            // ✅ ALLOWED - Social Features (Follow Only)
            'follow-users' => true,
            'follow-threads' => true,
            'follow-showcases' => true,
            'receive-notifications' => true,
            'view-following-feed' => true,

            // ❌ RESTRICTED - Content Creation
            'create-threads' => false,
            'create-comments' => false,
            'create-showcases' => false,
            'edit-own-content' => false,
            'delete-own-content' => false,

            // ❌ RESTRICTED - Interactions
            'vote-polls' => false,
            'rate-content' => false,
            'like-content' => false,
            'share-content' => false,
            'report-content' => false,

            // ✅ ALLOWED - Marketplace (Digital Products Only)
            'marketplace-access' => true,
            'buy-products' => true,
            'sell-products' => true,
            'create-products' => true,  // Với admin approval

            // ❌ RESTRICTED - Advanced Features
            'send-messages' => false,
            'upload-files' => false,
            'create-polls' => false,
            'manage-profile' => false,
        ];
    }

    /**
     * Check if guest can perform specific action
     */
    public static function canPerform(string $action): bool
    {
        $permissions = self::getGuestPermissions();
        return $permissions[$action] ?? false;
    }

    /**
     * Get allowed actions for guest
     */
    public static function getAllowedActions(): array
    {
        return array_keys(array_filter(self::getGuestPermissions()));
    }

    /**
     * Get restricted actions for guest
     */
    public static function getRestrictedActions(): array
    {
        return array_keys(array_filter(self::getGuestPermissions(), function($allowed) {
            return !$allowed;
        }));
    }

    /**
     * Check if user can follow another user
     */
    public static function canFollowUser(User $guest, User $target): bool
    {
        // Guest can follow any active member
        return $guest->role === 'guest' &&
               !in_array($target->role, ['guest']) &&
               $target->is_active &&
               $guest->id !== $target->id;
    }

    /**
     * Check if user can follow thread
     */
    public static function canFollowThread(User $guest, $thread): bool
    {
        // Guest can follow any public thread
        return $guest->role === 'guest' &&
               $thread->is_public &&
               !$thread->is_locked;
    }

    /**
     * Check if user can view content
     */
    public static function canViewContent(User $guest, $content): bool
    {
        // Guest can view all public content
        return $guest->role === 'guest' &&
               ($content->is_public ?? true) &&
               !($content->is_private ?? false);
    }

    /**
     * Get upgrade incentive message for guest
     */
    public static function getUpgradeIncentive(string $action): string
    {
        $incentives = [
            'create-threads' => 'Đăng ký thành Member để tạo chủ đề thảo luận!',
            'create-comments' => 'Đăng ký thành Member để tham gia bình luận!',
            'create-showcases' => 'Đăng ký thành Member để chia sẻ dự án của bạn!',
            'vote-polls' => 'Đăng ký thành Member để tham gia bình chọn!',
            'rate-content' => 'Đăng ký thành Member để đánh giá nội dung!',
            'marketplace-access' => 'Đăng ký thành Member để truy cập marketplace!',
            'send-messages' => 'Đăng ký thành Member để gửi tin nhắn!',
            'upload-files' => 'Đăng ký thành Member để tải lên tệp tin!',
        ];

        return $incentives[$action] ?? 'Đăng ký thành Member để sử dụng tính năng này!';
    }

    /**
     * Get guest dashboard features
     */
    public static function getDashboardFeatures(): array
    {
        return [
            'following_feed' => [
                'title' => 'Bảng tin theo dõi',
                'description' => 'Cập nhật từ những người và chủ đề bạn theo dõi',
                'icon' => 'fas fa-rss',
                'enabled' => true,
            ],
            'trending_content' => [
                'title' => 'Nội dung thịnh hành',
                'description' => 'Khám phá nội dung hot nhất trong cộng đồng',
                'icon' => 'fas fa-fire',
                'enabled' => true,
            ],
            'recommended_users' => [
                'title' => 'Gợi ý theo dõi',
                'description' => 'Những thành viên bạn có thể quan tâm',
                'icon' => 'fas fa-user-plus',
                'enabled' => true,
            ],
            'upgrade_prompt' => [
                'title' => 'Nâng cấp tài khoản',
                'description' => 'Trở thành Member để sử dụng đầy đủ tính năng',
                'icon' => 'fas fa-star',
                'enabled' => true,
                'action' => 'upgrade-to-member',
            ],
        ];
    }

    /**
     * Check if guest has reached follow limits
     */
    public static function hasReachedFollowLimit(User $guest): array
    {
        $limits = [
            'users' => 50,      // Max 50 users to follow
            'threads' => 100,   // Max 100 threads to follow
            'showcases' => 30,  // Max 30 showcases to follow
        ];

        $current = [
            'users' => $guest->following()->count(),
            'threads' => $guest->followedThreads()->count(),
            'showcases' => $guest->followedShowcases()->count() ?? 0,
        ];

        return [
            'users' => [
                'current' => $current['users'],
                'limit' => $limits['users'],
                'reached' => $current['users'] >= $limits['users'],
            ],
            'threads' => [
                'current' => $current['threads'],
                'limit' => $limits['threads'],
                'reached' => $current['threads'] >= $limits['threads'],
            ],
            'showcases' => [
                'current' => $current['showcases'],
                'limit' => $limits['showcases'],
                'reached' => $current['showcases'] >= $limits['showcases'],
            ],
        ];
    }

    /**
     * Get guest role description
     */
    public static function getRoleDescription(): array
    {
        return [
            'name' => 'Guest',
            'display_name' => 'Khách',
            'level' => 10,
            'description' => 'Tài khoản khách với quyền xem và theo dõi nội dung',
            'features' => [
                '✅ Xem tất cả nội dung công khai',
                '✅ Theo dõi thành viên và chủ đề',
                '✅ Nhận thông báo cập nhật',
                '❌ Không thể tạo nội dung',
                '❌ Không thể tham gia marketplace',
                '❌ Không thể bình luận hoặc đánh giá',
            ],
            'upgrade_benefits' => [
                '🎯 Tạo chủ đề thảo luận',
                '💬 Bình luận và tương tác',
                '⭐ Đánh giá và bình chọn',
                '🛒 Truy cập marketplace',
                '📁 Tải lên tệp tin',
                '💌 Gửi tin nhắn riêng tư',
            ],
        ];
    }
}
