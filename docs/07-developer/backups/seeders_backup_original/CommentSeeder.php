<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();

        if ($users->count() === 0) {
            return;
        }

        // Comments cho threads
        if ($threads->count() > 0) {
            $threadComments = [
                'Cảm ơn bạn đã chia sẻ! Rất hữu ích cho dự án hiện tại của tôi.',
                'Tôi cũng đang gặp vấn đề tương tự. Có thể bạn chia sẻ thêm chi tiết technical specs không?',
                'Excellent work! Có thể share thêm về cost analysis không?',
                'Đã save post này. Sẽ apply vào project tuần tới.',
                'Theo kinh nghiệm của tôi, nên thêm redundancy cho critical components.',
                'ROI như thế nào sau khi implement? Có data cụ thể không?',
                'Impressive! Commissioning time mất bao lâu vậy bạn?',
                'Safety aspects được handle như thế nào? Có risk assessment chưa?',
                'Tôi recommend thêm predictive maintenance cho hệ thống này.',
                'Code có thể optimize thêm bằng cách sử dụng function blocks.',
                'Đã test với extreme conditions chưa? Winter operation có issues không?',
                'Vendor support như thế nào? Training cho operators đầy đủ chưa?',
                'Integration với existing MES system có smooth không?',
                'Backup strategy cho PLC program ra sao?',
                'Documentation có complete chưa? Cần cho maintenance team.',
            ];

            foreach ($threads->take(20) as $thread) {
                $numComments = rand(1, 6);
                for ($i = 0; $i < $numComments; $i++) {
                    Comment::create([
                        'thread_id' => $thread->id,
                        'user_id' => $users->random()->id,
                        'content' => $threadComments[array_rand($threadComments)],
                        'like_count' => rand(0, 15),
                        'created_at' => now()->subDays(rand(0, 20))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        // Tạo nested comments (replies) cho threads
        $topLevelComments = Comment::whereNull('parent_id')->take(20)->get();

        $replyComments = [
            '@user Cảm ơn feedback! Tôi sẽ update documentation.',
            'Good point! Sẽ consider trong phase 2.',
            'Exactly! Đó là lesson learned quan trọng.',
            'Tôi agree, safety first!',
            'Cost analysis sẽ có trong report cuối tháng.',
            'Training materials đang finalize.',
            'Vendor đã confirm support 24/7.',
            'Backup system đã setup parallel.',
            'Performance better than expected!',
            'Troubleshooting guide available ở wiki.',
        ];

        foreach ($topLevelComments as $comment) {
            if (rand(0, 100) < 30) { // 30% chance có reply
                Comment::create([
                    'thread_id' => $comment->thread_id,
                    'user_id' => $users->random()->id,
                    'parent_id' => $comment->id,
                    'content' => $replyComments[array_rand($replyComments)],
                    'like_count' => rand(0, 5),
                    'created_at' => $comment->created_at->addHours(rand(1, 48)),
                    'updated_at' => $comment->created_at->addHours(rand(1, 48)),
                ]);
            }
        }
    }
}
