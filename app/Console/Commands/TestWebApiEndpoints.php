<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadFollow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TestWebApiEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:web-api-endpoints {--user-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test web API endpoints cho thread bookmark và follow functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Web API Endpoints');
        $this->line('================================');

        // Get test user
        $userId = $this->option('user-id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ User với ID {$userId} không tìm thấy");
            return 1;
        }

        $this->info("👤 Test User: {$user->name} (ID: {$user->id})");

        // Get test thread
        $thread = Thread::first();
        if (!$thread) {
            $this->error('❌ Không tìm thấy thread nào để test');
            return 1;
        }

        $this->info("📝 Test Thread: {$thread->title} (ID: {$thread->id})");
        $this->line('');

        // Simulate login
        Auth::login($user);
        $this->info('🔐 User đã được login (simulated)');

        // Test Routes Existence
        $this->testRoutesExistence();

        // Test Bookmark Functionality
        $this->testBookmarkFunctionality($user, $thread);

        // Test Follow Functionality
        $this->testFollowFunctionality($user, $thread);

        // Final Status
        $this->showFinalStatus($user, $thread);

        $this->line('');
        $this->info('✅ Test completed!');

        return 0;
    }

    /**
     * Test if routes exist
     */
    private function testRoutesExistence()
    {
        $this->info('🛣️  Testing Routes Existence:');

        $routes = [
            'POST /api/threads/{thread}/bookmark',
            'DELETE /api/threads/{thread}/bookmark',
            'POST /api/threads/{thread}/follow',
            'DELETE /api/threads/{thread}/follow'
        ];

        foreach ($routes as $route) {
            // Check if route exists in web routes
            $routeExists = collect(Route::getRoutes())->contains(function ($r) use ($route) {
                $method = explode(' ', $route)[0];
                $uri = explode(' ', $route)[1];
                return $r->methods()[0] === $method && str_contains($r->uri(), str_replace('{thread}', '', $uri));
            });

            if ($routeExists) {
                $this->line("   ✅ {$route}");
            } else {
                $this->line("   ❌ {$route}");
            }
        }
        $this->line('');
    }

    /**
     * Test bookmark functionality
     */
    private function testBookmarkFunctionality($user, $thread)
    {
        $this->info('📚 Testing Bookmark Functionality:');

        // Clear existing bookmark
        ThreadBookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->delete();

        // Test: Thread not bookmarked initially
        $isBookmarked = $thread->isBookmarkedBy($user);
        $this->line("   Initial bookmark status: " . ($isBookmarked ? '❌ True (should be false)' : '✅ False'));

        // Test: Add bookmark
        $bookmark = ThreadBookmark::create([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
        ]);

        if ($bookmark) {
            $this->line('   ✅ Bookmark created successfully');

            // Test: Check if bookmarked
            $isBookmarked = $thread->fresh()->isBookmarkedBy($user);
            $this->line("   Bookmark check after creation: " . ($isBookmarked ? '✅ True' : '❌ False (should be true)'));
        } else {
            $this->line('   ❌ Failed to create bookmark');
        }

        // Test: Remove bookmark
        $bookmarkToDelete = ThreadBookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($bookmarkToDelete) {
            $deleted = $bookmarkToDelete->delete(); // Use model instance delete

            if ($deleted) {
                $this->line('   ✅ Bookmark deleted successfully');

                // Test: Check if not bookmarked
                $isBookmarked = $thread->fresh()->isBookmarkedBy($user);
                $this->line("   Bookmark check after deletion: " . ($isBookmarked ? '❌ True (should be false)' : '✅ False'));
            } else {
                $this->line('   ❌ Failed to delete bookmark');
            }
        } else {
            $this->line('   ❌ Bookmark record not found for deletion');
        }

        $this->line('');
    }

    /**
     * Test follow functionality
     */
    private function testFollowFunctionality($user, $thread)
    {
        $this->info('👥 Testing Follow Functionality:');

        // Clear existing follow
        ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->delete();

        $initialFollowCount = $thread->fresh()->follow_count ?? 0;
        $this->line("   Initial follow count: {$initialFollowCount}");

        // Test: Add follow
        $follow = ThreadFollow::create([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
        ]);

        if ($follow) {
            $this->line('   ✅ Follow created successfully');

            // Check follow count update
            $newFollowCount = $thread->fresh()->follow_count ?? 0;
            $this->line("   Follow count after creation: {$newFollowCount} " .
                ($newFollowCount > $initialFollowCount ? '✅' : '❌'));
        } else {
            $this->line('   ❌ Failed to create follow');
        }

        // Test: Remove follow
        $followToDelete = ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($followToDelete) {
            $deleted = $followToDelete->delete(); // Use model instance delete để trigger events

            if ($deleted) {
                $this->line('   ✅ Follow deleted successfully');

                // Check follow count update
                $finalFollowCount = $thread->fresh()->follow_count ?? 0;
                $this->line("   Follow count after deletion: {$finalFollowCount} " .
                    ($finalFollowCount == $initialFollowCount ? '✅' : '❌'));
            } else {
                $this->line('   ❌ Failed to delete follow');
            }
        } else {
            $this->line('   ❌ Follow record not found for deletion');
        }

        $this->line('');
    }

    /**
     * Show final status
     */
    private function showFinalStatus($user, $thread)
    {
        $this->info('📊 Final Status:');

        $bookmarkCount = ThreadBookmark::where('thread_id', $thread->id)->count();
        $followCount = ThreadFollow::where('thread_id', $thread->id)->count();
        $threadFollowCount = $thread->fresh()->follow_count ?? 0;

        $this->line("   Thread bookmarks: {$bookmarkCount}");
        $this->line("   Thread follows: {$followCount}");
        $this->line("   Thread.follow_count: {$threadFollowCount}");

        $isBookmarked = $thread->isBookmarkedBy($user);
        $isFollowed = ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->exists();

        $this->line("   User has bookmarked: " . ($isBookmarked ? 'Yes' : 'No'));
        $this->line("   User is following: " . ($isFollowed ? 'Yes' : 'No'));
    }
}
