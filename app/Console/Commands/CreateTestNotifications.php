<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationCategoryService;
use Carbon\Carbon;

class CreateTestNotifications extends Command
{
    protected $signature = 'notifications:create-test {user_id : User ID to create notifications for}';
    protected $description = 'Create test notifications of all types for a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Creating test notifications for user: {$user->name} (ID: {$userId})");

        // Lấy tất cả notification types từ service
        $allTypes = $this->getAllNotificationTypes();
        
        // Xóa notifications cũ (tùy chọn)
        if ($this->confirm('Do you want to delete existing notifications for this user?')) {
            $user->userNotifications()->delete();
            $this->info('Existing notifications deleted.');
        }

        $created = 0;
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $statuses = ['delivered', 'read'];

        foreach ($allTypes as $category => $types) {
            foreach ($types as $type) {
                $notification = $this->createNotificationForType($user, $type, $category, $priorities, $statuses);
                if ($notification) {
                    $created++;
                    $this->line("✓ Created: {$type}");
                }
            }
        }

        $this->info("Successfully created {$created} test notifications for user {$user->name}");
        return 0;
    }

    private function getAllNotificationTypes(): array
    {
        return [
            'messages' => [
                'message_received',
                'seller_message',
            ],
            'forum' => [
                'thread_created',
                'thread_replied', 
                'comment_mention',
                'forum_activity',
            ],
            'marketplace' => [
                'product_approved',
                'order_update',
                'order_status_changed',
                'price_drop_alert',
                'product_out_of_stock',
                'review_received',
                'wishlist_available',
                'marketplace_activity',
                'commission_paid',
            ],
            'security' => [
                'login_from_new_device',
                'password_changed',
                'security_alert',
            ],
            'social' => [
                'user_followed',
                'user_registered',
                'achievement_unlocked',
                'business_verified',
            ],
            'system' => [
                'system_announcement',
            ],
        ];
    }

    private function createNotificationForType($user, $type, $category, $priorities, $statuses)
    {
        $priority = $priorities[array_rand($priorities)];
        $status = $statuses[array_rand($statuses)];
        $isRead = $status === 'read';

        // Tạo data và message phù hợp với từng loại
        $notificationData = $this->getNotificationData($type);

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'category' => $category,
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'data' => json_encode($notificationData['data']),
            'priority' => $priority,
            'urgency_level' => $this->getUrgencyLevel($priority),
            'status' => $status,
            'is_read' => $isRead,
            'read_at' => $isRead ? now() : null,
            'action_url' => $notificationData['action_url'] ?? null,
            'requires_action' => $notificationData['requires_action'] ?? false,
            'created_at' => Carbon::now()->subMinutes(rand(1, 1440)), // Random time trong 24h qua
        ]);
    }

    private function getNotificationData($type): array
    {
        $baseData = [
            'title' => "notifications.types.{$type}",
            'message' => "Test notification for {$type}",
            'data' => ['test' => true, 'type' => $type],
            'action_url' => null,
            'requires_action' => false,
        ];

        // Customize data cho từng loại notification
        switch ($type) {
            case 'message_received':
                return array_merge($baseData, [
                    'message' => 'You have a new message from Test User',
                    'data' => [
                        'sender_id' => 1,
                        'sender_name' => 'Test User',
                        'message_preview' => 'This is a test message for notification system',
                        'conversation_id' => 1,
                    ],
                    'action_url' => '/conversations/1',
                ]);

            case 'thread_created':
                return array_merge($baseData, [
                    'message' => 'New thread created in your followed forum',
                    'data' => [
                        'thread_id' => 1,
                        'thread_title' => 'Test Thread for Notifications',
                        'forum_name' => 'General Discussion',
                        'author_name' => 'Test Author',
                    ],
                    'action_url' => '/threads/1',
                ]);

            case 'product_approved':
                return array_merge($baseData, [
                    'message' => 'Your product has been approved',
                    'data' => [
                        'product_id' => 1,
                        'product_name' => 'Test Product',
                        'approved_by' => 'Admin',
                    ],
                    'action_url' => '/marketplace/products/1',
                    'requires_action' => true,
                ]);

            case 'login_from_new_device':
                return array_merge($baseData, [
                    'message' => 'New login detected from unknown device',
                    'data' => [
                        'device' => 'Chrome on Windows',
                        'ip_address' => '192.168.1.100',
                        'location' => 'Ho Chi Minh City, Vietnam',
                    ],
                    'requires_action' => true,
                ]);

            case 'user_followed':
                return array_merge($baseData, [
                    'message' => 'Test User started following you',
                    'data' => [
                        'follower_id' => 2,
                        'follower_name' => 'Test Follower',
                        'follower_avatar' => '/images/avatars/default.jpg',
                    ],
                    'action_url' => '/users/2',
                ]);

            case 'system_announcement':
                return array_merge($baseData, [
                    'message' => 'Important system announcement',
                    'data' => [
                        'announcement_id' => 1,
                        'title' => 'System Maintenance Notice',
                        'content' => 'Scheduled maintenance on Sunday 2AM-4AM',
                    ],
                    'requires_action' => false,
                ]);

            default:
                return array_merge($baseData, [
                    'message' => "Test notification for {$type} type",
                    'data' => [
                        'type' => $type,
                        'test_data' => 'Sample data for ' . $type,
                        'timestamp' => now()->toISOString(),
                    ],
                ]);
        }
    }

    private function getUrgencyLevel($priority): int
    {
        return match($priority) {
            'urgent' => 4,
            'high' => 3,
            'normal' => 2,
            'low' => 1,
            default => 2,
        };
    }
}
