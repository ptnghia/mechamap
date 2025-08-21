<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class ReactionSeeder extends Seeder
{
    /**
     * Seed reactions với emoji reactions thực tế
     * Tạo reactions cho threads và comments
     */
    public function run(): void
    {
        $this->command->info('😊 Bắt đầu seed reactions...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $comments = Comment::all();

        if ($users->isEmpty() || $threads->isEmpty() || $comments->isEmpty()) {
            $this->command->error('❌ Cần có users, threads và comments trước khi seed reactions!');
            return;
        }

        // Tạo reactions
        $this->createReactions($users, $threads, $comments);

        $this->command->info('✅ Hoàn thành seed reactions!');
    }

    private function createReactions($users, $threads, $comments): void
    {
        $reactions = [];

        // Reactions cho threads
        foreach ($threads as $thread) {
            $reactionCount = rand(0, 15); // 0-15 reactions per thread

            if ($reactionCount > 0) {
                $reactingUsers = $users->random(min($reactionCount, $users->count()));

                foreach ($reactingUsers as $user) {
                    // Tránh self-reaction
                    if ($user->id !== $thread->user_id) {
                        $reaction = $this->getRandomReaction($thread->title, 'thread');

                        $reactions[] = [
                            'user_id' => $user->id,
                            'reactable_type' => 'App\\Models\\Thread',
                            'reactable_id' => $thread->id,
                            'type' => $reaction,
                            'created_at' => $thread->created_at->addDays(rand(0, 10)),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Reactions cho comments
        foreach ($comments as $comment) {
            $reactionCount = rand(0, 8); // 0-8 reactions per comment

            if ($reactionCount > 0) {
                $reactingUsers = $users->random(min($reactionCount, $users->count()));

                foreach ($reactingUsers as $user) {
                    // Tránh self-reaction
                    if ($user->id !== $comment->user_id) {
                        $reaction = $this->getRandomReaction($comment->content, 'comment');

                        $reactions[] = [
                            'user_id' => $user->id,
                            'reactable_type' => 'App\\Models\\Comment',
                            'reactable_id' => $comment->id,
                            'type' => $reaction,
                            'created_at' => $comment->created_at->addHours(rand(1, 48)),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Batch insert
        $chunks = array_chunk($reactions, 500);
        foreach ($chunks as $chunk) {
            DB::table('reactions')->insert($chunk);
        }

        $this->command->line("   😊 Tạo " . count($reactions) . " reactions");
    }

    private function getRandomReaction($content, $type): string
    {
        // Reactions dựa vào content context
        $content = strtolower($content);

        // Technical/Educational content
        if (str_contains($content, 'tutorial') || str_contains($content, 'guide') || str_contains($content, 'how to')) {
            $reactions = ['👍', '💡', '🎯', '📚', '⭐'];
            return $reactions[array_rand($reactions)];
        }

        // Problem solving content
        if (str_contains($content, 'problem') || str_contains($content, 'issue') || str_contains($content, 'help')) {
            $reactions = ['🤔', '💭', '🔧', '💡', '👍'];
            return $reactions[array_rand($reactions)];
        }

        // Achievement/Success content
        if (str_contains($content, 'completed') || str_contains($content, 'finished') || str_contains($content, 'success')) {
            $reactions = ['🎉', '👏', '🔥', '⭐', '💪'];
            return $reactions[array_rand($reactions)];
        }

        // CAD/Design content
        if (str_contains($content, 'solidworks') || str_contains($content, 'cad') || str_contains($content, 'design')) {
            $reactions = ['🎨', '⚙️', '📐', '💡', '👍'];
            return $reactions[array_rand($reactions)];
        }

        // CNC/Manufacturing content
        if (str_contains($content, 'cnc') || str_contains($content, 'machining') || str_contains($content, 'manufacturing')) {
            $reactions = ['🔧', '⚙️', '🏭', '💪', '👍'];
            return $reactions[array_rand($reactions)];
        }

        // Analysis/Simulation content
        if (str_contains($content, 'fea') || str_contains($content, 'analysis') || str_contains($content, 'simulation')) {
            $reactions = ['📊', '🧮', '💡', '🎯', '👍'];
            return $reactions[array_rand($reactions)];
        }

        // Robot/Automation content
        if (str_contains($content, 'robot') || str_contains($content, 'automation') || str_contains($content, 'plc')) {
            $reactions = ['🤖', '⚙️', '🔧', '💡', '🚀'];
            return $reactions[array_rand($reactions)];
        }

        // Material/Testing content
        if (str_contains($content, 'material') || str_contains($content, 'test') || str_contains($content, 'quality')) {
            $reactions = ['🔬', '📋', '✅', '💎', '👍'];
            return $reactions[array_rand($reactions)];
        }

        // Funny/Interesting content
        if (str_contains($content, 'funny') || str_contains($content, 'interesting') || str_contains($content, 'amazing')) {
            $reactions = ['😄', '😮', '🤯', '👀', '🔥'];
            return $reactions[array_rand($reactions)];
        }

        // Default reactions for general content
        $defaultReactions = [
            '👍', '❤️', '💡', '🔥', '⭐', '👏', '🎯', '💪', '🚀', '✨'
        ];

        // Weight distribution for default reactions
        $weights = [
            '👍' => 30,  // Most common
            '💡' => 20,  // Ideas/insights
            '❤️' => 15,  // Love/appreciation
            '🔥' => 10,  // Hot/trending
            '⭐' => 8,   // Quality content
            '👏' => 7,   // Appreciation
            '🎯' => 4,   // On target
            '💪' => 3,   // Strong/powerful
            '🚀' => 2,   // Innovative
            '✨' => 1    // Special
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $reaction => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $reaction;
            }
        }

        return '👍'; // Fallback
    }
}
