<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Populate notification auxiliary tables with default data
     */
    public function up(): void
    {
        echo "🚀 Populating notification auxiliary tables...\n";

        try {
            // Step 1: Populate notification templates
            echo "📋 Populating notification templates...\n";
            $this->populateNotificationTemplates();

            // Step 2: Create default notification preferences for existing users
            echo "📋 Creating default notification preferences...\n";
            $this->createDefaultNotificationPreferences();

            // Step 3: Optimize auxiliary table indexes
            echo "📋 Optimizing auxiliary table indexes...\n";
            $this->optimizeAuxiliaryIndexes();

            echo "✅ Notification auxiliary tables populated successfully!\n";

        } catch (\Exception $e) {
            echo "❌ Error populating auxiliary tables: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Populate notification templates
     */
    private function populateNotificationTemplates(): void
    {
        $templates = [
            // Forum notifications
            [
                'type' => 'forum_activity',
                'name' => 'Forum Activity',
                'description' => 'General forum activity notification',
                'channels' => json_encode(['database']),
                'database_template' => json_encode([
                    'title' => 'Hoạt động diễn đàn mới',
                    'message' => 'Có hoạt động mới trong diễn đàn: {activity_description}',
                    'variables' => ['activity_description', 'forum_name', 'user_name']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Hoạt động diễn đàn mới - MechaMap',
                    'body' => 'Xin chào {user_name},\n\nCó hoạt động mới trong diễn đàn: {activity_description}\n\nTruy cập: {action_url}',
                    'variables' => ['user_name', 'activity_description', 'action_url']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'thread_created',
                'name' => 'New Thread Created',
                'description' => 'Notification when a new thread is created',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Chủ đề mới được tạo',
                    'message' => '{author_name} đã tạo chủ đề mới: {thread_title}',
                    'variables' => ['author_name', 'thread_title', 'forum_name']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Chủ đề mới: {thread_title}',
                    'body' => 'Xin chào,\n\n{author_name} đã tạo chủ đề mới "{thread_title}" trong diễn đàn {forum_name}.\n\nXem chi tiết: {action_url}',
                    'variables' => ['author_name', 'thread_title', 'forum_name', 'action_url']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'thread_replied',
                'name' => 'Thread Reply',
                'description' => 'Notification when someone replies to a thread',
                'channels' => json_encode(['database']),
                'database_template' => json_encode([
                    'title' => 'Phản hồi mới',
                    'message' => '{replier_name} đã phản hồi chủ đề: {thread_title}',
                    'variables' => ['replier_name', 'thread_title', 'reply_content']
                ]),
                'is_active' => true
            ],

            // Social notifications
            [
                'type' => 'user_followed',
                'name' => 'User Followed',
                'description' => 'Notification when someone follows you',
                'channels' => json_encode(['database']),
                'database_template' => json_encode([
                    'title' => 'Người theo dõi mới',
                    'message' => '{follower_name} đã bắt đầu theo dõi bạn',
                    'variables' => ['follower_name', 'follower_profile_url']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'user_registered',
                'name' => 'User Registered',
                'description' => 'Welcome notification for new users',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Chào mừng đến với MechaMap!',
                    'message' => 'Chào mừng {user_name} đã tham gia cộng đồng MechaMap',
                    'variables' => ['user_name']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Chào mừng đến với MechaMap!',
                    'body' => 'Xin chào {user_name},\n\nChào mừng bạn đến với cộng đồng kỹ sư cơ khí MechaMap!\n\nKhám phá ngay: {platform_url}',
                    'variables' => ['user_name', 'platform_url']
                ]),
                'is_active' => true
            ],

            // Marketplace notifications
            [
                'type' => 'marketplace_activity',
                'name' => 'Marketplace Activity',
                'description' => 'General marketplace activity notification',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Hoạt động marketplace',
                    'message' => 'Có hoạt động mới trong marketplace: {activity_description}',
                    'variables' => ['activity_description', 'product_name', 'seller_name']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'business_verified',
                'name' => 'Business Verified',
                'description' => 'Notification when business is verified',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Doanh nghiệp được xác minh',
                    'message' => 'Doanh nghiệp {business_name} đã được xác minh thành công',
                    'variables' => ['business_name', 'verification_date']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Doanh nghiệp được xác minh - MechaMap',
                    'body' => 'Chúc mừng!\n\nDoanh nghiệp {business_name} của bạn đã được xác minh thành công vào {verification_date}.\n\nBạn có thể bắt đầu sử dụng các tính năng dành cho doanh nghiệp.',
                    'variables' => ['business_name', 'verification_date']
                ]),
                'is_active' => true
            ],

            // System notifications
            [
                'type' => 'system_announcement',
                'name' => 'System Announcement',
                'description' => 'Important system announcements',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Thông báo hệ thống',
                    'message' => '{announcement_title}: {announcement_content}',
                    'variables' => ['announcement_title', 'announcement_content']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Thông báo hệ thống - {announcement_title}',
                    'body' => 'Thông báo quan trọng từ MechaMap:\n\n{announcement_content}\n\nTrân trọng,\nĐội ngũ MechaMap',
                    'variables' => ['announcement_title', 'announcement_content']
                ]),
                'is_active' => true
            ],

            // Security notifications
            [
                'type' => 'security_alert',
                'name' => 'Security Alert',
                'description' => 'Security-related notifications',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Cảnh báo bảo mật',
                    'message' => 'Phát hiện hoạt động bảo mật: {alert_description}',
                    'variables' => ['alert_description', 'ip_address', 'timestamp']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Cảnh báo bảo mật - MechaMap',
                    'body' => 'CẢNH BÁO BẢO MẬT\n\n{alert_description}\n\nIP: {ip_address}\nThời gian: {timestamp}\n\nNếu không phải bạn, vui lòng đổi mật khẩu ngay.',
                    'variables' => ['alert_description', 'ip_address', 'timestamp']
                ]),
                'is_active' => true
            ],

            // Messaging notifications
            [
                'type' => 'message_received',
                'name' => 'Message Received',
                'description' => 'Notification when receiving a new message',
                'channels' => json_encode(['database']),
                'database_template' => json_encode([
                    'title' => 'Tin nhắn mới',
                    'message' => 'Bạn có tin nhắn mới từ {sender_name}: {message_preview}',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url']
                ]),
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['type' => $template['type']],
                array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $count = count($templates);
        echo "  ✅ Created/updated {$count} notification templates\n";
    }

    /**
     * Create default notification preferences for existing users
     */
    private function createDefaultNotificationPreferences(): void
    {
        $users = DB::table('users')->select('id')->get();
        $defaultPreferences = [
            // Forum preferences
            ['channel' => 'database', 'type' => 'forum_activity', 'enabled' => true],
            ['channel' => 'database', 'type' => 'thread_created', 'enabled' => true],
            ['channel' => 'database', 'type' => 'thread_replied', 'enabled' => true],
            ['channel' => 'email', 'type' => 'thread_created', 'enabled' => false], // Email disabled by default

            // Social preferences
            ['channel' => 'database', 'type' => 'user_followed', 'enabled' => true],
            ['channel' => 'database', 'type' => 'user_registered', 'enabled' => true],

            // Marketplace preferences
            ['channel' => 'database', 'type' => 'marketplace_activity', 'enabled' => true],
            ['channel' => 'database', 'type' => 'business_verified', 'enabled' => true],
            ['channel' => 'email', 'type' => 'business_verified', 'enabled' => true],

            // System preferences
            ['channel' => 'database', 'type' => 'system_announcement', 'enabled' => true],
            ['channel' => 'email', 'type' => 'system_announcement', 'enabled' => true],

            // Security preferences (always enabled)
            ['channel' => 'database', 'type' => 'security_alert', 'enabled' => true],
            ['channel' => 'email', 'type' => 'security_alert', 'enabled' => true],

            // Messaging preferences
            ['channel' => 'database', 'type' => 'message_received', 'enabled' => true],
        ];

        $totalCreated = 0;
        foreach ($users as $user) {
            foreach ($defaultPreferences as $pref) {
                $exists = DB::table('notification_preferences')
                    ->where('user_id', $user->id)
                    ->where('channel', $pref['channel'])
                    ->where('type', $pref['type'])
                    ->exists();

                if (!$exists) {
                    DB::table('notification_preferences')->insert([
                        'user_id' => $user->id,
                        'channel' => $pref['channel'],
                        'type' => $pref['type'],
                        'enabled' => $pref['enabled'],
                        'settings' => json_encode([]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $totalCreated++;
                }
            }
        }

        echo "  ✅ Created {$totalCreated} default notification preferences for " . count($users) . " users\n";
    }

    /**
     * Optimize auxiliary table indexes
     */
    private function optimizeAuxiliaryIndexes(): void
    {
        // Add indexes for notification_templates
        if (!$this->indexExists('notification_templates', 'idx_type_active')) {
            Schema::table('notification_templates', function (Blueprint $table) {
                $table->index(['type', 'is_active'], 'idx_type_active');
            });
            echo "  ✅ Added index: notification_templates.idx_type_active\n";
        }

        // Add indexes for notification_preferences
        if (!$this->indexExists('notification_preferences', 'idx_user_channel_type')) {
            Schema::table('notification_preferences', function (Blueprint $table) {
                $table->index(['user_id', 'channel', 'type'], 'idx_user_channel_type');
            });
            echo "  ✅ Added index: notification_preferences.idx_user_channel_type\n";
        }

        if (!$this->indexExists('notification_preferences', 'idx_enabled_type')) {
            Schema::table('notification_preferences', function (Blueprint $table) {
                $table->index(['enabled', 'type'], 'idx_enabled_type');
            });
            echo "  ✅ Added index: notification_preferences.idx_enabled_type\n";
        }

        // Add indexes for notification_logs
        if (!$this->indexExists('notification_logs', 'idx_status_sent_at')) {
            Schema::table('notification_logs', function (Blueprint $table) {
                $table->index(['status', 'sent_at'], 'idx_status_sent_at');
            });
            echo "  ✅ Added index: notification_logs.idx_status_sent_at\n";
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
        echo "🔄 Rolling back notification auxiliary tables population...\n";

        try {
            // Clear templates
            DB::table('notification_templates')->truncate();

            // Clear preferences
            DB::table('notification_preferences')->truncate();

            // Remove indexes
            Schema::table('notification_templates', function (Blueprint $table) {
                $table->dropIndex('idx_type_active');
            });

            Schema::table('notification_preferences', function (Blueprint $table) {
                $table->dropIndex('idx_user_channel_type');
                $table->dropIndex('idx_enabled_type');
            });

            Schema::table('notification_logs', function (Blueprint $table) {
                $table->dropIndex('idx_status_sent_at');
            });

            echo "✅ Rollback completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
