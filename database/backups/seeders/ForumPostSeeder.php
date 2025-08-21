<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

class ForumPostSeeder extends Seeder
{
    /**
     * Seed forum posts for existing threads
     */
    public function run(): void
    {
        $this->command->info('🗨️ Bắt đầu tạo forum posts...');

        $threads = Thread::with('user')->get();
        $users = User::whereIn('role', ['admin', 'moderator', 'senior', 'member'])->get();

        if ($threads->isEmpty()) {
            $this->command->error('❌ Không có threads để tạo posts!');
            return;
        }

        $postsCreated = 0;

        foreach ($threads as $thread) {
            // Each thread gets 0-5 posts
            $postCount = rand(0, 5);

            for ($i = 0; $i < $postCount; $i++) {
                $author = $users->random();

                // Don't let user reply to their own thread immediately
                if ($i === 0 && $author->id === $thread->user_id) {
                    $author = $users->where('id', '!=', $thread->user_id)->random();
                }

                $post = Post::create([
                    'thread_id' => $thread->id,
                    'user_id' => $author->id,
                    'content' => $this->generatePostContent($thread, $i),
                    'created_at' => $thread->created_at->addMinutes(rand(10, 1440 * 7)), // Within a week
                ]);

                $postsCreated++;
            }
        }

        $this->command->info("✅ Đã tạo {$postsCreated} forum posts");
    }

    private function generatePostContent($thread, $postIndex): string
    {
        $responses = [
            // First responses
            [
                "Cảm ơn bạn đã chia sẻ! Tôi cũng đang gặp vấn đề tương tự.",
                "Rất hữu ích! Bạn có thể chia sẻ thêm chi tiết không?",
                "Tôi nghĩ bạn nên thử phương pháp này...",
                "Theo kinh nghiệm của tôi, vấn đề này thường do...",
                "Bạn đã thử kiểm tra các thông số kỹ thuật chưa?",
            ],
            // Follow-up responses
            [
                "Cập nhật: Tôi đã thử và nó hoạt động tốt!",
                "Có thể bạn cần xem xét thêm yếu tố an toàn.",
                "Đây là link tài liệu tham khảo: [document.pdf]",
                "Tôi đã làm project tương tự, có thể chia sẻ kinh nghiệm.",
                "Cần chú ý đến tiêu chuẩn ISO trong trường hợp này.",
            ],
            // Technical responses
            [
                "Theo tính toán của tôi, ứng suất tại điểm này là...",
                "Bạn cần kiểm tra hệ số an toàn trong thiết kế.",
                "Material properties rất quan trọng trong case này.",
                "Finite Element Analysis sẽ giúp verify kết quả.",
                "Đề xuất sử dụng steel grade S355 cho ứng dụng này.",
            ]
        ];

        $responseGroup = min($postIndex, 2);
        return $responses[$responseGroup][array_rand($responses[$responseGroup])];
    }
}
