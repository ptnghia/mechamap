<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class MigrateNotificationTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:migrate-translations
                            {--dry-run : Show what would be changed without making changes}
                            {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing notifications from hardcoded Vietnamese text to translation keys';

    /**
     * Translation mapping for common notification patterns
     */
    private array $translationMappings = [
        // Message notifications
        'Tin nhắn mới' => 'notifications.types.new_message',
        'Bạn có tin nhắn mới từ' => 'notifications.messages.new_message_from',

        // Thread notifications
        'Bài viết mới' => 'notifications.types.new_thread',
        'Có bài viết mới trong' => 'notifications.threads.new_thread_in',
        'Bài viết được cập nhật' => 'notifications.types.thread_updated',

        // Comment notifications
        'Bình luận mới' => 'notifications.types.new_comment',
        'Có bình luận mới trong bài viết' => 'notifications.comments.new_comment_in',

        // Showcase notifications
        'Showcase mới' => 'notifications.types.new_showcase',
        'Showcase được cập nhật' => 'notifications.types.showcase_updated',

        // System notifications
        'Thông báo hệ thống' => 'notifications.types.system_notification',
        'Cập nhật hệ thống' => 'notifications.types.system_update',

        // User notifications
        'Người dùng mới' => 'notifications.types.new_user',
        'Tài khoản được kích hoạt' => 'notifications.types.account_activated',

        // Marketplace notifications
        'Sản phẩm mới' => 'notifications.types.new_product',
        'Đơn hàng mới' => 'notifications.types.new_order',
        'Thanh toán thành công' => 'notifications.types.payment_success',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting notification translation migration...');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Get all notifications that need migration
        $notifications = Notification::whereNotNull('title')
            ->whereNotNull('message')
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('✅ No notifications found to migrate.');
            return 0;
        }

        $this->info("📊 Found {$notifications->count()} notifications to analyze.");

        // Analyze notifications
        $migrationPlan = $this->analyzeMigrationPlan($notifications);

        if (empty($migrationPlan)) {
            $this->info('✅ All notifications are already using translation keys or no mappings found.');
            return 0;
        }

        // Show migration plan
        $this->showMigrationPlan($migrationPlan);

        if ($dryRun) {
            $this->info('🔍 Dry run completed. Use --force to apply changes.');
            return 0;
        }

        // Confirm migration
        if (!$force && !$this->confirm('Do you want to proceed with the migration?')) {
            $this->info('❌ Migration cancelled.');
            return 0;
        }

        // Execute migration
        return $this->executeMigration($migrationPlan);
    }

    /**
     * Analyze notifications and create migration plan
     */
    private function analyzeMigrationPlan($notifications): array
    {
        $plan = [];

        foreach ($notifications as $notification) {
            $changes = [];

            // Check title
            if ($this->needsMigration($notification->title)) {
                $newTitle = $this->findTranslationKey($notification->title);
                if ($newTitle) {
                    $changes['title'] = [
                        'old' => $notification->title,
                        'new' => $newTitle
                    ];
                }
            }

            // Check message for patterns
            if ($this->needsMigration($notification->message)) {
                $newMessage = $this->migrateMessageContent($notification->message);
                if ($newMessage !== $notification->message) {
                    $changes['message'] = [
                        'old' => $notification->message,
                        'new' => $newMessage
                    ];
                }
            }

            if (!empty($changes)) {
                $plan[$notification->id] = [
                    'notification' => $notification,
                    'changes' => $changes
                ];
            }
        }

        return $plan;
    }

    /**
     * Check if content needs migration
     */
    private function needsMigration(string $content): bool
    {
        // Skip if already a translation key
        if (str_starts_with($content, 'notifications.')) {
            return false;
        }

        // Check if contains Vietnamese text patterns
        return preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/i', $content);
    }

    /**
     * Find translation key for content
     */
    private function findTranslationKey(string $content): ?string
    {
        foreach ($this->translationMappings as $pattern => $key) {
            if (str_contains($content, $pattern)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Migrate message content with dynamic data
     */
    private function migrateMessageContent(string $message): string
    {
        // Pattern: "Bạn có tin nhắn mới từ {username}: {content}"
        if (preg_match('/^Bạn có tin nhắn mới từ (.+?): (.+)$/', $message, $matches)) {
            return 'notifications.messages.new_message_from|' . json_encode([
                'username' => $matches[1],
                'preview' => substr($matches[2], 0, 100)
            ]);
        }

        // Pattern: "Có bài viết mới trong {forum}: {title}"
        if (preg_match('/^Có bài viết mới trong (.+?): (.+)$/', $message, $matches)) {
            return 'notifications.threads.new_thread_in|' . json_encode([
                'forum' => $matches[1],
                'title' => $matches[2]
            ]);
        }

        // Pattern: "Có bình luận mới trong bài viết {title}"
        if (preg_match('/^Có bình luận mới trong bài viết (.+)$/', $message, $matches)) {
            return 'notifications.comments.new_comment_in|' . json_encode([
                'title' => $matches[1]
            ]);
        }

        // If no pattern matches, return original
        return $message;
    }

    /**
     * Show migration plan to user
     */
    private function showMigrationPlan(array $plan): void
    {
        $this->info("\n📋 Migration Plan:");
        $this->info("==================");

        foreach ($plan as $id => $item) {
            $this->info("\n🔹 Notification ID: {$id}");

            foreach ($item['changes'] as $field => $change) {
                $this->line("  📝 {$field}:");
                $this->line("    ❌ Old: " . substr($change['old'], 0, 80) . (strlen($change['old']) > 80 ? '...' : ''));
                $this->line("    ✅ New: " . substr($change['new'], 0, 80) . (strlen($change['new']) > 80 ? '...' : ''));
            }
        }

        $this->info("\n📊 Summary: " . count($plan) . " notifications will be updated.");
    }

    /**
     * Execute the migration
     */
    private function executeMigration(array $plan): int
    {
        $this->info("\n🚀 Executing migration...");

        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();

        try {
            foreach ($plan as $id => $item) {
                $notification = $item['notification'];
                $changes = $item['changes'];

                $updateData = [];

                if (isset($changes['title'])) {
                    $updateData['title'] = $changes['title']['new'];
                }

                if (isset($changes['message'])) {
                    $updateData['message'] = $changes['message']['new'];
                }

                if (!empty($updateData)) {
                    $notification->update($updateData);
                    $successCount++;
                    $this->line("✅ Updated notification {$id}");
                }
            }

            DB::commit();

            $this->info("\n🎉 Migration completed successfully!");
            $this->info("✅ Updated: {$successCount} notifications");

            if ($errorCount > 0) {
                $this->warn("⚠️  Errors: {$errorCount} notifications");
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Migration failed: " . $e->getMessage());
            return 1;
        }
    }
}
