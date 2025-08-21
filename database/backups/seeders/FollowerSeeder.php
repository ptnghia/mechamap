<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThreadFollow;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FollowerSeeder extends Seeder
{
    /**
     * Seed follower relationships: user follows và thread follows
     * Tạo realistic following patterns cho community
     */
    public function run(): void
    {
        $this->command->info('👥 Bắt đầu seed follower relationships...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();

        if ($users->isEmpty() || $threads->isEmpty()) {
            $this->command->error('❌ Cần có users và threads trước khi seed followers!');
            return;
        }

        // Seed theo thứ tự
        $this->seedUserFollows($users);
        $this->seedThreadFollows($threads, $users);

        // Cập nhật cached counters
        $this->updateFollowerCounts();

        $this->command->info('✅ Hoàn thành seed follower relationships!');
    }

    private function seedUserFollows($users): void
    {
        $this->command->info('👥 Seeding user follows...');

        $userFollows = [];
        $processedPairs = [];

        foreach ($users as $user) {
            // Mỗi user follow 10-40% users khác (realistic social network)
            $followPercentage = rand(10, 40);
            $followCount = ceil($users->count() * $followPercentage / 100);

            // Random users để follow (trừ chính mình)
            $otherUsers = $users->where('id', '!=', $user->id);
            $followingUsers = $otherUsers->random(min($followCount, $otherUsers->count()));

            foreach ($followingUsers as $followedUser) {
                $pairKey = $user->id . '-' . $followedUser->id;

                // Tránh duplicate
                if (!in_array($pairKey, $processedPairs)) {
                    $userFollows[] = [
                        'follower_id' => $user->id,
                        'following_id' => $followedUser->id,
                        'created_at' => $this->getRandomTimestamp($user->created_at),
                        'updated_at' => now(),
                    ];

                    $processedPairs[] = $pairKey;
                }
            }
        }

        // Batch insert để tăng performance
        $chunks = array_chunk($userFollows, 500);
        foreach ($chunks as $chunk) {
            DB::table('followers')->insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($userFollows) . " user follows");
    }

    private function seedThreadFollows($threads, $users): void
    {
        $this->command->info('📌 Seeding thread follows...');

        $threadFollows = [];
        $processedPairs = [];

        foreach ($threads as $thread) {
            // Thread follows dựa vào chất lượng và type
            $followPercentage = $this->getThreadFollowPercentage($thread);
            $followCount = ceil($users->count() * $followPercentage / 100);

            // Random users để follow thread này (trừ author)
            $otherUsers = $users->where('id', '!=', $thread->user_id);
            $followingUsers = $otherUsers->random(min($followCount, $otherUsers->count()));

            foreach ($followingUsers as $user) {
                $pairKey = $user->id . '-' . $thread->id;

                // Tránh duplicate
                if (!in_array($pairKey, $processedPairs)) {
                    $threadFollows[] = [
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
        $chunks = array_chunk($threadFollows, 500);
        foreach ($chunks as $chunk) {
            ThreadFollow::insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($threadFollows) . " thread follows");
    }

    private function getThreadFollowPercentage(Thread $thread): int
    {
        $basePercentage = 8; // Base 8%

        // Bonus cho featured threads
        if ($thread->is_featured) {
            $basePercentage += 15;
        }

        // Bonus cho sticky threads
        if ($thread->is_sticky) {
            $basePercentage += 10;
        }

        // Bonus cho solved threads
        if ($thread->is_solved) {
            $basePercentage += 8;
        }

        // Bonus cho high quality score
        if ($thread->quality_score >= 8.0) {
            $basePercentage += 12;
        }

        // Bonus cho tutorial threads
        if ($thread->thread_type === 'tutorial') {
            $basePercentage += 10;
        }

        // Bonus cho question threads (people want to see answers)
        if ($thread->thread_type === 'question') {
            $basePercentage += 6;
        }

        // Bonus cho threads với nhiều replies
        if ($thread->replies >= 5) {
            $basePercentage += 5;
        }

        // Bonus cho threads có technical content
        if ($thread->has_calculations || $thread->has_3d_models) {
            $basePercentage += 8;
        }

        // Bonus cho expert verified threads
        if ($thread->expert_verified) {
            $basePercentage += 10;
        }

        return min($basePercentage, 45); // Cap at 45%
    }

    private function getRandomTimestamp($baseTimestamp): string
    {
        // Random timestamp sau khi user/thread được tạo
        $baseTime = is_string($baseTimestamp) ? strtotime($baseTimestamp) : $baseTimestamp->timestamp;
        $randomOffset = rand(3600, 86400 * 14); // 1 hour to 14 days after

        return date('Y-m-d H:i:s', $baseTime + $randomOffset);
    }

    private function updateFollowerCounts(): void
    {
        $this->command->info('🔄 Cập nhật follower counts...');

        // Update thread follow counts
        DB::statement("
            UPDATE threads
            SET follow_count = (
                SELECT COUNT(*)
                FROM thread_follows
                WHERE thread_follows.thread_id = threads.id
            )
        ");

        $this->command->line("   ✅ Cập nhật thread follow counts hoàn thành");
        $this->command->line("   ℹ️  User follower counts sẽ được tính dynamic từ relationships");
    }
}
