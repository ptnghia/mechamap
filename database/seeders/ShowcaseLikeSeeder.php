<?php

namespace Database\Seeders;

use App\Models\ShowcaseLike;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShowcaseLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎯 Bắt đầu tạo Showcase Likes...\n";

        $showcases = Showcase::all();
        $users = User::all();

        if ($showcases->count() === 0) {
            echo "❌ Không có showcases để tạo likes\n";
            return;
        }

        if ($users->count() === 0) {
            echo "❌ Không có users để tạo likes\n";
            return;
        }

        $createdCount = 0;

        // Tạo likes cho showcases
        foreach ($showcases as $showcase) {
            // Mỗi showcase có 2-6 likes từ users khác nhau
            $numLikes = rand(2, 6);
            $availableUsers = $users->filter(function ($user) use ($showcase) {
                return $user->id !== $showcase->user_id; // Không like showcase của chính mình
            });

            if ($availableUsers->count() === 0) {
                continue;
            }

            $selectedUsers = $availableUsers->random(min($numLikes, $availableUsers->count()));

            foreach ($selectedUsers as $user) {
                // Kiểm tra xem đã like chưa
                $exists = ShowcaseLike::where('showcase_id', $showcase->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($exists) {
                    continue; // Bỏ qua nếu đã like
                }

                try {
                    $like = ShowcaseLike::create([
                        'showcase_id' => $showcase->id,
                        'user_id' => $user->id,
                        'created_at' => now()->subDays(rand(1, 15))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                    ]);

                    if ($like) {
                        echo "✅ Like #{$like->id}: {$user->name} liked showcase #{$showcase->id}\n";
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Không thể tạo like cho showcase {$showcase->id}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }

        echo "🎉 Hoàn thành tạo {$createdCount} showcase likes!\n";
    }
}
