<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Showcase;
use Illuminate\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();
        $posts = Post::all();
        $showcases = Showcase::all();

        if ($users->count() === 0) {
            return;
        }

        // Tạo bookmarks cho threads
        if ($threads->count() > 0) {
            foreach ($users->take(15) as $user) {
                $userThreads = $threads->random(rand(3, 8));

                foreach ($userThreads as $thread) {
                    // Tránh duplicate bookmarks
                    $exists = Bookmark::where('user_id', $user->id)
                        ->where('bookmarkable_id', $thread->id)
                        ->where('bookmarkable_type', Thread::class)
                        ->exists();

                    if (!$exists) {
                        Bookmark::create([
                            'user_id' => $user->id,
                            'bookmarkable_id' => $thread->id,
                            'bookmarkable_type' => Thread::class,
                            'created_at' => now()->subDays(rand(0, 30)),
                        ]);
                    }
                }
            }
        }

        // Tạo bookmarks cho posts
        if ($posts->count() > 0) {
            foreach ($users->take(12) as $user) {
                $userPosts = $posts->random(rand(2, 6));

                foreach ($userPosts as $post) {
                    $exists = Bookmark::where('user_id', $user->id)
                        ->where('bookmarkable_id', $post->id)
                        ->where('bookmarkable_type', Post::class)
                        ->exists();

                    if (!$exists) {
                        Bookmark::create([
                            'user_id' => $user->id,
                            'bookmarkable_id' => $post->id,
                            'bookmarkable_type' => Post::class,
                            'created_at' => now()->subDays(rand(0, 20)),
                        ]);
                    }
                }
            }
        }

        // Tạo bookmarks cho showcases
        if ($showcases->count() > 0) {
            foreach ($users->take(10) as $user) {
                $userShowcases = $showcases->random(rand(1, 4));

                foreach ($userShowcases as $showcase) {
                    $exists = Bookmark::where('user_id', $user->id)
                        ->where('bookmarkable_id', $showcase->id)
                        ->where('bookmarkable_type', Showcase::class)
                        ->exists();

                    if (!$exists) {
                        Bookmark::create([
                            'user_id' => $user->id,
                            'bookmarkable_id' => $showcase->id,
                            'bookmarkable_type' => Showcase::class,
                            'created_at' => now()->subDays(rand(0, 25)),
                        ]);
                    }
                }
            }
        }
    }
}
