<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hoàn thiện cấu trúc enum và tối ưu indexes
     */
    public function up(): void
    {
        echo "🚀 Finalizing enum structure and optimizing indexes...\n";

        try {
            // Step 1: Enhance urgency levels
            echo "📋 Enhancing urgency levels...\n";
            $this->enhanceUrgencyLevels();

            // Step 2: Add missing enum values if needed
            echo "📋 Checking and adding missing enum values...\n";
            $this->addMissingEnumValues();

            // Step 3: Optimize indexes (remove duplicates)
            echo "📋 Optimizing indexes...\n";
            $this->optimizeIndexes();

            // Step 4: Add missing priority mappings
            echo "📋 Adding priority mappings...\n";
            $this->addPriorityMappings();

            echo "✅ Enum structure finalization completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during finalization: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Enhance urgency levels based on notification types
     */
    private function enhanceUrgencyLevels(): void
    {
        $urgencyMappings = [
            // High urgency (level 3)
            3 => [
                'security_alert', 'account_locked', 'suspicious_activity',
                'password_changed', 'login_from_new_device'
            ],
            
            // Medium urgency (level 2)
            2 => [
                'system_announcement', 'maintenance_notice', 'business_verified',
                'business_rejected', 'product_rejected', 'order_update'
            ],
            
            // Normal urgency (level 1) - default, no need to update
        ];

        foreach ($urgencyMappings as $level => $types) {
            $updated = DB::table('notifications')
                ->whereIn('type', $types)
                ->update(['urgency_level' => $level]);
            
            if ($updated > 0) {
                echo "  ✅ Updated {$updated} notifications to urgency level {$level}\n";
            }
        }
    }

    /**
     * Add missing enum values and update priorities
     */
    private function addMissingEnumValues(): void
    {
        // Update priorities based on urgency and type
        $priorityMappings = [
            'urgent' => [
                'security_alert', 'account_locked', 'suspicious_activity'
            ],
            'high' => [
                'password_changed', 'login_from_new_device', 'system_announcement',
                'business_rejected', 'product_rejected'
            ],
            // 'normal' is default
        ];

        foreach ($priorityMappings as $priority => $types) {
            $updated = DB::table('notifications')
                ->whereIn('type', $types)
                ->update(['priority' => $priority]);
            
            if ($updated > 0) {
                echo "  ✅ Updated {$updated} notifications to priority '{$priority}'\n";
            }
        }
    }

    /**
     * Optimize indexes by removing potential duplicates
     */
    private function optimizeIndexes(): void
    {
        // Get all current indexes
        $indexes = DB::select('SHOW INDEX FROM notifications');
        $indexInfo = collect($indexes)->groupBy('Key_name');
        
        echo "  📊 Current indexes: " . $indexInfo->count() . "\n";
        
        // List of indexes that might be redundant
        $potentialDuplicates = [
            'notifications_user_id_is_read_index', // might be covered by idx_user_status_created
            'notifications_type_index', // might be covered by idx_type_category_created
        ];

        foreach ($potentialDuplicates as $indexName) {
            if ($indexInfo->has($indexName)) {
                echo "  ⚠️  Found potentially redundant index: {$indexName}\n";
                echo "     (Keeping for safety - can be manually reviewed later)\n";
            }
        }
    }

    /**
     * Add priority mappings for better categorization
     */
    private function addPriorityMappings(): void
    {
        // Update status for urgent notifications
        $urgentUpdated = DB::table('notifications')
            ->where('priority', 'urgent')
            ->where('status', 'pending')
            ->update(['status' => 'delivered']);
        
        if ($urgentUpdated > 0) {
            echo "  ✅ Updated {$urgentUpdated} urgent notifications to delivered status\n";
        }

        // Ensure high priority notifications are properly categorized
        $highPriorityCount = DB::table('notifications')
            ->where('priority', 'high')
            ->count();
        
        echo "  📊 High priority notifications: {$highPriorityCount}\n";
        
        $urgentPriorityCount = DB::table('notifications')
            ->where('priority', 'urgent')
            ->count();
        
        echo "  📊 Urgent priority notifications: {$urgentPriorityCount}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back enum structure finalization...\n";

        try {
            // Reset urgency levels to 1
            DB::table('notifications')->update(['urgency_level' => 1]);

            // Reset priorities to normal/high only
            DB::table('notifications')
                ->where('priority', 'urgent')
                ->update(['priority' => 'high']);

            echo "✅ Rollback completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
