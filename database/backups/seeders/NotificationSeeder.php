<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use App\Models\Alert;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Seed notifications for admin users
     */
    public function run(): void
    {
        $this->command->info('🔔 Bắt đầu seed notifications...');

        // Get admin, moderator and some frontend users
        $adminUsers = User::whereIn('role', ['admin', 'moderator'])->get();
        $frontendUsers = User::whereIn('role', ['member', 'supplier', 'manufacturer', 'guest'])->limit(5)->get();
        $allUsers = $adminUsers->merge($frontendUsers);

        if ($allUsers->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed notifications!');
            return;
        }

        // Clear existing notifications
        DB::table('notifications')->truncate();

        $notifications = [];

        foreach ($allUsers as $user) {
            $userNotifications = $this->getNotificationsForUser($user);

            foreach ($userNotifications as $notificationData) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'type' => $notificationData['type'],
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'data' => json_encode($notificationData['data']),
                    'priority' => $notificationData['priority'],
                    'is_read' => $notificationData['is_read'],
                    'read_at' => $notificationData['read_at'],
                    'created_at' => $notificationData['created_at'],
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert
        $chunks = array_chunk($notifications, 100);
        foreach ($chunks as $chunk) {
            DB::table('notifications')->insert($chunk);
        }

        $this->command->info("✅ Tạo " . count($notifications) . " notifications cho " . $allUsers->count() . " users");
    }

    private function getNotificationsForUser(User $user): array
    {
        $notifications = [];

        // System notifications (for all admin users)
        $systemNotifications = [
            [
                'type' => 'system_announcement',
                'title' => 'Hệ thống MechaMap đã được cập nhật',
                'message' => 'Phiên bản mới với nhiều tính năng cải tiến đã được triển khai thành công.',
                'data' => ['action_url' => route('admin.dashboard')],
                'priority' => 'high',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'days_ago' => 0
            ],
            [
                'type' => 'user_registered',
                'title' => 'Người dùng mới đăng ký',
                'message' => 'Có 3 người dùng mới đăng ký tài khoản trong 24h qua.',
                'data' => ['action_url' => route('admin.users.index')],
                'priority' => 'normal',
                'is_read' => rand(0, 100) < 70,
                'read_at' => rand(0, 100) < 70 ? now()->subHours(rand(1, 12)) : null,
                'created_at' => now()->subHours(rand(6, 24)),
                'days_ago' => 1
            ],
            [
                'type' => 'forum_activity',
                'title' => 'Hoạt động diễn đàn tăng cao',
                'message' => 'Có 15 bài đăng mới và 45 bình luận trong ngày hôm nay.',
                'data' => ['action_url' => route('admin.threads.index')],
                'priority' => 'normal',
                'is_read' => rand(0, 100) < 60,
                'read_at' => rand(0, 100) < 60 ? now()->subHours(rand(1, 8)) : null,
                'created_at' => now()->subHours(rand(3, 12)),
                'days_ago' => 0
            ]
        ];

        // Role-specific notifications
        if ($user->role === 'admin') {
            $adminNotifications = [
                [
                    'type' => 'business_verified',
                    'title' => 'Doanh nghiệp được xác minh',
                    'message' => 'Công ty TNHH Cơ Khí Việt Nam đã hoàn tất quy trình xác minh.',
                    'data' => ['action_url' => route('admin.users.index')],
                    'priority' => 'normal',
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now()->subMinutes(30),
                    'days_ago' => 0
                ],
                [
                    'type' => 'marketplace_activity',
                    'title' => 'Đơn hàng mới trên Marketplace',
                    'message' => 'Có 5 đơn hàng mới cần xử lý trong hệ thống marketplace.',
                    'data' => ['action_url' => '#'],
                    'priority' => 'normal',
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now()->subHours(1),
                    'days_ago' => 0
                ],
                [
                    'type' => 'commission_paid',
                    'title' => 'Hoa hồng đã được thanh toán',
                    'message' => 'Đã thanh toán 2,500,000₫ hoa hồng cho 12 sellers trong tháng này.',
                    'data' => ['action_url' => '#'],
                    'priority' => 'normal',
                    'is_read' => true,
                    'read_at' => now()->subHours(2),
                    'created_at' => now()->subHours(3),
                    'days_ago' => 0
                ]
            ];
            $notifications = array_merge($notifications, $adminNotifications);
        }

        if ($user->role === 'moderator') {
            $moderatorNotifications = [
                [
                    'type' => 'forum_activity',
                    'title' => 'Bài đăng cần kiểm duyệt',
                    'message' => 'Có 8 bài đăng mới cần được kiểm duyệt trong diễn đàn CAD/CAM.',
                    'data' => ['action_url' => route('admin.threads.index')],
                    'priority' => 'normal',
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now()->subMinutes(45),
                    'days_ago' => 0
                ],
                [
                    'type' => 'user_registered',
                    'title' => 'Báo cáo vi phạm',
                    'message' => 'Có 2 báo cáo vi phạm mới cần xem xét và xử lý.',
                    'data' => ['action_url' => '#'],
                    'priority' => 'high',
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now()->subHours(2),
                    'days_ago' => 0
                ]
            ];
            $notifications = array_merge($notifications, $moderatorNotifications);
        }

        // Frontend user notifications
        if (in_array($user->role, ['member', 'supplier', 'manufacturer', 'guest'])) {
            $frontendNotifications = [
                [
                    'type' => 'forum_activity',
                    'title' => 'Bình luận mới trong thread của bạn',
                    'message' => 'Có 2 bình luận mới trong thread "Hướng dẫn thiết kế CAD cơ bản".',
                    'data' => ['action_url' => '/threads/123'],
                    'priority' => 'normal',
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now()->subMinutes(15),
                    'days_ago' => 0
                ],
                [
                    'type' => 'marketplace_activity',
                    'title' => 'Sản phẩm được phê duyệt',
                    'message' => 'Sản phẩm "Thiết kế CAD Engine V1.0" đã được phê duyệt và đăng lên marketplace.',
                    'data' => ['action_url' => '/marketplace/products/1'],
                    'priority' => 'normal',
                    'is_read' => rand(0, 100) < 50,
                    'read_at' => rand(0, 100) < 50 ? now()->subHours(rand(1, 6)) : null,
                    'created_at' => now()->subHours(rand(1, 12)),
                    'days_ago' => 0
                ]
            ];

            if ($user->role === 'supplier' || $user->role === 'manufacturer') {
                $businessNotifications = [
                    [
                        'type' => 'business_verified',
                        'title' => 'Tài khoản doanh nghiệp đã xác thực',
                        'message' => 'Chúc mừng! Tài khoản doanh nghiệp của bạn đã được xác thực thành công.',
                        'data' => ['action_url' => '/business/dashboard'],
                        'priority' => 'high',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now()->subHours(2),
                        'days_ago' => 0
                    ],
                    [
                        'type' => 'order_update',
                        'title' => 'Đơn hàng mới',
                        'message' => 'Bạn có đơn hàng mới #12345 cần xử lý.',
                        'data' => ['action_url' => '/orders/12345'],
                        'priority' => 'normal',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now()->subMinutes(30),
                        'days_ago' => 0
                    ]
                ];
                $frontendNotifications = array_merge($frontendNotifications, $businessNotifications);
            }

            $notifications = array_merge($notifications, $frontendNotifications);
        }

        // Add system notifications for all users
        $notifications = array_merge($notifications, $systemNotifications);

        // Add some older notifications
        $olderNotifications = [
            [
                'type' => 'system_announcement',
                'title' => 'Bảo trì hệ thống hoàn tất',
                'message' => 'Việc bảo trì hệ thống đã hoàn tất. Tất cả tính năng đã hoạt động bình thường.',
                'data' => [],
                'priority' => 'normal',
                'is_read' => true,
                'read_at' => now()->subDays(2),
                'created_at' => now()->subDays(3),
                'days_ago' => 3
            ],
            [
                'type' => 'forum_activity',
                'title' => 'Thống kê tuần qua',
                'message' => 'Tuần qua có 150 bài đăng mới, 420 bình luận và 25 thành viên mới.',
                'data' => ['action_url' => route('admin.statistics.index')],
                'priority' => 'normal',
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(7),
                'days_ago' => 7
            ]
        ];

        $notifications = array_merge($notifications, $olderNotifications);

        return $notifications;
    }
}
