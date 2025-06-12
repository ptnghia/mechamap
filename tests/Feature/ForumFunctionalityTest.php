<?php

namespace Tests\Feature;

use App\Models\Forum;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ForumFunctionalityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();
    }

    public function test_forum_index_page_loads_successfully(): void
    {
        // Create some test data
        $forum = Forum::factory()->create([
            'title' => 'Mechanical Engineering',
            'description' => 'Discussion about mechanical engineering topics',
        ]);

        $response = $this->get(route('forums.index'));

        $response->assertStatus(200);
        $response->assertViewIs('forums.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('stats');
        $response->assertSee('Mechanical Engineering');
    }

    public function test_forum_search_functionality(): void
    {
        // Create test forum and threads
        $forum = Forum::factory()->create(['title' => 'Test Forum']);

        $thread1 = Thread::factory()->create([
            'title' => 'CAD Software Discussion',
            'body' => 'Let\'s discuss about CAD software for mechanical design',
            'forum_id' => $forum->id,
        ]);

        $thread2 = Thread::factory()->create([
            'title' => 'CNC Machining Tips',
            'body' => 'Share your CNC machining experience here',
            'forum_id' => $forum->id,
        ]);

        // Test search
        $response = $this->get(route('forums.search', ['q' => 'CAD']));

        $response->assertStatus(200);
        $response->assertViewIs('forums.search');
        $response->assertSee('CAD Software Discussion');
        $response->assertDontSee('CNC Machining Tips');
    }

    public function test_forum_search_requires_minimum_characters(): void
    {
        $response = $this->get(route('forums.search', ['q' => 'ab']));

        $response->assertStatus(422);
        $response->assertSessionHasErrors(['q']);
    }

    public function test_forum_show_page_with_threads(): void
    {
        $user = User::factory()->create();
        $forum = Forum::factory()->create(['title' => 'Test Forum']);

        $threads = Thread::factory(3)->create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('forums.show', $forum));

        $response->assertStatus(200);
        $response->assertViewIs('forums.show');
        $response->assertViewHas('forum');
        $response->assertViewHas('threads');
        $response->assertViewHas('forumStats');

        foreach ($threads as $thread) {
            $response->assertSee($thread->title);
        }
    }

    public function test_forum_show_filtering_by_search(): void
    {
        $user = User::factory()->create();
        $forum = Forum::factory()->create();

        $targetThread = Thread::factory()->create([
            'title' => 'Advanced CAD Techniques',
            'body' => 'Discussion about advanced CAD modeling',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);

        $otherThread = Thread::factory()->create([
            'title' => 'Basic Manufacturing',
            'body' => 'Introduction to manufacturing processes',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('forums.show', [
            'forum' => $forum,
            'search' => 'CAD'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Advanced CAD Techniques');
        $response->assertDontSee('Basic Manufacturing');
    }

    public function test_forum_show_sorting_options(): void
    {
        $user = User::factory()->create();
        $forum = Forum::factory()->create();

        // Create threads with different creation dates
        $oldThread = Thread::factory()->create([
            'title' => 'Old Thread',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'created_at' => now()->subDays(10),
        ]);

        $newThread = Thread::factory()->create([
            'title' => 'New Thread',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'created_at' => now()->subHour(),
        ]);

        // Test latest sort (default)
        $response = $this->get(route('forums.show', [
            'forum' => $forum,
            'sort' => 'latest'
        ]));

        $response->assertStatus(200);

        // Test oldest sort
        $response = $this->get(route('forums.show', [
            'forum' => $forum,
            'sort' => 'oldest'
        ]));

        $response->assertStatus(200);
    }

    public function test_forum_statistics_are_cached(): void
    {
        $forum = Forum::factory()->create();
        Thread::factory(5)->create(['forum_id' => $forum->id]);

        // First request should hit database
        $response1 = $this->get(route('forums.index'));
        $response1->assertStatus(200);

        // Second request should use cache
        $response2 = $this->get(route('forums.index'));
        $response2->assertStatus(200);

        // Verify cache exists
        $this->assertNotNull(Cache::get('forums.categories'));
        $this->assertNotNull(Cache::get('forums.stats'));
    }

    public function test_forum_cache_middleware_shares_global_stats(): void
    {
        User::factory(10)->create();
        Forum::factory(3)->create();

        $response = $this->get(route('forums.index'));

        $response->assertStatus(200);
        $response->assertViewHas('globalForumStats');

        $globalStats = $response->viewData('globalForumStats');
        $this->assertArrayHasKey('total_users', $globalStats);
        $this->assertArrayHasKey('total_forums', $globalStats);
    }

    public function test_forum_search_highlights_results(): void
    {
        $forum = Forum::factory()->create();
        $thread = Thread::factory()->create([
            'title' => 'Mechanical Design Principles',
            'body' => 'This thread discusses mechanical design fundamentals',
            'forum_id' => $forum->id,
        ]);

        $response = $this->get(route('forums.search', ['q' => 'mechanical']));

        $response->assertStatus(200);
        $response->assertSee('Mechanical Design Principles');
    }

    public function test_helper_functions_work_correctly(): void
    {
        // Test formatNumber helper
        $this->assertEquals('1K', formatNumber(1000));
        $this->assertEquals('1.5K', formatNumber(1500));
        $this->assertEquals('1M', formatNumber(1000000));
        $this->assertEquals('999', formatNumber(999));

        // Test getForumIcon helper
        $this->assertEquals('fas fa-drafting-compass', getForumIcon('Design Forum'));
        $this->assertEquals('fas fa-industry', getForumIcon('Manufacturing Hub'));
        $this->assertEquals('fas fa-comments', getForumIcon('General Discussion'));

        // Test highlightSearchQuery helper
        $text = 'This is a test about CAD software';
        $highlighted = highlightSearchQuery($text, 'CAD');
        $this->assertStringContainsString('<span class="highlight">CAD</span>', $highlighted);
    }

    public function test_cache_clear_command_works(): void
    {
        // Set some cache
        Cache::put('forums.categories', 'test_data', 3600);
        Cache::put('forums.stats', 'test_stats', 3600);

        // Run clear command
        $this->artisan('forum:clear-cache')
            ->expectsOutput('🧹 Clearing Forum Cache...')
            ->expectsOutput('✅ Forum cache cleared successfully!')
            ->assertExitCode(0);

        // Verify cache is cleared
        $this->assertNull(Cache::get('forums.categories'));
        $this->assertNull(Cache::get('forums.stats'));
    }

    public function test_forum_breadcrumb_navigation(): void
    {
        $forum = Forum::factory()->create(['title' => 'Test Forum']);

        $response = $this->get(route('forums.show', $forum));

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Forums');
        $response->assertSee('Test Forum');
    }
}
