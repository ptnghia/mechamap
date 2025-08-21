<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use Carbon\Carbon;

class InteractionDataSeeder extends Seeder
{
    /**
     * Tạo dữ liệu tương tác thực tế cho MechaMap
     * - Thread likes, follows, saves
     * - Comment likes
     * - Phân bố realistic theo user activity
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo dữ liệu tương tác thực tế...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $comments = Comment::all();

        if ($users->isEmpty() || $threads->isEmpty()) {
            $this->command->error('❌ Không có users hoặc threads để tạo interactions!');
            return;
        }

        // Xóa dữ liệu cũ
        $this->command->info('🧹 Xóa dữ liệu interactions cũ...');
        DB::table('thread_likes')->truncate();
        DB::table('thread_follows')->truncate();
        DB::table('thread_saves')->truncate();
        DB::table('comment_likes')->truncate();

        // Tạo thread likes (realistic distribution)
        $this->createThreadLikes($users, $threads);

        // Tạo thread follows
        $this->createThreadFollows($users, $threads);

        // Tạo thread saves
        $this->createThreadSaves($users, $threads);

        // Tạo comment likes
        $this->createCommentLikes($users, $comments);

        $this->command->info('✅ Hoàn thành tạo dữ liệu tương tác!');
    }

    /**
     * Tạo thread likes với phân bố realistic
     */
    private function createThreadLikes($users, $threads): void
    {
        $this->command->info('👍 Tạo thread likes...');

        $threadLikes = [];
        $now = Carbon::now();

        foreach ($threads as $thread) {
            // Số likes dựa trên view_count và quality
            $baseChance = min(0.15, $thread->view_count / 1000); // 15% max chance
            $likesCount = (int) ($thread->view_count * $baseChance * rand(50, 150) / 100);

            // Giới hạn reasonable
            $likesCount = min($likesCount, $users->count() - 1, 50);

            if ($likesCount > 0) {
                $likers = $users->where('id', '!=', $thread->user_id)
                              ->random(min($likesCount, $users->count() - 1));

                foreach ($likers as $user) {
                    $threadLikes[] = [
                        'user_id' => $user->id,
                        'thread_id' => $thread->id,
                        'created_at' => $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Insert batch
        if (!empty($threadLikes)) {
            DB::table('thread_likes')->insert($threadLikes);
            $this->command->info("   ✓ Tạo " . count($threadLikes) . " thread likes");
        }
    }

    /**
     * Tạo thread follows
     */
    private function createThreadFollows($users, $threads): void
    {
        $this->command->info('👁️ Tạo thread follows...');

        $threadFollows = [];
        $now = Carbon::now();

        foreach ($threads as $thread) {
            // Follow rate thấp hơn like rate
            $followChance = min(0.05, $thread->view_count / 2000);
            $followsCount = (int) ($thread->view_count * $followChance * rand(30, 80) / 100);

            $followsCount = min($followsCount, $users->count() - 1, 20);

            if ($followsCount > 0) {
                $followers = $users->where('id', '!=', $thread->user_id)
                                  ->random(min($followsCount, $users->count() - 1));

                foreach ($followers as $user) {
                    $threadFollows[] = [
                        'user_id' => $user->id,
                        'thread_id' => $thread->id,
                        'created_at' => $now->copy()->subDays(rand(0, 25))->subHours(rand(0, 23)),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($threadFollows)) {
            DB::table('thread_follows')->insert($threadFollows);
            $this->command->info("   ✓ Tạo " . count($threadFollows) . " thread follows");
        }
    }

    /**
     * Tạo thread saves/bookmarks
     */
    private function createThreadSaves($users, $threads): void
    {
        $this->command->info('🔖 Tạo thread saves...');

        $threadSaves = [];
        $now = Carbon::now();

        foreach ($threads as $thread) {
            // Save rate thấp nhất
            $saveChance = min(0.03, $thread->view_count / 3000);
            $savesCount = (int) ($thread->view_count * $saveChance * rand(20, 60) / 100);

            $savesCount = min($savesCount, $users->count() - 1, 15);

            if ($savesCount > 0) {
                $savers = $users->where('id', '!=', $thread->user_id)
                               ->random(min($savesCount, $users->count() - 1));

                foreach ($savers as $user) {
                    $threadSaves[] = [
                        'user_id' => $user->id,
                        'thread_id' => $thread->id,
                        'created_at' => $now->copy()->subDays(rand(0, 20))->subHours(rand(0, 23)),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($threadSaves)) {
            DB::table('thread_saves')->insert($threadSaves);
            $this->command->info("   ✓ Tạo " . count($threadSaves) . " thread saves");
        }
    }

    /**
     * Tạo comment likes
     */
    private function createCommentLikes($users, $comments): void
    {
        $this->command->info('💬 Tạo comment likes...');

        $commentLikes = [];
        $now = Carbon::now();

        foreach ($comments as $comment) {
            // Comment likes dựa trên length và quality
            $contentLength = strlen($comment->content);
            $baseChance = min(0.08, $contentLength / 1000); // 8% max chance
            $likesCount = rand(0, (int) ($users->count() * $baseChance));

            $likesCount = min($likesCount, $users->count() - 1, 25);

            if ($likesCount > 0) {
                $likers = $users->where('id', '!=', $comment->user_id)
                               ->random(min($likesCount, $users->count() - 1));

                foreach ($likers as $user) {
                    $commentLikes[] = [
                        'user_id' => $user->id,
                        'comment_id' => $comment->id,
                        'created_at' => $now->copy()->subDays(rand(0, 15))->subHours(rand(0, 23)),
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($commentLikes)) {
            DB::table('comment_likes')->insert($commentLikes);
            $this->command->info("   ✓ Tạo " . count($commentLikes) . " comment likes");
        }
    }
}
