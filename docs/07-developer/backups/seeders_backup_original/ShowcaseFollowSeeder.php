<?php

namespace Database\Seeders;

use App\Models\ShowcaseFollow;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShowcaseFollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎯 Bắt đầu tạo Showcase Follows (User Follows)...\n";

        $users = User::all();

        if ($users->count() < 2) {
            echo "❌ Cần ít nhất 2 users để tạo follows\n";
            return;
        }

        $createdCount = 0;

        // Tạo follow relationships giữa users
        foreach ($users as $follower) {
            // Mỗi user follow 1-4 users khác
            $numFollows = rand(1, 4);
            $availableUsers = $users->filter(function ($user) use ($follower) {
                return $user->id !== $follower->id; // Không follow chính mình
            });

            if ($availableUsers->count() === 0) {
                continue;
            }

            $usersToFollow = $availableUsers->random(min($numFollows, $availableUsers->count()));

            foreach ($usersToFollow as $userToFollow) {
                // Kiểm tra xem đã follow chưa
                $exists = ShowcaseFollow::where('follower_id', $follower->id)
                    ->where('following_id', $userToFollow->id)
                    ->exists();

                if ($exists) {
                    continue; // Bỏ qua nếu đã follow
                }

                try {
                    $follow = ShowcaseFollow::create([
                        'follower_id' => $follower->id,
                        'following_id' => $userToFollow->id,
                        'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 20))->subHours(rand(0, 23)),
                    ]);

                    if ($follow) {
                        echo "✅ Follow #{$follow->id}: {$follower->name} follows {$userToFollow->name}\n";
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Không thể tạo follow từ {$follower->id} đến {$userToFollow->id}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }

        echo "🎉 Hoàn thành tạo {$createdCount} user follows!\n";
    }
}
