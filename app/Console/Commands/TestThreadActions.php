<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadFollow;

class TestThreadActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:thread-actions {user_id=11} {thread_id=11}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test thread bookmark and follow functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $threadId = $this->argument('thread_id');

        $user = User::find($userId);
        $thread = Thread::find($threadId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        if (!$thread) {
            $this->error("Thread with ID {$threadId} not found");
            return 1;
        }

        $this->info("Testing with User: {$user->name} ({$user->email})");
        $this->info("Testing with Thread: {$thread->title}");
        $this->info("Thread ID: {$thread->id}, Slug: {$thread->slug}");
        $this->info("");

        // Test Bookmark functionality
        $this->info("🔖 Testing Bookmark functionality...");

        // Check if already bookmarked
        $isBookmarked = ThreadBookmark::where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->exists();

        $this->info("Currently bookmarked: " . ($isBookmarked ? 'Yes' : 'No'));

        if (!$isBookmarked) {
            // Add bookmark
            $bookmark = ThreadBookmark::create([
                'user_id' => $userId,
                'thread_id' => $threadId,
                'folder' => 'default',
                'notes' => 'Test bookmark via command'
            ]);
            $this->info("✅ Bookmark added successfully!");
        }

        // Check follow functionality
        $this->info("");
        $this->info("👥 Testing Follow functionality...");

        $isFollowed = ThreadFollow::where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->exists();

        $this->info("Currently followed: " . ($isFollowed ? 'Yes' : 'No'));

        if (!$isFollowed) {
            // Add follow
            $follow = ThreadFollow::create([
                'user_id' => $userId,
                'thread_id' => $threadId
            ]);
            $this->info("✅ Follow added successfully!");
        }

        // Check thread counts
        $thread->refresh();
        $this->info("");
        $this->info("📊 Current thread stats:");
        $this->info("Bookmark count: {$thread->bookmark_count}");
        $this->info("Follow count: {$thread->follow_count}");

        // Test helper methods
        $this->info("");
        $this->info("🧪 Testing helper methods:");
        $isBookmarkedByUser = $thread->isBookmarkedBy($user);
        $this->info("isBookmarkedBy method: " . ($isBookmarkedByUser ? 'Yes' : 'No'));

        return 0;
    }
}
