<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AlertSeeder extends Seeder
{
    /**
     * Seed alerts với thông báo hệ thống thực tế
     * Tạo notifications cho users
     */
    public function run(): void
    {
        $this->command->info('🔔 Bắt đầu seed alerts...');

        // Lấy dữ liệu cần thiết
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed alerts!');
            return;
        }

        // Tạo alerts cho users
        $this->createAlerts($users);

        $this->command->info('✅ Hoàn thành seed alerts!');
    }

    private function createAlerts($users): void
    {
        $alerts = [];

        // Tạo system alerts cho tất cả users
        $systemAlerts = $this->getSystemAlerts();
        foreach ($systemAlerts as $alertData) {
            foreach ($users as $user) {
                $alerts[] = [
                    'user_id' => $user->id,
                    'type' => $alertData['type'],
                    'title' => $alertData['title'],
                    'content' => $alertData['message'],
                    'read_at' => rand(0, 100) < 70 ? now()->subDays(rand(0, 5)) : null, // 70% đã đọc
                    'alertable_type' => null,
                    'alertable_id' => null,
                    'created_at' => now()->subDays(rand(0, $alertData['days_ago'])),
                    'updated_at' => now(),
                ];
            }
        }

        // Tạo personal alerts cho từng user
        foreach ($users as $user) {
            $personalAlerts = $this->getPersonalAlerts($user);
            foreach ($personalAlerts as $alertData) {
                $alerts[] = [
                    'user_id' => $user->id,
                    'type' => $alertData['type'],
                    'title' => $alertData['title'],
                    'content' => $alertData['message'],
                    'read_at' => rand(0, 100) < 60 ? now()->subDays(rand(0, 3)) : null, // 60% đã đọc
                    'alertable_type' => null,
                    'alertable_id' => null,
                    'created_at' => now()->subDays(rand(0, $alertData['days_ago'])),
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert
        $chunks = array_chunk($alerts, 500);
        foreach ($chunks as $chunk) {
            DB::table('alerts')->insert($chunk);
        }

        $this->command->line("   🔔 Tạo " . count($alerts) . " alerts");
    }

    private function getSystemAlerts(): array
    {
        return [
            [
                'type' => 'system',
                'title' => 'Chào mừng đến với MechaMap!',
                'message' => 'Cảm ơn bạn đã tham gia cộng đồng kỹ sư cơ khí hàng đầu Việt Nam. Hãy khám phá các forum chuyên ngành và chia sẻ kiến thức của bạn!',
                'days_ago' => 30
            ],
            [
                'type' => 'maintenance',
                'title' => 'Bảo trì hệ thống hoàn tất',
                'message' => 'Hệ thống đã được cập nhật với nhiều tính năng mới: Showcase Projects, Advanced Search, và Performance Improvements.',
                'days_ago' => 7
            ],
            [
                'type' => 'feature',
                'title' => 'Tính năng mới: Project Showcase',
                'message' => 'Bây giờ bạn có thể showcase các dự án kỹ thuật của mình! Upload CAD files, chia sẻ calculations và nhận feedback từ cộng đồng.',
                'days_ago' => 14
            ],
            [
                'type' => 'community',
                'title' => 'MechaMap Community Guidelines',
                'message' => 'Để duy trì môi trường thảo luận chuyên nghiệp, vui lòng đọc và tuân thủ community guidelines. Focus vào technical content và respectful discussions.',
                'days_ago' => 21
            ]
        ];
    }

    private function getPersonalAlerts($user): array
    {
        $alerts = [];

        // Random personal alerts dựa vào user role
        if ($user->role === 'expert' || $user->role === 'admin') {
            $alerts[] = [
                'type' => 'expert',
                'title' => 'Yêu cầu review technical content',
                'message' => 'Có 3 threads mới cần expert review trong lĩnh vực chuyên môn của bạn. Hãy giúp verify technical accuracy.',
                'days_ago' => 2
            ];
        }

        if (rand(0, 100) < 40) { // 40% users có achievement alert
            $achievements = [
                'Đạt 100 likes cho comments',
                'Tạo thread đầu tiên',
                'Hoàn thành profile 100%',
                'Tham gia cộng đồng 1 tháng',
                'Chia sẻ showcase project đầu tiên'
            ];

            $alerts[] = [
                'type' => 'achievement',
                'title' => 'Chúc mừng! Bạn đã unlock achievement',
                'message' => 'Achievement: ' . $achievements[array_rand($achievements)] . '. Tiếp tục đóng góp để unlock thêm nhiều achievements khác!',
                'days_ago' => rand(1, 10)
            ];
        }

        if (rand(0, 100) < 30) { // 30% users có interaction alert
            $interactions = [
                'Có 5 users mới follow bạn tuần này',
                'Thread của bạn đã nhận được 20+ likes',
                'Comment của bạn được mark là solution',
                'Showcase project của bạn trending trong tuần'
            ];

            $alerts[] = [
                'type' => 'interaction',
                'title' => 'Hoạt động mới trên profile',
                'message' => $interactions[array_rand($interactions)],
                'days_ago' => rand(1, 5)
            ];
        }

        if (rand(0, 100) < 25) { // 25% users có learning alert
            $learningTopics = [
                'SolidWorks Advanced Techniques',
                'CNC Programming Best Practices',
                'FEA Analysis Fundamentals',
                'Industry 4.0 Technologies',
                'Sustainable Manufacturing'
            ];

            $alerts[] = [
                'type' => 'learning',
                'title' => 'Nội dung học tập mới',
                'message' => 'Có tutorial mới về "' . $learningTopics[array_rand($learningTopics)] . '" được chia sẻ trong community. Check it out!',
                'days_ago' => rand(1, 7)
            ];
        }

        return $alerts;
    }
}
