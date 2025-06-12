<?php

namespace Database\Seeders;

use App\Models\Showcase;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Database\Seeder;

class ShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎯 Bắt đầu tạo Showcases...\n";

        $users = User::all();
        $threads = Thread::with('posts')->get();

        if ($users->count() === 0) {
            echo "❌ Không có users để tạo showcase\n";
            return;
        }

        if ($threads->count() === 0) {
            echo "❌ Không có threads để showcase\n";
            return;
        }

        // Tạo showcases từ threads có chất lượng cao
        $showcaseData = [
            [
                'description' => 'Thiết kế hệ thống truyền động bánh răng CNC 5 trục với độ chính xác cao, giảm thiểu rung động và tối ưu hiệu năng.',
                'order' => 1,
            ],
            [
                'description' => 'Mô hình 3D chi tiết động cơ V8 với đầy đủ các bộ phận cho nghiên cứu và học tập kỹ thuật ô tô.',
                'order' => 2,
            ],
            [
                'description' => 'Hệ thống thủy lực máy ép 100 tấn với sơ đồ mạch chi tiết và thông số kỹ thuật hoàn chỉnh.',
                'order' => 3,
            ],
            [
                'description' => 'Robot công nghiệp 6 bậc tự do cho ứng dụng hàn tự động với hệ thống điều khiển thông minh.',
                'order' => 4,
            ],
            [
                'description' => 'Bản vẽ kỹ thuật hộp số tự động 8 cấp với công nghệ hybrid cho xe điện.',
                'order' => 5,
            ],
            [
                'description' => 'Thiết kế đầy đủ hệ thống phanh ABS với mô hình 3D và mô phỏng hoạt động.',
                'order' => 6,
            ],
            [
                'description' => 'Máy công cụ CNC phay 5 trục với hệ thống đo lường tự động và điều khiển số.',
                'order' => 7,
            ],
            [
                'description' => 'Hệ thống lái trợ lực điện EPS với tính toán động lực học và điều khiển thông minh.',
                'order' => 8,
            ],
            [
                'description' => 'Thiết kế tua-bin khí với mô phỏng CFD và tối ưu hóa hiệu suất khí động học.',
                'order' => 9,
            ],
            [
                'description' => 'Hệ thống treo khí nén thích ứng với điều khiển tự động theo địa hình.',
                'order' => 10,
            ],
        ];

        $createdCount = 0;

        // Lấy threads chất lượng cao để showcase
        $qualityThreads = $threads->filter(function ($thread) {
            return $thread->view_count > 50 ||
                $thread->posts->count() > 3 ||
                str_contains(strtolower($thread->title), 'thiết kế') ||
                str_contains(strtolower($thread->title), 'cad') ||
                str_contains(strtolower($thread->title), 'mô hình');
        })->take(15);

        // Nếu không đủ threads chất lượng, lấy thêm threads ngẫu nhiên
        if ($qualityThreads->count() < 10) {
            $additionalThreads = $threads->diff($qualityThreads)->random(10 - $qualityThreads->count());
            $qualityThreads = $qualityThreads->merge($additionalThreads);
        }

        foreach ($qualityThreads->take(10) as $index => $thread) {
            $user = $users->random();
            $showcaseInfo = $showcaseData[$index] ?? [
                'description' => 'Dự án kỹ thuật chất lượng cao với thiết kế chi tiết và tính toán chính xác.',
                'order' => $index + 1,
            ];

            // Kiểm tra xem đã tồn tại showcase này chưa
            $exists = Showcase::where('user_id', $user->id)
                ->where('showcaseable_type', Thread::class)
                ->where('showcaseable_id', $thread->id)
                ->exists();

            if ($exists) {
                continue; // Bỏ qua nếu đã tồn tại
            }

            // Tạo showcase từ thread
            try {
                $showcase = Showcase::create([
                    'user_id' => $user->id,
                    'showcaseable_type' => Thread::class,
                    'showcaseable_id' => $thread->id,
                    'description' => $showcaseInfo['description'],
                    'order' => $showcaseInfo['order'],
                ]);

                if ($showcase) {
                    echo "✅ Showcase #{$showcase->id}: User {$user->name} showcased thread '{$thread->title}'\n";
                    $createdCount++;
                }
            } catch (\Exception $e) {
                echo "⚠️ Không thể tạo showcase cho thread {$thread->id}: " . $e->getMessage() . "\n";
                continue;
            }
        }

        // Tạo thêm showcases từ posts chất lượng cao
        $qualityPosts = Post::whereHas('thread', function ($query) {
            $query->where('view_count', '>', 30);
        })->with(['thread', 'user'])->get()->take(5);

        foreach ($qualityPosts as $index => $post) {
            $user = $users->random();

            // Kiểm tra xem đã tồn tại showcase này chưa
            $exists = Showcase::where('user_id', $user->id)
                ->where('showcaseable_type', Post::class)
                ->where('showcaseable_id', $post->id)
                ->exists();

            if ($exists) {
                continue; // Bỏ qua nếu đã tồn tại
            }

            try {
                $showcase = Showcase::create([
                    'user_id' => $user->id,
                    'showcaseable_type' => Post::class,
                    'showcaseable_id' => $post->id,
                    'description' => 'Bài viết kỹ thuật chất lượng cao với nội dung chi tiết và hữu ích cho cộng đồng.',
                    'order' => $createdCount + $index + 1,
                ]);

                if ($showcase) {
                    echo "✅ Showcase #{$showcase->id}: User {$user->name} showcased post trong thread '{$post->thread->title}'\n";
                    $createdCount++;
                }
            } catch (\Exception $e) {
                echo "⚠️ Không thể tạo showcase cho post {$post->id}: " . $e->getMessage() . "\n";
                continue;
            }
        }

        echo "🎉 Hoàn thành tạo {$createdCount} showcases!\n";
    }
}
