<?php

namespace App\Services;

use App\Models\User;

/**
 * 👥 Member Permission Service
 * 
 * Quản lý permissions cho Member users:
 * - Member là thành viên của diễn đàn và showcase
 * - Có full community permissions
 * - Chỉ xem marketplace (không mua/bán)
 */
class MemberPermissionService
{
    /**
     * Get all permissions for member role
     */
    public static function getMemberPermissions(): array
    {
        return [
            // ✅ FULL ACCESS - Community Features
            'view-content' => true,
            'create-threads' => true,
            'create-comments' => true,
            'edit-own-content' => true,
            'delete-own-content' => true,
            'create-showcases' => true,
            'edit-showcases' => true,
            'manage-own-showcases' => true,
            
            // ✅ FULL ACCESS - Social Features
            'follow-users' => true,
            'follow-threads' => true,
            'follow-showcases' => true,
            'receive-notifications' => true,
            'send-messages' => true,
            'view-following-feed' => true,
            
            // ✅ FULL ACCESS - Interactions
            'vote-polls' => true,
            'rate-content' => true,
            'like-content' => true,
            'share-content' => true,
            'report-content' => true,
            'create-polls' => true,
            
            // ✅ FULL ACCESS - File Management
            'upload-files' => true,
            'manage-own-files' => true,
            'upload-images' => true,
            'upload-documents' => true,
            
            // ✅ LIMITED ACCESS - Marketplace (View Only)
            'view-marketplace' => true,
            'browse-products' => true,
            'view-product-details' => true,
            'view-seller-profiles' => true,
            
            // ❌ RESTRICTED - Marketplace Transactions
            'marketplace-buy' => false,
            'marketplace-sell' => false,
            'create-products' => false,
            'manage-cart' => false,
            'checkout' => false,
            'access-seller-dashboard' => false,
            
            // ✅ FULL ACCESS - Profile Management
            'manage-profile' => true,
            'update-avatar' => true,
            'update-bio' => true,
            'manage-privacy-settings' => true,
        ];
    }

    /**
     * Check if member can perform specific action
     */
    public static function canPerform(string $action): bool
    {
        $permissions = self::getMemberPermissions();
        return $permissions[$action] ?? false;
    }

    /**
     * Get community features for member
     */
    public static function getCommunityFeatures(): array
    {
        return [
            'forums' => [
                'create_threads' => true,
                'reply_threads' => true,
                'edit_own_posts' => true,
                'delete_own_posts' => true,
                'vote_polls' => true,
                'create_polls' => true,
            ],
            'showcases' => [
                'create_showcases' => true,
                'edit_showcases' => true,
                'comment_showcases' => true,
                'rate_showcases' => true,
                'share_showcases' => true,
            ],
            'social' => [
                'follow_users' => true,
                'send_messages' => true,
                'create_groups' => true,
                'join_groups' => true,
            ],
        ];
    }

    /**
     * Get marketplace features for member (view only)
     */
    public static function getMarketplaceFeatures(): array
    {
        return [
            'viewing' => [
                'browse_products' => true,
                'view_details' => true,
                'view_reviews' => true,
                'view_seller_info' => true,
                'search_products' => true,
                'filter_products' => true,
            ],
            'interactions' => [
                'save_favorites' => true,
                'share_products' => true,
                'compare_products' => true,
                'view_recommendations' => true,
            ],
            'restrictions' => [
                'buy_products' => false,
                'sell_products' => false,
                'add_to_cart' => false,
                'checkout' => false,
                'create_products' => false,
                'manage_orders' => false,
            ],
        ];
    }

