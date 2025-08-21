<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserActivity;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Showcase;
use Illuminate\Support\Facades\DB;

class UserActivitySeeder extends Seeder
{
    /**
     * Seed user activities với hoạt động thực tế
     * Tạo activity log cho users
     */
    public function run(): void
    {
        $this->command->info('📈 Bắt đầu seed user activities...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $comments = Comment::all();
        $showcases = Showcase::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed activities!');
            return;
        }

        // Tạo activities cho users
        $this->createUserActivities($users, $threads, $comments, $showcases);

        $this->command->info('✅ Hoàn thành seed user activities!');
    }

    private function createUserActivities($users, $threads, $comments, $showcases): void
    {
        $activities = [];

        // Tạo activities cho mỗi user
        foreach ($users as $user) {
            // Mỗi user có 10-30 activities
            $activityCount = rand(10, 30);

            for ($i = 0; $i < $activityCount; $i++) {
                $activity = $this->generateRandomActivity($user, $users, $threads, $comments, $showcases);
                if ($activity) {
                    $activities[] = $activity;
                }
            }
        }

        // Batch insert để tăng performance
        $chunks = array_chunk($activities, 500);
        foreach ($chunks as $chunk) {
            UserActivity::insert($chunk);
        }

        $this->command->line("   📈 Tạo " . count($activities) . " user activities");
    }

    private function generateRandomActivity($user, $users, $threads, $comments, $showcases): ?array
    {
        $activityTypes = [
            'thread_created' => 15,
            'comment_posted' => 25,
            'thread_liked' => 20,
            'comment_liked' => 15,
            'thread_bookmarked' => 10,
            'showcase_created' => 5,
            'showcase_liked' => 5,
            'user_followed' => 3,
            'profile_updated' => 2
        ];

        // Weighted random selection
        $random = rand(1, 100);
        $cumulative = 0;
        $selectedType = null;

        foreach ($activityTypes as $type => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                $selectedType = $type;
                break;
            }
        }

        return $this->createActivityData($selectedType, $user, $users, $threads, $comments, $showcases);
    }

    private function createActivityData($type, $user, $users, $threads, $comments, $showcases): ?array
    {
        $baseActivity = [
            'user_id' => $user->id,
            'activity_type' => $type,
            'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            'updated_at' => now(),
        ];

        switch ($type) {
            case 'thread_created':
                if ($threads->isEmpty()) return null;
                $thread = $threads->where('user_id', $user->id)->first();
                if (!$thread) return null;
                return array_merge($baseActivity, [
                    'activity_id' => $thread->id,
                ]);

            case 'comment_posted':
                if ($comments->isEmpty()) return null;
                $comment = $comments->where('user_id', $user->id)->first();
                if (!$comment) return null;
                return array_merge($baseActivity, [
                    'activity_id' => $comment->id,
                ]);

            case 'thread_liked':
                if ($threads->isEmpty()) return null;
                $thread = $threads->random();
                return array_merge($baseActivity, [
                    'activity_id' => $thread->id,
                ]);

            case 'comment_liked':
                if ($comments->isEmpty()) return null;
                $comment = $comments->random();
                return array_merge($baseActivity, [
                    'activity_id' => $comment->id,
                ]);

            case 'thread_bookmarked':
                if ($threads->isEmpty()) return null;
                $thread = $threads->random();
                return array_merge($baseActivity, [
                    'activity_id' => $thread->id,
                ]);

            case 'showcase_created':
                if ($showcases->isEmpty()) return null;
                $showcase = $showcases->where('user_id', $user->id)->first();
                if (!$showcase) return null;
                return array_merge($baseActivity, [
                    'activity_id' => $showcase->id,
                ]);

            case 'showcase_liked':
                if ($showcases->isEmpty()) return null;
                $showcase = $showcases->random();
                return array_merge($baseActivity, [
                    'activity_id' => $showcase->id,
                ]);

            case 'user_followed':
                $followedUser = $users->where('id', '!=', $user->id)->random();
                return array_merge($baseActivity, [
                    'activity_id' => $followedUser->id,
                ]);

            case 'profile_updated':
                return array_merge($baseActivity, [
                    'activity_id' => $user->id,
                ]);

            default:
                return null;
        }
    }
}
