<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadRating;
use App\Models\ThreadBookmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;

class ThreadQualityApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Thread $thread;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo user và thread test
        $this->user = User::factory()->create([
            'username' => 'testuser123',
            'role' => 'member'
        ]);

        // Tạo category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category for unit tests',
            'color' => '#3b82f6'
        ]);

        // Tạo thread
        $this->thread = Thread::create([
            'title' => 'Test Thread for Quality',
            'slug' => 'test-thread-for-quality',
            'content' => 'This is a test thread content.',
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function can_rate_thread()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/{$this->thread->slug}/rate", [
                'rating' => 4
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'rating',
                    'thread_stats' => [
                        'average_rating',
                        'rating_count'
                    ]
                ]
            ]);

        // Kiểm tra database
        $this->assertDatabaseHas('thread_ratings', [
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'rating' => 4
        ]);
    }

    /** @test */
    public function cannot_rate_thread_with_invalid_rating()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/{$this->thread->slug}/rate", [
                'rating' => 6 // Invalid: chỉ cho phép 1-5
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('rating');
    }

    /** @test */
    public function can_update_existing_rating()
    {
        // Tạo rating ban đầu
        ThreadRating::create([
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'rating' => 3
        ]);

        // Update rating
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/{$this->thread->slug}/rate", [
                'rating' => 5
            ]);

        $response->assertStatus(200);

        // Kiểm tra rating đã được update
        $this->assertDatabaseHas('thread_ratings', [
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'rating' => 5
        ]);

        // Đảm bảo chỉ có 1 record
        $this->assertEquals(1, ThreadRating::where([
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id
        ])->count());
    }

    /** @test */
    public function can_bookmark_thread()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/{$this->thread->slug}/bookmark", [
                'folder' => 'Favorite Tutorials'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'bookmark' => [
                        'id',
                        'folder',
                        'created_at'
                    ]
                ]
            ]);

        // Kiểm tra database
        $this->assertDatabaseHas('thread_bookmarks', [
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'folder' => 'Favorite Tutorials'
        ]);
    }

    /** @test */
    public function cannot_bookmark_same_thread_twice()
    {
        // Tạo bookmark đầu tiên
        ThreadBookmark::create([
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'folder' => 'Test Folder'
        ]);

        // Thử bookmark lại
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/{$this->thread->slug}/bookmark");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Thread đã được bookmark trước đó'
            ]);
    }

    /** @test */
    public function can_remove_bookmark()
    {
        // Tạo bookmark trước
        $bookmark = ThreadBookmark::create([
            'user_id' => $this->user->id,
            'thread_id' => $this->thread->id,
            'folder' => 'Test Folder'
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/threads/{$this->thread->slug}/bookmark");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Bookmark đã được xóa'
            ]);

        // Kiểm tra bookmark đã bị xóa
        $this->assertDatabaseMissing('thread_bookmarks', [
            'id' => $bookmark->id
        ]);
    }

    /** @test */
    public function can_get_user_bookmarks()
    {
        // Tạo một số bookmarks
        $threads = Thread::factory(3)->create();
        foreach ($threads as $thread) {
            ThreadBookmark::create([
                'user_id' => $this->user->id,
                'thread_id' => $thread->id,
                'folder' => 'Test Folder'
            ]);
        }

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/user/bookmarks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'bookmarks' => [
                        'data' => [
                            '*' => [
                                'id',
                                'folder',
                                'created_at',
                                'thread' => [
                                    'id',
                                    'title',
                                    'slug',
                                    'user'
                                ]
                            ]
                        ],
                        'current_page',
                        'last_page',
                        'total'
                    ]
                ]
            ]);

        // Kiểm tra có đúng số bookmarks
        $this->assertEquals(3, $response->json('data.bookmarks.total'));
    }

    /** @test */
    public function can_filter_bookmarks_by_folder()
    {
        // Tạo bookmarks trong folders khác nhau
        $thread1 = Thread::factory()->create();
        $thread2 = Thread::factory()->create();

        ThreadBookmark::create([
            'user_id' => $this->user->id,
            'thread_id' => $thread1->id,
            'folder' => 'Laravel Tips'
        ]);

        ThreadBookmark::create([
            'user_id' => $this->user->id,
            'thread_id' => $thread2->id,
            'folder' => 'Vue.js Tutorials'
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/user/bookmarks?folder=Laravel Tips');

        $response->assertStatus(200);

        $bookmarks = $response->json('data.bookmarks.data');
        $this->assertCount(1, $bookmarks);
        $this->assertEquals('Laravel Tips', $bookmarks[0]['folder']);
    }

    /** @test */
    public function guest_cannot_access_quality_endpoints()
    {
        // Test rating endpoint
        $response = $this->postJson("/api/threads/{$this->thread->slug}/rate", [
            'rating' => 4
        ]);
        $response->assertStatus(401);

        // Test bookmark endpoint
        $response = $this->postJson("/api/threads/{$this->thread->slug}/bookmark");
        $response->assertStatus(401);

        // Test user bookmarks endpoint
        $response = $this->getJson('/api/user/bookmarks');
        $response->assertStatus(401);
    }

    /** @test */
    public function thread_not_found_returns_404()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/threads/non-existent-thread/rate", [
                'rating' => 4
            ]);

        $response->assertStatus(404);
    }
}