    /**
     * Get upgrade incentives for marketplace access
     */
    public static function getMarketplaceUpgradeIncentives(): array
    {
        return [
            'buy_products' => [
                'message' => 'Nâng cấp lên Guest để mua sản phẩm kỹ thuật số!',
                'benefits' => [
                    'Mua sản phẩm kỹ thuật số',
                    'Tải về ngay lập tức',
                    'Hỗ trợ từ seller',
                    'Cập nhật sản phẩm',
                ],
            ],
            'sell_products' => [
                'message' => 'Nâng cấp lên Guest để bán sản phẩm của bạn!',
                'benefits' => [
                    'Bán sản phẩm kỹ thuật số',
                    'Kiếm thu nhập từ kỹ năng',
                    'Xây dựng thương hiệu cá nhân',
                    'Tiếp cận khách hàng toàn cầu',
                ],
            ],
            'business_features' => [
                'message' => 'Nâng cấp lên Business để có đầy đủ tính năng!',
                'benefits' => [
                    'Bán tất cả loại sản phẩm',
                    'Không cần duyệt admin',
                    'Analytics chi tiết',
                    'Hỗ trợ ưu tiên',
                ],
            ],
        ];
    }

    /**
     * Check member content creation limits
     */
    public static function getContentLimits(User $member): array
    {
        $memberLevel = $member->role === 'senior_member' ? 'senior' : 'regular';
        
        $limits = [
            'regular' => [
                'threads_per_day' => 10,
                'comments_per_day' => 50,
                'showcases_per_month' => 5,
                'file_upload_mb' => 50,
                'images_per_post' => 10,
            ],
            'senior' => [
                'threads_per_day' => 20,
                'comments_per_day' => 100,
                'showcases_per_month' => 10,
                'file_upload_mb' => 100,
                'images_per_post' => 20,
            ],
        ];

        return $limits[$memberLevel];
    }

    /**
     * Get member dashboard features
     */
    public static function getDashboardFeatures(): array
    {
        return [
            'community_activity' => [
                'title' => 'Hoạt động cộng đồng',
                'description' => 'Threads, comments, và showcases của bạn',
                'icon' => 'fas fa-users',
                'enabled' => true,
            ],
            'following_feed' => [
                'title' => 'Bảng tin theo dõi',
                'description' => 'Cập nhật từ những người bạn theo dõi',
                'icon' => 'fas fa-rss',
                'enabled' => true,
            ],
            'showcase_gallery' => [
                'title' => 'Thư viện dự án',
                'description' => 'Quản lý và chia sẻ dự án của bạn',
                'icon' => 'fas fa-images',
                'enabled' => true,
            ],
            'marketplace_browser' => [
                'title' => 'Duyệt marketplace',
                'description' => 'Khám phá sản phẩm và dịch vụ',
                'icon' => 'fas fa-store',
                'enabled' => true,
                'note' => 'Chỉ xem - Nâng cấp để mua/bán',
            ],
            'reputation_system' => [
                'title' => 'Hệ thống uy tín',
                'description' => 'Điểm uy tín và thành tích',
                'icon' => 'fas fa-star',
                'enabled' => true,
            ],
        ];
    }

    /**
     * Get member role description
     */
    public static function getRoleDescription(): array
    {
        return [
            'name' => 'Member',
            'display_name' => 'Thành viên',
            'level' => 8,
            'description' => 'Thành viên chính thức của cộng đồng diễn đàn và showcase',
            'primary_focus' => 'Community participation và knowledge sharing',
            'features' => [
                '✅ Tạo và tham gia thảo luận',
                '✅ Chia sẻ dự án showcase',
                '✅ Tương tác đầy đủ với cộng đồng',
                '✅ Gửi tin nhắn và kết bạn',
                '✅ Xem marketplace (không mua/bán)',
                '❌ Không tham gia giao dịch marketplace',
            ],
            'upgrade_options' => [
                'guest' => [
                    'title' => 'Nâng cấp lên Guest',
                    'purpose' => 'Để tham gia marketplace',
                    'benefits' => ['Mua/bán sản phẩm kỹ thuật số'],
                    'note' => 'Mất quyền tạo nội dung community',
                ],
                'business' => [
                    'title' => 'Nâng cấp lên Business',
                    'purpose' => 'Để kinh doanh chuyên nghiệp',
                    'benefits' => ['Full marketplace access', 'Business tools'],
                    'note' => 'Cần xác thực doanh nghiệp',
                ],
            ],
        ];
    }
}
