<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Seed reports với báo cáo vi phạm thực tế
     * Tạo moderation system data
     */
    public function run(): void
    {
        $this->command->info('🚨 Bắt đầu seed reports...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $comments = Comment::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed reports!');
            return;
        }

        // Tạo reports
        $this->createReports($users, $threads, $comments);

        $this->command->info('✅ Hoàn thành seed reports!');
    }

    private function createReports($users, $threads, $comments): void
    {
        $reports = [];

        // Tạo 15-25 reports (realistic số lượng cho community)
        $reportCount = rand(15, 25);

        for ($i = 0; $i < $reportCount; $i++) {
            // Random chọn loại content để report
            $contentType = rand(1, 100) <= 70 ? 'thread' : 'comment'; // 70% threads, 30% comments

            if ($contentType === 'thread' && $threads->isNotEmpty()) {
                $reportedThread = $threads->random();
                $reporter = $users->where('id', '!=', $reportedThread->user_id)->random();

                $reportData = $this->generateThreadReport($reporter, $reportedThread);
                $reports[] = $reportData;

            } elseif ($contentType === 'comment' && $comments->isNotEmpty()) {
                $reportedComment = $comments->random();
                $reporter = $users->where('id', '!=', $reportedComment->user_id)->random();

                $reportData = $this->generateCommentReport($reporter, $reportedComment);
                $reports[] = $reportData;
            }
        }

        // Batch insert
        if (!empty($reports)) {
            $chunks = array_chunk($reports, 100);
            foreach ($chunks as $chunk) {
                DB::table('reports')->insert($chunk);
            }
        }

        $this->command->line("   🚨 Tạo " . count($reports) . " reports");
    }

    private function generateThreadReport($reporter, $thread): array
    {
        $reportTypes = [
            'spam' => 25,
            'inappropriate_content' => 20,
            'off_topic' => 15,
            'duplicate' => 10,
            'misleading_information' => 10,
            'copyright_violation' => 8,
            'harassment' => 7,
            'other' => 5
        ];

        $reportType = $this->getWeightedRandom($reportTypes);
        $reason = $this->getReportReason($reportType, 'thread');

        return [
            'user_id' => $reporter->id,
            'reportable_type' => 'App\\Models\\Thread',
            'reportable_id' => $thread->id,
            'reason' => $reportType,
            'description' => $reason,
            'status' => $this->getReportStatus(),
            'created_at' => now()->subDays(rand(0, 30)),
            'updated_at' => now()->subDays(rand(0, 5)),
        ];
    }

    private function generateCommentReport($reporter, $comment): array
    {
        $reportTypes = [
            'inappropriate_content' => 30,
            'spam' => 20,
            'harassment' => 15,
            'off_topic' => 12,
            'misleading_information' => 10,
            'hate_speech' => 8,
            'other' => 5
        ];

        $reportType = $this->getWeightedRandom($reportTypes);
        $reason = $this->getReportReason($reportType, 'comment');

        return [
            'user_id' => $reporter->id,
            'reportable_type' => 'App\\Models\\Comment',
            'reportable_id' => $comment->id,
            'reason' => $reportType,
            'description' => $reason,
            'status' => $this->getReportStatus(),
            'created_at' => now()->subDays(rand(0, 30)),
            'updated_at' => now()->subDays(rand(0, 5)),
        ];
    }

    private function getReportReason($type, $contentType): string
    {
        $reasons = [
            'spam' => [
                'Nội dung spam, quảng cáo không liên quan đến chủ đề kỹ thuật',
                'Đăng berulang kali cùng một nội dung',
                'Link spam đến website bán hàng',
                'Promotional content không phù hợp với forum'
            ],
            'inappropriate_content' => [
                'Nội dung không phù hợp với cộng đồng kỹ thuật',
                'Sử dụng ngôn ngữ thô tục, không professional',
                'Nội dung không liên quan đến mechanical engineering',
                'Vi phạm community guidelines'
            ],
            'off_topic' => [
                'Nội dung không liên quan đến chủ đề forum',
                'Thảo luận ngoài lề không phù hợp',
                'Post sai forum category',
                'Không đúng technical focus của community'
            ],
            'duplicate' => [
                'Thread trùng lặp với nội dung đã có',
                'Câu hỏi đã được hỏi nhiều lần',
                'Duplicate content không cần thiết'
            ],
            'misleading_information' => [
                'Thông tin kỹ thuật không chính xác',
                'Sai lệch về technical specifications',
                'Có thể gây hiểu lầm cho người đọc',
                'Cần fact-check từ experts'
            ],
            'harassment' => [
                'Bình luận mang tính công kích cá nhân',
                'Harassment other community members',
                'Tạo môi trường không friendly',
                'Vi phạm respect guidelines'
            ],
            'copyright_violation' => [
                'Sử dụng nội dung có bản quyền không được phép',
                'Share tài liệu copyrighted',
                'Vi phạm intellectual property'
            ],
            'hate_speech' => [
                'Ngôn ngữ kích động, phân biệt đối xử',
                'Nội dung hate speech',
                'Vi phạm nghiêm trọng community standards'
            ],
            'other' => [
                'Vi phạm khác không thuộc các category trên',
                'Cần review từ moderators',
                'Vấn đề cần xem xét đặc biệt'
            ]
        ];

        $typeReasons = $reasons[$type] ?? $reasons['other'];
        return $typeReasons[array_rand($typeReasons)];
    }

    private function getReportStatus(): string
    {
        $statuses = [
            'open' => 60,       // 60% open
            'closed' => 25,     // 25% closed
            'open' => 10,       // 10% open
            'closed' => 5       // 5% closed
        ];

        return $this->getWeightedRandom($statuses);
    }



    private function getWeightedRandom($weights): string
    {
        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $item;
            }
        }

        return array_key_first($weights);
    }
}
