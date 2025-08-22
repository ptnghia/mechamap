<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Đổi tên bảng custom_notifications thành notifications
     */
    public function up(): void
    {
        echo "🚀 Starting notification table rename process...\n";

        try {
            // Step 1: Backup existing notifications table (Laravel standard)
            if (Schema::hasTable('notifications')) {
                $count = DB::table('notifications')->count();
                echo "📋 Backing up existing notifications table ({$count} records)...\n";
                
                // Rename to backup table
                Schema::rename('notifications', 'notifications_laravel_backup');
                echo "✅ Laravel notifications table backed up as 'notifications_laravel_backup'\n";
            }

            // Step 2: Rename custom_notifications to notifications
            if (Schema::hasTable('custom_notifications')) {
                $count = DB::table('custom_notifications')->count();
                echo "📋 Renaming custom_notifications to notifications ({$count} records)...\n";
                
                Schema::rename('custom_notifications', 'notifications');
                echo "✅ custom_notifications renamed to 'notifications'\n";
            } else {
                echo "❌ custom_notifications table not found!\n";
                throw new Exception('custom_notifications table does not exist');
            }

            // Step 3: Update model table reference
            echo "📋 Migration completed successfully!\n";
            echo "⚠️  Remember to update Notification model \$table property\n";

        } catch (\Exception $e) {
            echo "❌ Error during migration: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Khôi phục lại tên bảng ban đầu
     */
    public function down(): void
    {
        echo "🔄 Rolling back notification table rename...\n";

        try {
            // Step 1: Rename notifications back to custom_notifications
            if (Schema::hasTable('notifications')) {
                $count = DB::table('notifications')->count();
                echo "📋 Renaming notifications back to custom_notifications ({$count} records)...\n";
                
                Schema::rename('notifications', 'custom_notifications');
                echo "✅ notifications renamed back to 'custom_notifications'\n";
            }

            // Step 2: Restore Laravel notifications table
            if (Schema::hasTable('notifications_laravel_backup')) {
                $count = DB::table('notifications_laravel_backup')->count();
                echo "📋 Restoring Laravel notifications table ({$count} records)...\n";
                
                Schema::rename('notifications_laravel_backup', 'notifications');
                echo "✅ Laravel notifications table restored\n";
            }

            echo "🎉 Rollback completed successfully!\n";
            echo "⚠️  Remember to update Notification model \$table property back to 'custom_notifications'\n";

        } catch (\Exception $e) {
            echo "❌ Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
