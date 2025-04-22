<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThreadAndPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        
        // Tạo 20 chủ đề
        for ($i = 1; $i <= 20; $i++) {
            $title = "Chủ đề thảo luận số {$i}";
            $user = $users->random();
            $category = $categories->random();
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => "Nội dung của chủ đề thảo luận số {$i}. Đây là phần mở đầu của cuộc thảo luận.",
                'user_id' => $user->id,
                'category_id' => $category->id,
                'is_sticky' => rand(0, 10) > 8, // 20% cơ hội là sticky
                'is_locked' => rand(0, 10) > 9, // 10% cơ hội là locked
                'view_count' => rand(10, 1000),
            ]);
            
            // Tạo 5-15 bài viết cho mỗi chủ đề
            $postCount = rand(5, 15);
            for ($j = 1; $j <= $postCount; $j++) {
                Post::create([
                    'content' => "Đây là bài viết số {$j} trong chủ đề '{$title}'. Nội dung bài viết này được tạo tự động.",
                    'user_id' => $users->random()->id,
                    'thread_id' => $thread->id,
                ]);
            }
        }
    }
}
