<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class MigrateNotificationsToUnified extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:migrate-unified
                            {--dry-run : Run without making changes}
                            {--batch-size=1000 : Number of records to process at once}
                            {--force : Force migration without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate alerts to unified custom_notifications table (Laravel notifications kept separate for admin access)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        $this->info('🚀 Starting Unified Notifications Migration');
        $this->info('=====================================');
        $this->warn('📋 Laravel notifications will be kept separate for admin access');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        // Check current state
        $this->checkCurrentState();

        if (!$force && !$this->confirm('Do you want to proceed with the migration? (Laravel notifications will remain separate)')) {
            $this->info('Migration cancelled.');
            return 0;
        }

        try {
            DB::beginTransaction();

            // Step 1: Migrate alerts table
            $alertsMigrated = $this->migrateAlerts($dryRun, $batchSize);

            // Step 2: Skip Laravel notifications migration (keep separate)
            $notificationsMigrated = 0;
            $this->info('📋 Skipping Laravel notifications migration - keeping separate for admin access');

            // Step 3: Update existing custom_notifications with new fields
            $customUpdated = $this->updateExistingCustomNotifications($dryRun, $batchSize);

            if (!$dryRun) {
                DB::commit();
                $this->info('✅ Migration completed successfully!');
            } else {
                DB::rollBack();
                $this->info('✅ Dry run completed successfully!');
            }

            $this->displaySummary($alertsMigrated, $notificationsMigrated, $customUpdated);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Migration failed: ' . $e->getMessage());
            Log::error('Notification migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Check current state of notification tables
     */
    private function checkCurrentState(): void
    {
        $alertsCount = DB::table('alerts')->count();
        $laravelNotificationsCount = DB::table('notifications')->count();
        $customNotificationsCount = DB::table('custom_notifications')->count();

        $this->info("📊 Current State:");
        $this->line("   • Alerts: {$alertsCount} records");
        $this->line("   • Laravel Notifications: {$laravelNotificationsCount} records");
        $this->line("   • Custom Notifications: {$customNotificationsCount} records");
        $this->newLine();
    }

    /**
     * Migrate alerts table to custom_notifications
     */
    private function migrateAlerts(bool $dryRun, int $batchSize): int
    {
        $this->info('📋 Migrating alerts table...');

        $totalAlerts = DB::table('alerts')->count();
        if ($totalAlerts === 0) {
            $this->line('   No alerts to migrate.');
            return 0;
        }

        $migrated = 0;
        $bar = $this->output->createProgressBar($totalAlerts);

        DB::table('alerts')->orderBy('id')->chunk($batchSize, function ($alerts) use (&$migrated, $dryRun, $bar) {
            $insertData = [];

            foreach ($alerts as $alert) {
                $insertData[] = [
                    'user_id' => $alert->user_id,
                    'type' => $this->mapAlertTypeToNotificationType($alert->type),
                    'category' => $this->mapAlertTypeToCategory($alert->type),
                    'title' => $alert->title,
                    'message' => $alert->content,
                    'data' => json_encode([
                        'original_alert_id' => $alert->id,
                        'original_type' => $alert->type,
                        'migrated_from' => 'alerts'
                    ]),
                    'priority' => $this->mapAlertTypeToPriority($alert->type),
                    'status' => $alert->read_at ? 'read' : 'sent',
                    'is_read' => $alert->read_at ? true : false,
                    'read_at' => $alert->read_at,
                    'notifiable_type' => $alert->alertable_type,
                    'notifiable_id' => $alert->alertable_id,
                    'created_at' => $alert->created_at,
                    'updated_at' => $alert->updated_at,
                ];
                $migrated++;
                $bar->advance();
            }

            if (!$dryRun && !empty($insertData)) {
                DB::table('custom_notifications')->insert($insertData);
            }
        });

        $bar->finish();
        $this->newLine();
        $this->line("   ✅ Migrated {$migrated} alerts");

        return $migrated;
    }

    /**
     * Migrate Laravel notifications table to custom_notifications
     */
    private function migrateLaravelNotifications(bool $dryRun, int $batchSize): int
    {
        $this->info('📋 Migrating Laravel notifications table...');

        $totalNotifications = DB::table('notifications')->count();
        if ($totalNotifications === 0) {
            $this->line('   No Laravel notifications to migrate.');
            return 0;
        }

        $migrated = 0;
        $bar = $this->output->createProgressBar($totalNotifications);

        DB::table('notifications')->orderBy('created_at')->chunk($batchSize, function ($notifications) use (&$migrated, $dryRun, $bar) {
            $insertData = [];

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true) ?? [];

                $insertData[] = [
                    'user_id' => $notification->notifiable_id,
                    'type' => $this->mapLaravelNotificationTypeToCustomType($notification->type),
                    'category' => $this->mapLaravelNotificationTypeToCategory($notification->type),
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? $data['body'] ?? 'You have a new notification',
                    'data' => json_encode(array_merge($data, [
                        'original_notification_id' => $notification->id,
                        'original_type' => $notification->type,
                        'migrated_from' => 'notifications'
                    ])),
                    'priority' => $data['priority'] ?? 'normal',
                    'status' => $notification->read_at ? 'read' : 'sent',
                    'is_read' => $notification->read_at ? true : false,
                    'read_at' => $notification->read_at,
                    'notifiable_type' => $notification->notifiable_type,
                    'notifiable_id' => $notification->notifiable_id,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                ];
                $migrated++;
                $bar->advance();
            }

            if (!$dryRun && !empty($insertData)) {
                DB::table('custom_notifications')->insert($insertData);
            }
        });

        $bar->finish();
        $this->newLine();
        $this->line("   ✅ Migrated {$migrated} Laravel notifications");

        return $migrated;
    }

    /**
     * Update existing custom_notifications with new fields
     */
    private function updateExistingCustomNotifications(bool $dryRun, int $batchSize): int
    {
        $this->info('📋 Updating existing custom notifications...');

        $totalCustom = DB::table('custom_notifications')
            ->whereNull('category')
            ->count();

        if ($totalCustom === 0) {
            $this->line('   No custom notifications to update.');
            return 0;
        }

        $updated = 0;
        $bar = $this->output->createProgressBar($totalCustom);

        DB::table('custom_notifications')
            ->whereNull('category')
            ->orderBy('id')
            ->chunk($batchSize, function ($notifications) use (&$updated, $dryRun, $bar) {
                foreach ($notifications as $notification) {
                    if (!$dryRun) {
                        DB::table('custom_notifications')
                            ->where('id', $notification->id)
                            ->update([
                                'category' => $this->mapNotificationTypeToCategory($notification->type),
                                'status' => $notification->is_read ? 'read' : 'sent',
                                'urgency_level' => $this->mapPriorityToUrgencyLevel($notification->priority),
                                'delivery_channels' => json_encode(['database', 'websocket']),
                                'updated_at' => now(),
                            ]);
                    }
                    $updated++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->line("   ✅ Updated {$updated} custom notifications");

        return $updated;
    }

    /**
     * Map alert type to notification type
     */
    private function mapAlertTypeToNotificationType(string $alertType): string
    {
        return match($alertType) {
            'info' => 'system_announcement',
            'success' => 'system_announcement',
            'warning' => 'security_alert',
            'error' => 'security_alert',
            'system_update' => 'system_announcement',
            'comment' => 'thread_replied',
            'thread' => 'thread_created',
            'message' => 'message_received',
            default => 'system_announcement'
        };
    }

    /**
     * Map alert type to category
     */
    private function mapAlertTypeToCategory(string $alertType): string
    {
        return match($alertType) {
            'comment', 'thread' => 'forum',
            'message' => 'social',
            'warning', 'error' => 'security',
            default => 'system'
        };
    }

    /**
     * Map alert type to priority
     */
    private function mapAlertTypeToPriority(string $alertType): string
    {
        return match($alertType) {
            'error' => 'urgent',
            'warning' => 'high',
            'success' => 'normal',
            'info' => 'normal',
            default => 'normal'
        };
    }

    /**
     * Map Laravel notification type to custom type
     */
    private function mapLaravelNotificationTypeToCustomType(string $laravelType): string
    {
        // Extract class name from full namespace
        $className = class_basename($laravelType);

        return match($className) {
            'CommentNotification' => 'thread_replied',
            'ThreadNotification' => 'thread_created',
            'MessageNotification' => 'message_received',
            'OrderNotification' => 'order_updated',
            'ProductNotification' => 'product_approved',
            'UserNotification' => 'user_followed',
            default => 'system_announcement'
        };
    }

    /**
     * Map Laravel notification type to category
     */
    private function mapLaravelNotificationTypeToCategory(string $laravelType): string
    {
        $className = class_basename($laravelType);

        return match($className) {
            'CommentNotification', 'ThreadNotification' => 'forum',
            'MessageNotification', 'UserNotification' => 'social',
            'OrderNotification', 'ProductNotification' => 'marketplace',
            default => 'system'
        };
    }

    /**
     * Map notification type to category
     */
    private function mapNotificationTypeToCategory(string $type): string
    {
        return match(true) {
            str_contains($type, 'thread_') || str_contains($type, 'comment_') => 'forum',
            str_contains($type, 'order_') || str_contains($type, 'product_') || str_contains($type, 'payment_') => 'marketplace',
            str_contains($type, 'message_') || str_contains($type, 'user_') => 'social',
            str_contains($type, 'security_') || str_contains($type, 'login_') || str_contains($type, 'password_') => 'security',
            default => 'system'
        };
    }

    /**
     * Map priority to urgency level
     */
    private function mapPriorityToUrgencyLevel(string $priority): int
    {
        return match($priority) {
            'urgent' => 5,
            'high' => 4,
            'normal' => 3,
            'low' => 2,
            default => 1
        };
    }

    /**
     * Display migration summary
     */
    private function displaySummary(int $alertsMigrated, int $notificationsMigrated, int $customUpdated): void
    {
        $this->newLine();
        $this->info('📊 Migration Summary:');
        $this->line("   • Alerts migrated: {$alertsMigrated}");
        $this->line("   • Laravel notifications migrated: {$notificationsMigrated}");
        $this->line("   • Custom notifications updated: {$customUpdated}");
        $this->line("   • Total processed: " . ($alertsMigrated + $notificationsMigrated + $customUpdated));
        $this->newLine();

        if ($alertsMigrated > 0 || $notificationsMigrated > 0) {
            $this->warn('⚠️  Remember to:');
            $this->line('   1. Test the unified notification system thoroughly');
            $this->line('   2. Update all notification-related code to use custom_notifications');
            $this->line('   3. Consider dropping old tables after verification');
            $this->line('   4. Update frontend components to use /notifications route only');
        }
    }
}
