<?php

namespace Database\Seeders;

use App\Models\Reaction;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();
        $posts = Post::all();
        $comments = Comment::all();

        if ($users->count() === 0) {
            return;
        }

        $reactionTypes = ['like', 'love', 'helpful', 'thanks', 'disagree'];

        // Reactions cho threads
        if ($threads->count() > 0) {
            foreach ($threads->take(25) as $thread) {
                $numReactions = rand(2, 15);
                $reactedUsers = $users->random(min($numReactions, $users->count()));

                foreach ($reactedUsers as $user) {
                    // Tránh self-reaction và duplicate
                    if ($user->id !== $thread->user_id) {
                        $exists = Reaction::where('user_id', $user->id)
                            ->where('reactable_id', $thread->id)
                            ->where('reactable_type', Thread::class)
                            ->exists();

                        if (!$exists) {
                            Reaction::create([
                                'user_id' => $user->id,
                                'reactable_id' => $thread->id,
                                'reactable_type' => Thread::class,
                                'type' => $reactionTypes[array_rand($reactionTypes)],
                                'created_at' => now()->subDays(rand(0, 20)),
                            ]);
                        }
                    }
                }
            }
        }

        // Reactions cho posts
        if ($posts->count() > 0) {
            foreach ($posts->take(40) as $post) {
                $numReactions = rand(1, 10);
                $reactedUsers = $users->random(min($numReactions, $users->count()));

                foreach ($reactedUsers as $user) {
                    if ($user->id !== $post->user_id) {
                        $exists = Reaction::where('user_id', $user->id)
                            ->where('reactable_id', $post->id)
                            ->where('reactable_type', Post::class)
                            ->exists();

                        if (!$exists) {
                            Reaction::create([
                                'user_id' => $user->id,
                                'reactable_id' => $post->id,
                                'reactable_type' => Post::class,
                                'type' => $reactionTypes[array_rand($reactionTypes)],
                                'created_at' => now()->subDays(rand(0, 15)),
                            ]);
                        }
                    }
                }
            }
        }

        // Reactions cho comments
        if ($comments->count() > 0) {
            foreach ($comments->take(30) as $comment) {
                $numReactions = rand(0, 6);
                if ($numReactions > 0) {
                    $reactedUsers = $users->random(min($numReactions, $users->count()));

                    foreach ($reactedUsers as $user) {
                        if ($user->id !== $comment->user_id) {
                            $exists = Reaction::where('user_id', $user->id)
                                ->where('reactable_id', $comment->id)
                                ->where('reactable_type', Comment::class)
                                ->exists();

                            if (!$exists) {
                                Reaction::create([
                                    'user_id' => $user->id,
                                    'reactable_id' => $comment->id,
                                    'reactable_type' => Comment::class,
                                    'type' => $reactionTypes[array_rand($reactionTypes)],
                                    'created_at' => now()->subDays(rand(0, 12)),
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Cập nhật reaction counts cho các models
        // Note: No reaction count fields exist in the database schema
        // Reactions are counted dynamically through relationships

        // Cập nhật user reaction scores
        foreach ($users as $user) {
            $totalReceivedReactions = Reaction::whereIn('reactable_type', [Thread::class, Post::class, Comment::class])
                ->whereHas('reactable', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            $user->update([
                'reaction_score' => $totalReceivedReactions * 5 + $user->points,
            ]);
        }
    }
}
