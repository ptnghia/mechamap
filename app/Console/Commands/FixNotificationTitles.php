<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class FixNotificationTitles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:fix-titles {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     */
    protected $description = 'Fix notification titles to use correct translation keys';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔧 Fixing notification titles in database...');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
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
            $this->line("🔍 Processing type: {$type}");
            
            // Update notifications that have wrong translation key format
            $wrongKeyPatterns = [
                "notifications.types.{$type}",
                "notifications.{$type}",  // Without .title
                $type,  // Just the type name
            ];

            foreach ($wrongKeyPatterns as $wrongPattern) {
                if ($dryRun) {
                    $count = Notification::where('type', $type)
                        ->where('title', $wrongPattern)
                        ->count();
                    
                    if ($count > 0) {
                        $this->line("   🔄 Would fix {$count} notifications with wrong key '{$wrongPattern}'");
                        $fixedCount += $count;
                    }
                } else {
                    $updated = Notification::where('type', $type)
                        ->where('title', $wrongPattern)
                        ->update(['title' => $correctKey]);
                    
                    if ($updated > 0) {
                        $this->line("   ✅ Fixed {$updated} notifications with wrong key '{$wrongPattern}'");
                        $fixedCount += $updated;
                    }
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
                    if ($dryRun) {
                        $count = Notification::where('type', $type)
                            ->where('title', $hardcodedTitle)
                            ->count();
                        
                        if ($count > 0) {
                            $this->line("   🔄 Would fix {$count} notifications with hardcoded title '{$hardcodedTitle}'");
                            $fixedCount += $count;
                        }
                    } else {
                        $updated = Notification::where('type', $type)
                            ->where('title', $hardcodedTitle)
                            ->update(['title' => $correctKey]);
                        
                        if ($updated > 0) {
                            $this->line("   ✅ Fixed {$updated} notifications with hardcoded title '{$hardcodedTitle}'");
                            $fixedCount += $updated;
                        }
                    }
                }
            }
        }

        $this->newLine();
        if ($fixedCount > 0) {
            if ($dryRun) {
                $this->info("🎉 Would fix {$fixedCount} notification titles!");
            } else {
                $this->info("🎉 Fixed {$fixedCount} notification titles to use correct translation keys!");
            }
        } else {
            $this->info("✅ All notification titles are already using correct format");
        }

        return 0;
    }
}
