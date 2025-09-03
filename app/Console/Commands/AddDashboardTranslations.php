<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDashboardTranslations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:import-batch
                            {--dry-run : Preview changes without applying them}
                            {--group= : Import specific group only}
                            {--force : Force update existing translations}
                            {--locale= : Import specific locale only (vi|en)}';

    /**
     * The console command description.
     */
    protected $description = 'Import translation keys in batch with advanced options and validation';

    /**
     * Translation keys to import - Configure as needed
     *
     * Structure: 'key' => ['vi' => 'Vietnamese', 'en' => 'English']
     * Groups are auto-detected from key prefix (before first dot)
     */
    protected $translations = [
        // ===== THREADS FUNCTIONALITY TRANSLATIONS =====

        // UI Actions - Additional keys for threads functionality
        'ui.actions.unlike' => [
            'vi' => 'Bỏ thích',
            'en' => 'Unlike'
        ],
        'ui.actions.unsave' => [
            'vi' => 'Bỏ lưu',
            'en' => 'Unsave'
        ],
        'ui.actions.unfollow' => [
            'vi' => 'Bỏ theo dõi',
            'en' => 'Unfollow'
        ],

        // Thread notifications and real-time updates
        'thread.liked_thread' => [
            'vi' => 'đã thích bài viết này',
            'en' => 'liked this thread'
        ],
        'thread.liked_comment' => [
            'vi' => 'đã thích bình luận',
            'en' => 'liked a comment'
        ],
        'thread.replies' => [
            'vi' => 'phản hồi',
            'en' => 'replies'
        ],
        'thread.participants' => [
            'vi' => 'người tham gia',
            'en' => 'participants'
        ],

        // ===== NOTIFICATION SYSTEM TRANSLATIONS =====

        // Notification UI Actions
        'notifications.ui.mark_all_read' => [
            'vi' => 'Đánh dấu tất cả đã đọc',
            'en' => 'Mark all as read'
        ],
        'notifications.ui.view' => [
            'vi' => 'Xem',
            'en' => 'View'
        ],
        'notifications.ui.mark_as_read' => [
            'vi' => 'Đánh dấu đã đọc',
            'en' => 'Mark as read'
        ],

        // Default Notification Content
        'notifications.default.title' => [
            'vi' => 'Thông báo mới',
            'en' => 'New Notification'
        ],
        'notifications.default.message' => [
            'vi' => 'Bạn có một thông báo mới',
            'en' => 'You have a new notification'
        ],

        // ===== MARKETPLACE NOTIFICATION TYPES =====

        // Product Out of Stock
        'notifications.product_out_of_stock.title' => [
            'vi' => 'Sản phẩm hết hàng',
            'en' => 'Product Out of Stock'
        ],
        'notifications.product_out_of_stock.message' => [
            'vi' => 'Sản phẩm ":product_name" đã hết hàng',
            'en' => 'Product ":product_name" is now out of stock'
        ],

        // Price Drop Alert
        'notifications.price_drop_alert.title' => [
            'vi' => 'Cảnh báo giảm giá',
            'en' => 'Price Drop Alert'
        ],
        'notifications.price_drop_alert.message' => [
            'vi' => 'Sản phẩm ":product_name" đã giảm giá từ :old_price xuống :new_price',
            'en' => 'Product ":product_name" price dropped from :old_price to :new_price'
        ],

        // Wishlist Available
        'notifications.wishlist_available.title' => [
            'vi' => 'Sản phẩm yêu thích có sẵn',
            'en' => 'Wishlist Item Available'
        ],
        'notifications.wishlist_available.message' => [
            'vi' => 'Sản phẩm ":product_name" trong danh sách yêu thích của bạn đã có sẵn',
            'en' => 'Product ":product_name" from your wishlist is now available'
        ],

        // Marketplace Activity
        'notifications.marketplace_activity.title' => [
            'vi' => 'Hoạt động thị trường',
            'en' => 'Marketplace Activity'
        ],
        'notifications.marketplace_activity.message' => [
            'vi' => 'Có hoạt động mới trong thị trường',
            'en' => 'New activity in the marketplace'
        ],

        // Order Status Changed
        'notifications.order_status_changed.title' => [
            'vi' => 'Trạng thái đơn hàng thay đổi',
            'en' => 'Order Status Changed'
        ],
        'notifications.order_status_changed.message' => [
            'vi' => 'Đơn hàng #:order_id của bạn đã được cập nhật trạng thái: :status',
            'en' => 'Your order #:order_id status has been updated to: :status'
        ],

        // Order Update
        'notifications.order_update.title' => [
            'vi' => 'Cập nhật đơn hàng',
            'en' => 'Order Update'
        ],
        'notifications.order_update.message' => [
            'vi' => 'Có cập nhật mới cho đơn hàng của bạn',
            'en' => 'There is a new update for your order'
        ],

        // Review Received
        'notifications.review_received.title' => [
            'vi' => 'Nhận được đánh giá',
            'en' => 'Review Received'
        ],
        'notifications.review_received.message' => [
            'vi' => 'Bạn đã nhận được một đánh giá mới',
            'en' => 'You have received a new review'
        ],

        // Commission Paid
        'notifications.commission_paid.title' => [
            'vi' => 'Hoa hồng đã thanh toán',
            'en' => 'Commission Paid'
        ],
        'notifications.commission_paid.message' => [
            'vi' => 'Hoa hồng của bạn đã được thanh toán thành công',
            'en' => 'Your commission has been paid successfully'
        ],

        // ===== FORUM NOTIFICATION TYPES =====

        // Thread Created
        'notifications.thread_created.title' => [
            'vi' => 'Chủ đề mới',
            'en' => 'New Thread'
        ],
        'notifications.thread_created.message' => [
            'vi' => 'Có chủ đề mới trong diễn đàn bạn theo dõi',
            'en' => 'New thread created in your followed forum'
        ],

        // Thread Replied
        'notifications.thread_replied.title' => [
            'vi' => 'Trả lời chủ đề',
            'en' => 'Thread Reply'
        ],
        'notifications.thread_replied.message' => [
            'vi' => 'Có người đã trả lời chủ đề của bạn',
            'en' => 'Someone replied to your thread'
        ],

        // Comment Mention
        'notifications.comment_mention.title' => [
            'vi' => 'Được nhắc đến',
            'en' => 'You were mentioned'
        ],
        'notifications.comment_mention.message' => [
            'vi' => 'Bạn đã được nhắc đến trong một bình luận',
            'en' => 'You were mentioned in a comment'
        ],

        // Forum Activity
        'notifications.forum_activity.title' => [
            'vi' => 'Hoạt động diễn đàn',
            'en' => 'Forum Activity'
        ],
        'notifications.forum_activity.message' => [
            'vi' => 'Có hoạt động mới trong diễn đàn',
            'en' => 'New activity in the forum'
        ],

        // ===== MESSAGE NOTIFICATION TYPES =====

        // Message Received
        'notifications.message_received.title' => [
            'vi' => 'Tin nhắn mới',
            'en' => 'New Message'
        ],
        'notifications.message_received.message' => [
            'vi' => 'Bạn có một tin nhắn mới từ :sender_name',
            'en' => 'You have a new message from :sender_name'
        ],

        // Seller Message
        'notifications.seller_message.title' => [
            'vi' => 'Tin nhắn từ người bán',
            'en' => 'Message from Seller'
        ],
        'notifications.seller_message.message' => [
            'vi' => 'Bạn có tin nhắn mới từ người bán',
            'en' => 'You have a new message from seller'
        ],

        // ===== SOCIAL NOTIFICATION TYPES =====

        // User Followed
        'notifications.user_followed.title' => [
            'vi' => 'Người theo dõi mới',
            'en' => 'New Follower'
        ],
        'notifications.user_followed.message' => [
            'vi' => ':follower_name đã bắt đầu theo dõi bạn',
            'en' => ':follower_name started following you'
        ],

        // Achievement Unlocked
        'notifications.achievement_unlocked.title' => [
            'vi' => 'Thành tựu mới',
            'en' => 'Achievement Unlocked'
        ],
        'notifications.achievement_unlocked.message' => [
            'vi' => 'Chúc mừng! Bạn đã đạt được thành tựu mới',
            'en' => 'Congratulations! You unlocked a new achievement'
        ],

        // Business Verified
        'notifications.business_verified.title' => [
            'vi' => 'Doanh nghiệp được xác minh',
            'en' => 'Business Verified'
        ],
        'notifications.business_verified.message' => [
            'vi' => 'Doanh nghiệp của bạn đã được xác minh thành công',
            'en' => 'Your business has been successfully verified'
        ],

        // User Registered
        'notifications.user_registered.title' => [
            'vi' => 'Người dùng đăng ký',
            'en' => 'User Registered'
        ],
        'notifications.user_registered.message' => [
            'vi' => 'Có người dùng mới đăng ký',
            'en' => 'A new user has registered'
        ],

        // ===== SECURITY NOTIFICATION TYPES =====

        // Security Alert
        'notifications.security_alert.title' => [
            'vi' => 'Cảnh báo bảo mật',
            'en' => 'Security Alert'
        ],
        'notifications.security_alert.message' => [
            'vi' => 'Có hoạt động bảo mật cần chú ý trong tài khoản của bạn',
            'en' => 'There is security activity that needs attention in your account'
        ],

        // Password Changed
        'notifications.password_changed.title' => [
            'vi' => 'Mật khẩu đã thay đổi',
            'en' => 'Password Changed'
        ],
        'notifications.password_changed.message' => [
            'vi' => 'Mật khẩu tài khoản của bạn đã được thay đổi',
            'en' => 'Your account password has been changed'
        ],

        // Login from New Device
        'notifications.login_from_new_device.title' => [
            'vi' => 'Đăng nhập từ thiết bị mới',
            'en' => 'Login from New Device'
        ],
        'notifications.login_from_new_device.message' => [
            'vi' => 'Tài khoản của bạn đã được đăng nhập từ một thiết bị mới',
            'en' => 'Your account was logged in from a new device'
        ],

        // ===== SYSTEM NOTIFICATION TYPES =====

        // System Announcement
        'notifications.system_announcement.title' => [
            'vi' => 'Thông báo hệ thống',
            'en' => 'System Announcement'
        ],
        'notifications.system_announcement.message' => [
            'vi' => 'Có thông báo quan trọng từ hệ thống',
            'en' => 'Important announcement from the system'
        ],

        // ===== MARKETPLACE DIGITAL PRODUCTS TRANSLATIONS =====

        // Digital Products Create Page
        'marketplace.digital_products.create_title' => [
            'vi' => 'Tạo Sản Phẩm Số',
            'en' => 'Create Digital Product'
        ],

        // ===== SHOWCASE SIDEBAR TRANSLATIONS =====

        // Author Profile Section
        'showcase.sidebar.author_profile' => [
            'vi' => 'Hồ sơ tác giả',
            'en' => 'Author Profile'
        ],
        'showcase.sidebar.member_since' => [
            'vi' => 'Tham gia từ',
            'en' => 'Member since'
        ],
        'showcase.sidebar.total_showcases' => [
            'vi' => 'Tổng showcases',
            'en' => 'Total showcases'
        ],
        'showcase.sidebar.total_views' => [
            'vi' => 'Tổng lượt xem',
            'en' => 'Total views'
        ],
        'showcase.sidebar.avg_rating' => [
            'vi' => 'Đánh giá TB',
            'en' => 'Avg rating'
        ],
        'showcase.sidebar.view_profile' => [
            'vi' => 'Xem hồ sơ',
            'en' => 'View Profile'
        ],
        'showcase.sidebar.follow' => [
            'vi' => 'Theo dõi',
            'en' => 'Follow'
        ],
        'showcase.sidebar.unfollow' => [
            'vi' => 'Bỏ theo dõi',
            'en' => 'Unfollow'
        ],
        'showcase.sidebar.contact' => [
            'vi' => 'Liên hệ',
            'en' => 'Contact'
        ],

        // Other Showcases Section
        'showcase.sidebar.other_showcases' => [
            'vi' => 'Showcases khác của tác giả',
            'en' => 'Other showcases by author'
        ],
        'showcase.sidebar.no_other_showcases' => [
            'vi' => 'Tác giả chưa có showcase nào khác',
            'en' => 'Author has no other showcases'
        ],

        // Featured Showcases Section
        'showcase.sidebar.featured_showcases' => [
            'vi' => 'Showcases nổi bật',
            'en' => 'Featured Showcases'
        ],
        'showcase.sidebar.no_featured_showcases' => [
            'vi' => 'Chưa có showcase nổi bật',
            'en' => 'No featured showcases'
        ],

        // Top Contributors Section
        'showcase.sidebar.top_contributors' => [
            'vi' => 'Người đóng góp hàng đầu',
            'en' => 'Top Contributors'
        ],
        'showcase.sidebar.no_contributors' => [
            'vi' => 'Chưa có dữ liệu người đóng góp',
            'en' => 'No contributors data'
        ],

        // General Sidebar
        'showcase.sidebar.views' => [
            'vi' => 'lượt xem',
            'en' => 'views'
        ],
        'showcase.sidebar.showcases' => [
            'vi' => 'showcases',
            'en' => 'showcases'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $groupFilter = $this->option('group');
        $localeFilter = $this->option('locale');
        $forceUpdate = $this->option('force');

        $stats = [
            'added' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $this->displayHeader($dryRun, $groupFilter, $localeFilter, $forceUpdate);

        // Validate options
        if ($localeFilter && !in_array($localeFilter, ['vi', 'en'])) {
            $this->error('❌ Invalid locale. Use: vi or en');
            return 1;
        }

        // Filter translations based on options
        $filteredTranslations = $this->filterTranslations($groupFilter, $localeFilter);

        if (empty($filteredTranslations)) {
            $this->warn('⚠️  No translations to process with current filters');
            return 0;
        }

        $this->info("� Processing " . count($filteredTranslations) . " translation keys...");

        DB::beginTransaction();

        try {
            foreach ($filteredTranslations as $key => $locales) {
                $this->processTranslationKey($key, $locales, $dryRun, $forceUpdate, $stats);
            }

            if (!$dryRun) {
                DB::commit();
                $this->info('💾 Changes committed to database');
                $this->clearTranslationCache();
            } else {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('📍 Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->displaySummary($stats, $dryRun);
        return 0;
    }

    /**
     * Display command header with options
     */
    private function displayHeader(bool $dryRun, ?string $group, ?string $locale, bool $force): void
    {
        $this->info('🚀 Translation Batch Import Tool');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        if ($group) {
            $this->info("🎯 Group filter: {$group}");
        }

        if ($locale) {
            $this->info("🌐 Locale filter: {$locale}");
        }

        if ($force) {
            $this->warn('⚡ Force mode: Will update existing translations');
        }

        $this->newLine();
    }

    /**
     * Filter translations based on options
     */
    private function filterTranslations(?string $groupFilter, ?string $localeFilter): array
    {
        $filtered = [];

        foreach ($this->translations as $key => $locales) {
            // Filter by group
            if ($groupFilter) {
                $keyGroup = explode('.', $key)[0];
                if ($keyGroup !== $groupFilter) {
                    continue;
                }
            }

            // Filter by locale
            if ($localeFilter) {
                $locales = array_filter($locales, function($locale) use ($localeFilter) {
                    return $locale === $localeFilter;
                }, ARRAY_FILTER_USE_KEY);

                if (empty($locales)) {
                    continue;
                }
            }

            $filtered[$key] = $locales;
        }

        return $filtered;
    }

    /**
     * Process a single translation key
     */
    private function processTranslationKey(string $key, array $locales, bool $dryRun, bool $forceUpdate, array &$stats): void
    {
        foreach ($locales as $locale => $content) {
            $existing = Translation::where('key', $key)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$forceUpdate) {
                $this->line("⏭️  Skipped: {$key} ({$locale}) - already exists");
                $stats['skipped']++;
                continue;
            }

            if (!$dryRun) {
                if ($existing && $forceUpdate) {
                    // Update existing
                    $existing->update([
                        'content' => $content,
                        'updated_by' => 1,
                        'updated_at' => now(),
                    ]);
                    $this->line("🔄 Updated: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    // Create new
                    Translation::create([
                        'key' => $key,
                        'content' => $content,
                        'locale' => $locale,
                        'group_name' => explode('.', $key)[0],
                        'is_active' => true,
                        'created_by' => 1,
                    ]);
                    $this->line("✅ Added: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            } else {
                if ($existing && $forceUpdate) {
                    $this->line("🔄 Would update: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    $this->line("✅ Would add: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            }
        }
    }

    /**
     * Clear translation cache
     */
    private function clearTranslationCache(): void
    {
        try {
            cache()->tags(['translations'])->flush();
            cache()->forget('translations.*');
            $this->info('🗑️  Translation cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Display summary statistics
     */
    private function displaySummary(array $stats, bool $dryRun): void
    {
        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($stats['added'] > 0) {
            $this->info("✅ Added: {$stats['added']} translations");
        }

        if ($stats['updated'] > 0) {
            $this->info("🔄 Updated: {$stats['updated']} translations");
        }

        if ($stats['skipped'] > 0) {
            $this->info("⏭️  Skipped: {$stats['skipped']} translations");
        }

        if (!empty($stats['errors'])) {
            $this->error("❌ Errors: " . count($stats['errors']));
            foreach ($stats['errors'] as $error) {
                $this->error("   - {$error}");
            }
        }

        if (!$dryRun && ($stats['added'] > 0 || $stats['updated'] > 0)) {
            $this->newLine();
            $this->info('🎉 Translation import completed successfully!');
            $this->info('💡 Recommendations:');
            $this->line('   • Test translations on frontend');
            $this->line('   • Check translation management UI');
            $this->line('   • Verify cache is working properly');

            // Fix notification titles after importing translations
            $this->fixNotificationTitles();
        }
    }

    /**
     * Fix notification titles in database to use correct translation keys
     */
    private function fixNotificationTitles(): void
    {
        $this->newLine();
        $this->info('🔧 Fixing notification titles in database...');

        // Mapping of notification types to correct translation keys
        $titleMappings = [
            'commission_paid' => 'notifications.commission_paid.title',
            'wishlist_available' => 'notifications.wishlist_available.title',
            'price_drop_alert' => 'notifications.price_drop_alert.title',
            'order_update' => 'notifications.order_update.title',
            'order_status_changed' => 'notifications.order_status_changed.title',
            'review_received' => 'notifications.review_received.title',
            'product_out_of_stock' => 'notifications.product_out_of_stock.title',
            'marketplace_activity' => 'notifications.marketplace_activity.title',
            'thread_created' => 'notifications.thread_created.title',
            'thread_replied' => 'notifications.thread_replied.title',
            'comment_mention' => 'notifications.comment_mention.title',
            'forum_activity' => 'notifications.forum_activity.title',
            'message_received' => 'notifications.message_received.title',
            'seller_message' => 'notifications.seller_message.title',
            'user_followed' => 'notifications.user_followed.title',
            'achievement_unlocked' => 'notifications.achievement_unlocked.title',
            'business_verified' => 'notifications.business_verified.title',
            'user_registered' => 'notifications.user_registered.title',
            'security_alert' => 'notifications.security_alert.title',
            'password_changed' => 'notifications.password_changed.title',
            'login_from_new_device' => 'notifications.login_from_new_device.title',
            'system_announcement' => 'notifications.system_announcement.title',
        ];

        $fixedCount = 0;

        foreach ($titleMappings as $type => $correctKey) {
            // Update notifications that have wrong translation key format
            $wrongKeyPatterns = [
                "notifications.types.{$type}",
                "notifications.{$type}",  // Without .title
                $type,  // Just the type name
            ];

            foreach ($wrongKeyPatterns as $wrongPattern) {
                $updated = \App\Models\Notification::where('type', $type)
                    ->where('title', $wrongPattern)
                    ->update(['title' => $correctKey]);

                if ($updated > 0) {
                    $this->line("   ✅ Fixed {$updated} notifications of type '{$type}' with wrong key '{$wrongPattern}'");
                    $fixedCount += $updated;
                }
            }

            // Also update hardcoded titles to use translation keys
            $hardcodedTitles = [
                'commission_paid' => ['Hoa hồng đã được thanh toán', 'Commission Paid'],
                'wishlist_available' => ['Test Wishlist Available', 'Wishlist Available'],
                'price_drop_alert' => ['Price Drop Alert', 'Cảnh báo giảm giá'],
                'order_update' => ['Order Update', 'Cập nhật đơn hàng'],
            ];

            if (isset($hardcodedTitles[$type])) {
                foreach ($hardcodedTitles[$type] as $hardcodedTitle) {
                    $updated = \App\Models\Notification::where('type', $type)
                        ->where('title', $hardcodedTitle)
                        ->update(['title' => $correctKey]);

                    if ($updated > 0) {
                        $this->line("   ✅ Fixed {$updated} notifications of type '{$type}' with hardcoded title '{$hardcodedTitle}'");
                        $fixedCount += $updated;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $this->info("🎉 Fixed {$fixedCount} notification titles to use correct translation keys!");
        } else {
            $this->info("✅ All notification titles are already using correct format");
        }
    }
}
