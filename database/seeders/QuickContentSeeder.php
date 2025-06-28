<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\ThreadFollow;
use App\Models\Reaction;
use App\Models\Bookmark;
use App\Models\UserActivity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class QuickContentSeeder extends Seeder
{
    /**
     * Seed nhanh content cho tất cả forums hiện có
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu seed nhanh content cho MechaMap...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $forums = Forum::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $forums->isEmpty()) {
            $this->command->error('❌ Cần có users và forums trước!');
            return;
        }

        // Tạo threads cho mỗi forum
        $allThreads = [];
        foreach ($forums as $forum) {
            $threads = $this->createThreadsForForum($forum, $users, $tags);
            $allThreads = array_merge($allThreads, $threads);
        }

        $this->command->info('✅ Đã tạo ' . count($allThreads) . ' threads');

        // Tạo comments cho threads
        $totalComments = 0;
        foreach ($allThreads as $thread) {
            $comments = $this->createCommentsForThread($thread, $users);
            $totalComments += count($comments);
        }

        $this->command->info('✅ Đã tạo ' . $totalComments . ' comments');

        // Tạo reactions, bookmarks, follows
        $this->createInteractions($allThreads, $users);
        $this->command->info('✅ Đã tạo interactions');

        // Tạo user activities
        $this->createUserActivities($users, $allThreads);
        $this->command->info('✅ Đã tạo user activities');

        $this->command->info('🎉 Hoàn thành seed nhanh content!');
    }

    private function createThreadsForForum($forum, $users, $tags): array
    {
        $threads = [];
        $threadCount = rand(2, 5); // 2-5 threads per forum

        for ($i = 0; $i < $threadCount; $i++) {
            $user = $users->random();
            $threadData = $this->getThreadDataForForum($forum);

            $thread = Thread::create([
                'title' => $threadData['title'],
                'slug' => Str::slug($threadData['title']) . '-' . $forum->id . '-' . time() . '-' . rand(100, 999),
                'content' => $threadData['content'],
                'user_id' => $user->id,
                'forum_id' => $forum->id,
                'category_id' => $forum->category_id,
                'moderation_status' => 'approved',
                'view_count' => rand(10, 200),
                'technical_difficulty' => ['beginner', 'intermediate', 'advanced', 'expert'][array_rand(['beginner', 'intermediate', 'advanced', 'expert'])],
                'project_type' => $threadData['project_type'],
                'requires_calculations' => rand(0, 1),
                'has_cad_files' => rand(0, 1),
                'has_calculations' => rand(0, 1),
                'has_3d_models' => rand(0, 1),
                'expert_verified' => rand(0, 1),
                'thread_type' => 'discussion',
                'status' => 'published',
                'urgency_level' => ['low', 'normal', 'high', 'critical'][array_rand(['low', 'normal', 'high', 'critical'])],
                'industry_sector' => ['automotive', 'aerospace', 'manufacturing', 'energy', 'general'][array_rand(['automotive', 'aerospace', 'manufacturing', 'energy', 'general'])],
                'last_activity_at' => now()->subHours(rand(1, 48)),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Attach random tags (avoid duplicates)
            if ($tags->isNotEmpty()) {
                $randomTags = $tags->random(min(rand(1, 3), $tags->count()))->pluck('id')->unique();
                $thread->tags()->sync($randomTags);
            }

            $threads[] = $thread;
        }

        return $threads;
    }

    private function getThreadDataForForum($forum): array
    {
        $forumName = $forum->name;

        // Thread templates based on forum type
        $templates = [
            'CAD' => [
                'titles' => [
                    'Hướng dẫn thiết kế bánh răng trong {forum}',
                    'Tối ưu hóa assembly lớn trong {forum}',
                    'Cách tạo surface phức tạp trong {forum}',
                    'Tips và tricks cho {forum} beginners',
                ],
                'content' => 'Mình đang làm dự án {project} và gặp khó khăn với {challenge}. Các bạn có kinh nghiệm với vấn đề này không? Mong được chia sẻ workflow và best practices.',
                'project_type' => 'design'
            ],
            'CNC' => [
                'titles' => [
                    'Tối ưu thông số cắt cho {material}',
                    'Giải quyết vấn đề rung động khi gia công',
                    'Lập trình G-code cho hình dạng phức tạp',
                    'Chọn dao phù hợp cho gia công {operation}',
                ],
                'content' => 'Đang gia công chi tiết {part} bằng {material}. Gặp vấn đề {issue}. Các bạn có thể tư vấn thông số cutting speed, feed rate và depth of cut không?',
                'project_type' => 'manufacturing'
            ],
            'FEA' => [
                'titles' => [
                    'Phân tích ứng suất cho {component}',
                    'Setup boundary conditions cho {analysis}',
                    'Mesh refinement strategies',
                    'Validation kết quả FEA với thực nghiệm',
                ],
                'content' => 'Đang thực hiện phân tích {analysis_type} cho {component}. Cần tư vấn về mesh quality, boundary conditions và convergence criteria.',
                'project_type' => 'analysis'
            ]
        ];

        // Determine forum type
        $forumType = 'CAD';
        if (str_contains(strtolower($forumName), 'cnc') || str_contains(strtolower($forumName), 'gia công')) {
            $forumType = 'CNC';
        } elseif (str_contains(strtolower($forumName), 'fea') || str_contains(strtolower($forumName), 'ansys') || str_contains(strtolower($forumName), 'phân tích')) {
            $forumType = 'FEA';
        }

        $template = $templates[$forumType];
        $title = str_replace('{forum}', $forumName, $template['titles'][array_rand($template['titles'])]);

        $content = str_replace(
            ['{project}', '{challenge}', '{material}', '{operation}', '{part}', '{issue}', '{component}', '{analysis}', '{analysis_type}'],
            ['hộp số ô tô', 'modeling surface phức tạp', 'thép không gỉ 316L', 'roughing', 'trục cam', 'chatter vibration', 'khung chassis', 'static analysis', 'modal analysis'],
            $template['content']
        );

        return [
            'title' => $title,
            'content' => $content,
            'project_type' => $template['project_type']
        ];
    }

    private function createCommentsForThread($thread, $users): array
    {
        $comments = [];
        $commentCount = rand(1, 6);

        for ($i = 0; $i < $commentCount; $i++) {
            $user = $users->random();

            $comment = Comment::create([
                'content' => $this->generateCommentContent($thread),
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'like_count' => rand(0, 15),
                'helpful_count' => rand(0, 10),
                'quality_score' => rand(300, 500) / 100,
                'technical_accuracy_score' => rand(350, 500) / 100,
                'verification_status' => rand(0, 1) ? 'verified' : 'unverified',
                'is_solution' => $i === 0 && rand(0, 1), // First comment có thể là solution
                'created_at' => $thread->created_at->addMinutes(rand(30, 2880)),
            ]);

            $comments[] = $comment;
        }

        return $comments;
    }

    private function generateCommentContent($thread): string
    {
        $responses = [
            'Mình đã gặp vấn đề tương tự. Giải pháp là {solution}. Kết quả khá tốt, bạn có thể thử.',
            'Theo kinh nghiệm của mình, {advice}. Đã áp dụng thành công trong nhiều dự án.',
            'Bạn có thể tham khảo {reference}. Approach này khá hiệu quả cho trường hợp này.',
            'Thanks bạn chia sẻ! Mình nghĩ {opinion} và có thể cải thiện bằng {improvement}.',
            'Có thể do {cause}. Bạn đã kiểm tra {check_items} chưa? Theo kinh nghiệm thì {advice}.',
        ];

        $solutions = ['điều chỉnh thông số cắt', 'sử dụng feature khác', 'tối ưu hóa workflow', 'áp dụng best practice'];
        $advice = ['nên kiểm tra material properties trước', 'setup boundary conditions cẩn thận', 'sử dụng adaptive meshing'];
        $references = ['ISO 6336 standard', 'Machinery Handbook', 'technical paper về chủ đề này'];

        $template = $responses[array_rand($responses)];

        return str_replace(
            ['{solution}', '{advice}', '{reference}', '{opinion}', '{improvement}', '{cause}', '{check_items}'],
            [$solutions[array_rand($solutions)], $advice[array_rand($advice)], $references[array_rand($references)], 'approach này khá hay', 'optimize thêm parameters', 'setup không đúng', 'tolerance và constraints'],
            $template
        );
    }

    private function createInteractions($threads, $users): void
    {
        foreach ($threads as $thread) {
            // Reactions
            $reactionUsers = $users->random(rand(1, 8));
            foreach ($reactionUsers as $user) {
                Reaction::create([
                    'user_id' => $user->id,
                    'reactable_type' => Thread::class,
                    'reactable_id' => $thread->id,
                    'type' => ['like', 'helpful', 'expert'][array_rand(['like', 'helpful', 'expert'])],
                ]);
            }

            // Bookmarks
            $bookmarkUsers = $users->random(rand(0, 5));
            foreach ($bookmarkUsers as $user) {
                Bookmark::create([
                    'user_id' => $user->id,
                    'bookmarkable_id' => $thread->id,
                    'bookmarkable_type' => Thread::class,
                ]);
            }

            // Follows
            $followUsers = $users->random(rand(0, 3));
            foreach ($followUsers as $user) {
                ThreadFollow::create([
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                ]);
            }
        }
    }

    private function createUserActivities($users, $threads): void
    {
        $activities = ['thread_created', 'comment_posted', 'thread_viewed', 'user_followed', 'thread_bookmarked'];

        foreach ($users->random(20) as $user) {
            for ($i = 0; $i < rand(3, 10); $i++) {
                UserActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => $activities[array_rand($activities)],
                    'activity_id' => $threads[array_rand($threads)]->id,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
