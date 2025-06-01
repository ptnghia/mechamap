<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() === 0) {
            return;
        }

        // Tạo alerts cho users về hoạt động diễn đàn
        $alertTypes = [
            'thread_reply' => 'Có phản hồi mới trong thread của bạn',
            'comment_reply' => 'Có người trả lời comment của bạn',
            'showcase_comment' => 'Có comment mới trong showcase của bạn',
            'showcase_like' => 'Có người thích showcase của bạn',
            'thread_mention' => 'Bạn được mention trong một thread',
            'new_follower' => 'Có người theo dõi bạn',
            'system_update' => 'Cập nhật hệ thống mới',
            'maintenance_notice' => 'Thông báo bảo trì hệ thống',
        ];

        $mechanicalMessages = [
            'Phản hồi của bạn về hệ thống PLC Siemens rất hữu ích!',
            'Thread về robot ABB của bạn đã nhận được 15 likes mới.',
            'Có người hỏi về tối ưu CNC trong thread của bạn.',
            'Showcase thiết kế conveyor system được nhiều người quan tâm.',
            'Câu trả lời về troubleshooting hydraulic được mark là solution.',
            'Bài viết về Industry 4.0 của bạn đang trending.',
            'Có expert muốn kết nối với bạn về automation project.',
            'Thread về safety standard ISO 13849 cần thêm input.',
            'Design CAD mới của bạn được admin feature.',
            'Hướng dẫn programming robot nhận được feedback tích cực.',
        ];

        foreach ($users->take(15) as $user) {
            $numAlerts = rand(3, 8);

            for ($i = 0; $i < $numAlerts; $i++) {
                $alertType = array_rand($alertTypes);
                $isRead = rand(0, 100) < 60; // 60% đã đọc

                Alert::create([
                    'user_id' => $user->id,
                    'type' => $alertType,
                    'title' => $alertTypes[$alertType],
                    'content' => $mechanicalMessages[array_rand($mechanicalMessages)],
                    'alertable_type' => 'App\\Models\\Thread',
                    'alertable_id' => rand(1, 50),
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'read_at' => $isRead ? now()->subDays(rand(0, 15))->subHours(rand(0, 23)) : null,
                ]);
            }
        }

        // Tạo system alerts cho tất cả users
        $systemAlerts = [
            [
                'title' => 'Cập Nhật Tính Năng Mới',
                'message' => 'MechaMap vừa ra mắt tính năng CAD Viewer tích hợp, hỗ trợ xem file STEP, IGES trực tiếp trên browser.',
                'type' => 'system_update',
            ],
            [
                'title' => 'Bảo Trì Hệ Thống',
                'message' => 'Hệ thống sẽ bảo trì từ 2:00-4:00 AM ngày mai để nâng cấp database và cải thiện performance.',
                'type' => 'maintenance_notice',
            ],
            [
                'title' => 'Cuộc Thi Thiết Kế Automation',
                'message' => 'Đăng ký tham gia cuộc thi thiết kế hệ thống automation với giải thưởng 50 triệu VND.',
                'type' => 'contest_announcement',
            ],
        ];

        foreach ($systemAlerts as $alert) {
            foreach ($users->take(20) as $user) {
                Alert::create([
                    'user_id' => $user->id,
                    'type' => $alert['type'],
                    'title' => $alert['title'],
                    'content' => $alert['message'],
                    'alertable_type' => null,
                    'alertable_id' => null,
                    'created_at' => now()->subDays(rand(1, 7)),
                    'read_at' => null,
                ]);
            }
        }
    }
}
