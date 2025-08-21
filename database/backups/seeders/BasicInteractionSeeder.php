<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThreadLike;
use App\Models\ThreadBookmark;
use App\Models\CommentLike;
use App\Models\CommentDislike;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class BasicInteractionSeeder extends Seeder
{
    /**
     * Seed basic interactions: likes, bookmarks, dislikes
     * Tạo interactions thực tế cho threads và comments
     */
    public function run(): void
    {
        $this->command->info('👍 Bắt đầu seed basic interactions...');

        // Lấy dữ liệu cần thiết
        $threads = Thread::all();
        $comments = Comment::all();
        $users = User::all();

        if ($threads->isEmpty() || $comments->isEmpty() || $users->isEmpty()) {
            $this->command->error('❌ Cần có threads, comments và users trước khi seed interactions!');
            return;
        }

        // Seed theo thứ tự
        $this->seedThreadLikes($threads, $users);
        $this->seedThreadBookmarks($threads, $users);
        $this->seedCommentLikes($comments, $users);
        $this->seedCommentDislikes($comments, $users);

        // Cập nhật cached counters
        $this->updateCachedCounters();

        $this->command->info('✅ Hoàn thành seed basic interactions!');
    }

    private function seedThreadLikes(Collection $threads, Collection $users): void
    {
        $this->command->info('👍 Seeding thread likes...');

        $threadLikes = [];
        $processedPairs = [];

        foreach ($threads as $thread) {
            // Mỗi thread có 20-70% users like (realistic engagement)
            $likePercentage = rand(20, 70);
            $likeCount = ceil($users->count() * $likePercentage / 100);

            // Random users để like thread này
            $likingUsers = $users->random($likeCount);

            foreach ($likingUsers as $user) {
                $pairKey = $user->id . '-' . $thread->id;

                // Tránh duplicate
                if (!in_array($pairKey, $processedPairs)) {
                    $threadLikes[] = [
                        'user_id' => $user->id,
                        'thread_id' => $thread->id,
                        'created_at' => $this->getRandomTimestamp($thread->created_at),
                        'updated_at' => now(),
                    ];

                    $processedPairs[] = $pairKey;
                }
            }
        }

        // Batch insert để tăng performance
        $chunks = array_chunk($threadLikes, 500);
        foreach ($chunks as $chunk) {
            ThreadLike::insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($threadLikes) . " thread likes");
    }

    private function seedThreadBookmarks(Collection $threads, Collection $users): void
    {
        $this->command->info('🔖 Seeding thread bookmarks...');

        $threadBookmarks = [];
        $processedPairs = [];

        foreach ($threads as $thread) {
            // Bookmark rate thấp hơn like rate (5-25%)
            $bookmarkPercentage = rand(5, 25);
            $bookmarkCount = ceil($users->count() * $bookmarkPercentage / 100);

            // Random users để bookmark thread này
            $bookmarkingUsers = $users->random($bookmarkCount);

            foreach ($bookmarkingUsers as $user) {
                $pairKey = $user->id . '-' . $thread->id;

                // Tránh duplicate
                if (!in_array($pairKey, $processedPairs)) {
                    $threadBookmarks[] = [
                        'user_id' => $user->id,
                        'thread_id' => $thread->id,
                        'created_at' => $this->getRandomTimestamp($thread->created_at),
                        'updated_at' => now(),
                    ];

                    $processedPairs[] = $pairKey;
                }
            }
        }

        // Batch insert
        $chunks = array_chunk($threadBookmarks, 500);
        foreach ($chunks as $chunk) {
            ThreadBookmark::insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($threadBookmarks) . " thread bookmarks");
    }

    private function seedCommentLikes(Collection $comments, Collection $users): void
    {
        $this->command->info('👍 Seeding comment likes...');

        $commentLikes = [];
        $processedPairs = [];

        foreach ($comments as $comment) {
            // Comment likes dựa vào quality và type
            $likePercentage = $this->getCommentLikePercentage($comment);
            $likeCount = ceil($users->count() * $likePercentage / 100);

            // Random users để like comment này
            $likingUsers = $users->random(min($likeCount, $users->count()));

            foreach ($likingUsers as $user) {
                $pairKey = $user->id . '-' . $comment->id;

                // Tránh duplicate và self-like
                if (!in_array($pairKey, $processedPairs) && $user->id !== $comment->user_id) {
                    $commentLikes[] = [
                        'user_id' => $user->id,
                        'comment_id' => $comment->id,
                        'created_at' => $this->getRandomTimestamp($comment->created_at),
                        'updated_at' => now(),
                    ];

                    $processedPairs[] = $pairKey;
                }
            }
        }

        // Batch insert
        $chunks = array_chunk($commentLikes, 500);
        foreach ($chunks as $chunk) {
            CommentLike::insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($commentLikes) . " comment likes");
    }

    private function seedCommentDislikes(Collection $comments, Collection $users): void
    {
        $this->command->info('👎 Seeding comment dislikes...');

        $commentDislikes = [];
        $processedPairs = [];

        foreach ($comments as $comment) {
            // Dislike rate rất thấp (1-8%), chỉ cho low quality comments
            $dislikePercentage = $this->getCommentDislikePercentage($comment);

            if ($dislikePercentage > 0) {
                $dislikeCount = ceil($users->count() * $dislikePercentage / 100);

                // Random users để dislike comment này
                $dislikingUsers = $users->random(min($dislikeCount, $users->count()));

                foreach ($dislikingUsers as $user) {
                    $pairKey = $user->id . '-' . $comment->id;

                    // Tránh duplicate và self-dislike
                    if (!in_array($pairKey, $processedPairs) && $user->id !== $comment->user_id) {
                        $commentDislikes[] = [
                            'user_id' => $user->id,
                            'comment_id' => $comment->id,
                            'created_at' => $this->getRandomTimestamp($comment->created_at),
                            'updated_at' => now(),
                        ];

                        $processedPairs[] = $pairKey;
                    }
                }
            }
        }

        // Batch insert
        if (!empty($commentDislikes)) {
            $chunks = array_chunk($commentDislikes, 500);
            foreach ($chunks as $chunk) {
                CommentDislike::insert($chunk);
            }
        }

        $this->command->line("   ✅ Tạo " . count($commentDislikes) . " comment dislikes");
    }

    private function getCommentLikePercentage(Comment $comment): int
    {
        $basePercentage = 15; // Base 15%

        // Bonus cho solution comments
        if ($comment->is_solution) {
            $basePercentage += 25;
        }

        // Bonus cho verified comments
        if ($comment->verification_status === 'verified') {
            $basePercentage += 15;
        }

        // Bonus cho comments có formula/code
        if ($comment->has_formula || $comment->has_code_snippet) {
            $basePercentage += 10;
        }

        // Bonus cho high quality score
        if ($comment->quality_score >= 4.0) {
            $basePercentage += 10;
        }

        // Bonus cho answer types
        if (in_array($comment->answer_type, ['calculation', 'tutorial'])) {
            $basePercentage += 8;
        }

        return min($basePercentage, 60); // Cap at 60%
    }

    private function getCommentDislikePercentage(Comment $comment): int
    {
        $basePercentage = 0;

        // Chỉ dislike cho low quality comments
        if ($comment->quality_score < 3.0) {
            $basePercentage = 5;
        }

        // Thêm dislike cho unverified với low accuracy
        if ($comment->verification_status === 'unverified' && $comment->technical_accuracy_score < 3.0) {
            $basePercentage += 3;
        }

        return min($basePercentage, 8); // Cap at 8%
    }

    private function getRandomTimestamp($baseTimestamp): string
    {
        // Random timestamp sau khi thread/comment được tạo
        $baseTime = is_string($baseTimestamp) ? strtotime($baseTimestamp) : $baseTimestamp->timestamp;
        $randomOffset = rand(3600, 86400 * 7); // 1 hour to 7 days after

        return date('Y-m-d H:i:s', $baseTime + $randomOffset);
    }

    private function updateCachedCounters(): void
    {
        $this->command->info('🔄 Cập nhật cached counters...');

        // Update thread like counts
        DB::statement("
            UPDATE threads
            SET likes = (
                SELECT COUNT(*)
                FROM thread_likes
                WHERE thread_likes.thread_id = threads.id
            )
        ");

        // Update thread bookmark counts
        DB::statement("
            UPDATE threads
            SET bookmarks = (
                SELECT COUNT(*)
                FROM thread_bookmarks
                WHERE thread_bookmarks.thread_id = threads.id
            )
        ");

        // Update comment like counts
        DB::statement("
            UPDATE comments
            SET like_count = (
                SELECT COUNT(*)
                FROM comment_likes
                WHERE comment_likes.comment_id = comments.id
            )
        ");

        // Update comment dislike counts
        DB::statement("
            UPDATE comments
            SET dislikes_count = (
                SELECT COUNT(*)
                FROM comment_dislikes
                WHERE comment_dislikes.comment_id = comments.id
            )
        ");

        $this->command->line("   ✅ Cập nhật cached counters hoàn thành");
    }
}
