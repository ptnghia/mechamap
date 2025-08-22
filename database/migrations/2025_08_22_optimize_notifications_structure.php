<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tối ưu hóa cấu trúc bảng notifications
     */
    public function up(): void
    {
        echo "🚀 Optimizing notifications table structure...\n";

        try {
            // Step 1: Update categories based on notification types
            echo "📋 Updating notification categories...\n";
            $this->updateNotificationCategories();

            // Step 2: Update status based on read status
            echo "📋 Updating notification statuses...\n";
            $this->updateNotificationStatuses();

            // Step 3: Add optimized indexes
            echo "📋 Adding optimized indexes...\n";
            $this->addOptimizedIndexes();

            // Step 4: Clean up test data (optional)
            echo "📋 Cleaning up test data...\n";
            $this->cleanupTestData();

            echo "✅ Notifications table optimization completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during optimization: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Update notification categories based on types
     */
    private function updateNotificationCategories(): void
    {
        $categoryMappings = [
            // Forum activities
            'forum' => [
                'forum_activity', 'thread_created', 'thread_replied', 'comment_created',
                'comment_mention', 'thread_liked', 'comment_liked'
            ],
            
            // Marketplace activities
            'marketplace' => [
                'marketplace_activity', 'product_approved', 'product_rejected',
                'order_update', 'commission_paid', 'quote_request', 'business_verified',
                'business_rejected'
            ],
            
            // Social activities
            'social' => [
                'user_followed', 'user_registered', 'user_mention', 'like_received',
                'follow_received', 'message_received'
            ],
            
            // Security activities
            'security' => [
                'login_from_new_device', 'password_changed', 'account_locked',
                'suspicious_activity', 'security_alert'
            ],
            
            // System activities (default)
            'system' => [
                'system_announcement', 'role_changed', 'maintenance_notice',
                'feature_update', 'policy_update'
            ]
        ];

        foreach ($categoryMappings as $category => $types) {
            $updated = DB::table('notifications')
                ->whereIn('type', $types)
                ->update(['category' => $category]);
            
            if ($updated > 0) {
                echo "  ✅ Updated {$updated} notifications to category '{$category}'\n";
            }
        }
    }

    /**
     * Update notification statuses based on read status
     */
    private function updateNotificationStatuses(): void
    {
        // Update read notifications
        $readUpdated = DB::table('notifications')
            ->where('is_read', true)
            ->whereNotNull('read_at')
            ->update(['status' => 'read']);
        
        if ($readUpdated > 0) {
            echo "  ✅ Updated {$readUpdated} notifications to status 'read'\n";
        }

        // Update delivered notifications (sent but not read)
        $deliveredUpdated = DB::table('notifications')
            ->where('is_read', false)
            ->where('status', 'pending')
            ->update(['status' => 'delivered']);
        
        if ($deliveredUpdated > 0) {
            echo "  ✅ Updated {$deliveredUpdated} notifications to status 'delivered'\n";
        }
    }

    /**
     * Add optimized indexes for better performance
     */
    private function addOptimizedIndexes(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Composite index for user queries
            if (!$this->indexExists('notifications', 'idx_user_status_created')) {
                $table->index(['user_id', 'status', 'created_at'], 'idx_user_status_created');
                echo "  ✅ Added index: idx_user_status_created\n";
            }

            // Index for type and category queries
            if (!$this->indexExists('notifications', 'idx_type_category_created')) {
                $table->index(['type', 'category', 'created_at'], 'idx_type_category_created');
                echo "  ✅ Added index: idx_type_category_created\n";
            }

            // Index for priority queries
            if (!$this->indexExists('notifications', 'idx_priority_urgency')) {
                $table->index(['priority', 'urgency_level'], 'idx_priority_urgency');
                echo "  ✅ Added index: idx_priority_urgency\n";
            }

            // Index for scheduled notifications
            if (!$this->indexExists('notifications', 'idx_scheduled_status')) {
                $table->index(['scheduled_at', 'status'], 'idx_scheduled_status');
                echo "  ✅ Added index: idx_scheduled_status\n";
            }
        });
    }

    /**
     * Clean up test data
     */
    private function cleanupTestData(): void
    {
        $testTypes = [
            'performance_test',
            'bulk_performance_test', 
            'concurrent_test',
            'test_cleanup',
            'test_rename'
        ];

        $deleted = DB::table('notifications')
            ->whereIn('type', $testTypes)
            ->delete();

        if ($deleted > 0) {
            echo "  ✅ Cleaned up {$deleted} test notifications\n";
        }
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back notifications optimization...\n";

        try {
            // Remove added indexes
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_user_status_created');
                $table->dropIndex('idx_type_category_created');
                $table->dropIndex('idx_priority_urgency');
                $table->dropIndex('idx_scheduled_status');
            });

            // Reset categories to 'system'
            DB::table('notifications')->update(['category' => 'system']);

            // Reset statuses to 'pending'
            DB::table('notifications')->update(['status' => 'pending']);

            echo "✅ Rollback completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
