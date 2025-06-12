<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();
        $posts = Post::all();
        $comments = Comment::all();

        if ($users->count() === 0) {
            return;
        }

        $reportTypes = [
            'spam',
            'inappropriate_content',
            'harassment',
            'copyright_violation',
            'misinformation',
            'off_topic',
            'commercial_promotion',
            'duplicate_content',
        ];

        $reportReasons = [
            'spam' => [
                'Nội dung spam liên tục về sản phẩm không liên quan',
                'Đăng link affiliate không được phép',
                'Quảng cáo dịch vụ không phù hợp với diễn đàn',
            ],
            'inappropriate_content' => [
                'Sử dụng ngôn từ không phù hợp với môi trường chuyên nghiệp',
                'Nội dung không phù hợp với chủ đề cơ khí/automation',
                'Chia sẻ thông tin không chính xác có thể gây nguy hiểm',
            ],
            'harassment' => [
                'Tấn công cá nhân người dùng khác',
                'Bình luận thiếu tôn trọng đối với ý kiến chuyên môn',
                'Bình luận phân biệt đối xử không phù hợp',
            ],
            'copyright_violation' => [
                'Chia sẻ tài liệu có bản quyền mà không được phép',
                'Sử dụng hình ảnh không có quyền sở hữu',
                'Copy paste nội dung từ nguồn khác',
            ],
            'misinformation' => [
                'Thông tin kỹ thuật sai lệch có thể gây nguy hiểm',
                'Chia sẻ quy trình không an toàn',
                'Thông số kỹ thuật và tiêu chuẩn không chính xác',
            ],
            'off_topic' => [
                'Nội dung không liên quan đến cơ khí/automation',
                'Thảo luận chính trị trong thread kỹ thuật',
                'Thảo luận ngoài chủ đề về vấn đề cá nhân',
            ],
            'commercial_promotion' => [
                'Quảng cáo sản phẩm/dịch vụ mà không được phép',
                'Tự quảng cáo quá mức cho bản thân',
                'Bài tuyển dụng trong thread kỹ thuật',
            ],
            'duplicate_content' => [
                'Đăng lại thread đã có sẵn',
                'Sao chép nội dung từ posts khác',
                'Đăng nhiều bài cùng nội dung',
            ],
        ];

        // Reports cho threads
        if ($threads->count() > 0) {
            foreach ($threads->take(8) as $thread) {
                $reportType = $reportTypes[array_rand($reportTypes)];
                $reasons = $reportReasons[$reportType];

                Report::create([
                    'user_id' => $users->random()->id,
                    'reportable_id' => $thread->id,
                    'reportable_type' => Thread::class,
                    'reason' => $reasons[array_rand($reasons)],
                    'description' => 'Thread vi phạm quy định cộng đồng về ' . strtolower($reportType),
                    'status' => ['pending', 'resolved', 'rejected'][array_rand(['pending', 'resolved', 'rejected'])],
                    'created_at' => now()->subDays(rand(0, 20)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        }

        // Reports cho posts
        if ($posts->count() > 0) {
            foreach ($posts->take(12) as $post) {
                $reportType = $reportTypes[array_rand($reportTypes)];
                $reasons = $reportReasons[$reportType];

                Report::create([
                    'user_id' => $users->random()->id,
                    'reportable_id' => $post->id,
                    'reportable_type' => Post::class,
                    'reason' => $reasons[array_rand($reasons)],
                    'description' => 'Post vi phạm quy định cộng đồng về ' . strtolower($reportType),
                    'status' => ['pending', 'resolved', 'rejected'][array_rand(['pending', 'resolved', 'rejected'])],
                    'created_at' => now()->subDays(rand(0, 15)),
                    'updated_at' => now()->subDays(rand(0, 8)),
                ]);
            }
        }

        // Reports cho comments
        if ($comments->count() > 0) {
            foreach ($comments->take(6) as $comment) {
                $reportType = $reportTypes[array_rand($reportTypes)];
                $reasons = $reportReasons[$reportType];

                Report::create([
                    'user_id' => $users->random()->id,
                    'reportable_id' => $comment->id,
                    'reportable_type' => Comment::class,
                    'reason' => $reasons[array_rand($reasons)],
                    'description' => 'Comment vi phạm quy định cộng đồng về ' . strtolower($reportType),
                    'status' => ['pending', 'resolved', 'rejected'][array_rand(['pending', 'resolved', 'rejected'])],
                    'created_at' => now()->subDays(rand(0, 12)),
                    'updated_at' => now()->subDays(rand(0, 6)),
                ]);
            }
        }
    }
}
