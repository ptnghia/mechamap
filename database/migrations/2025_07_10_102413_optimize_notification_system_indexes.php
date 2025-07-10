<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Optimize notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            // Add composite index for common queries
            $table->index(['user_id', 'type', 'created_at'], 'notifications_user_type_created_idx');
            $table->index(['user_id', 'is_read', 'created_at'], 'notifications_user_read_created_idx');
            $table->index(['type', 'is_read', 'created_at'], 'notifications_type_read_created_idx');

            // Add index for priority queries (if priority column exists)
            if (Schema::hasColumn('notifications', 'priority')) {
                $table->index(['user_id', 'priority', 'created_at'], 'notifications_user_priority_created_idx');
            }
        });

        // Optimize user_devices table indexes
        Schema::table('user_devices', function (Blueprint $table) {
            // Add composite indexes for device queries
            $table->index(['user_id', 'is_trusted', 'last_seen_at'], 'user_devices_user_trusted_seen_idx');
            $table->index(['device_fingerprint', 'user_id'], 'user_devices_fingerprint_user_idx');
            $table->index(['ip_address', 'created_at'], 'user_devices_ip_created_idx');
        });

        // Optimize threads table for notification queries
        Schema::table('threads', function (Blueprint $table) {
            // Add index for forum notification queries
            $table->index(['forum_id', 'created_at'], 'threads_forum_created_idx');
            $table->index(['user_id', 'forum_id', 'created_at'], 'threads_user_forum_created_idx');
        });

        // Optimize comments table for notification queries
        Schema::table('comments', function (Blueprint $table) {
            // Add index for thread reply notifications
            $table->index(['thread_id', 'created_at'], 'comments_thread_created_idx');
            $table->index(['user_id', 'thread_id', 'created_at'], 'comments_user_thread_created_idx');
        });

        // Optimize thread_follows table if it exists
        if (Schema::hasTable('thread_follows')) {
            Schema::table('thread_follows', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'thread_follows_user_created_idx');
                $table->index(['thread_id', 'created_at'], 'thread_follows_thread_created_idx');
            });
        }

        // Add indexes to users table for notification queries
        Schema::table('users', function (Blueprint $table) {
            // Add index for active users
            $table->index(['is_active', 'last_seen_at'], 'users_active_seen_idx');
            $table->index(['email_verified_at', 'is_active'], 'users_verified_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_type_created_idx');
            $table->dropIndex('notifications_user_read_created_idx');
            $table->dropIndex('notifications_type_read_created_idx');

            if (Schema::hasColumn('notifications', 'priority')) {
                $table->dropIndex('notifications_user_priority_created_idx');
            }
        });

        // Drop user_devices table indexes
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropIndex('user_devices_user_trusted_seen_idx');
            $table->dropIndex('user_devices_fingerprint_user_idx');
            $table->dropIndex('user_devices_ip_created_idx');
        });

        // Drop threads table indexes
        Schema::table('threads', function (Blueprint $table) {
            $table->dropIndex('threads_forum_created_idx');
            $table->dropIndex('threads_user_forum_created_idx');
        });

        // Drop comments table indexes
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_thread_created_idx');
            $table->dropIndex('comments_user_thread_created_idx');
        });

        // Drop thread_follows table indexes if it exists
        if (Schema::hasTable('thread_follows')) {
            Schema::table('thread_follows', function (Blueprint $table) {
                $table->dropIndex('thread_follows_user_created_idx');
                $table->dropIndex('thread_follows_thread_created_idx');
            });
        }

        // Drop users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_active_seen_idx');
            $table->dropIndex('users_verified_active_idx');
        });
    }
};
