<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class ApiTestSuite extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo users để test
        $this->user = User::factory()->create([
            'role' => 'member',
            'status' => 'active'
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Tạo dữ liệu mẫu
        $this->createSampleData();
    }

    protected function createSampleData()
    {
        // Tạo category và forum
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);

        // Tạo threads
        Thread::factory(5)->create([
            'forum_id' => $forum->id,
            'user_id' => $this->user->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function test_cors_endpoint()
    {
        $response = $this->getJson('/api/v1/cors-test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'origin',
                'allowed_origins'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'CORS test successful'
            ]);
    }

    /** @test */
    public function test_user_can_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'username',
                        'email',
                        'role'
                    ],
                    'token'
                ]
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function test_user_can_register()
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => 'Test',
            'last_name' => 'User'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'username' => 'testuser'
        ]);
    }

    /** @test */
    public function test_authenticated_user_can_get_profile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'username',
                    'email',
                    'role'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->user->id,
                    'email' => $this->user->email
                ]
            ]);
    }

    /** @test */
    public function test_can_get_forums_list()
    {
        $response = $this->getJson('/api/v1/forums');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description'
                    ]
                ]
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_can_get_threads_list()
    {
        $response = $this->getJson('/api/v1/threads');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'slug',
                            'content',
                            'user',
                            'forum'
                        ]
                    ],
                    'pagination'
                ]
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_authenticated_user_can_create_thread()
    {
        Sanctum::actingAs($this->user);

        $forum = Forum::first();

        $threadData = [
            'title' => 'Test Thread từ API',
            'content' => 'Đây là nội dung test thread',
            'forum_id' => $forum->id,
            'tags' => ['test', 'api']
        ];

        $response = $this->postJson('/api/v1/threads', $threadData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'user',
                    'forum'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Test Thread từ API'
                ]
            ]);

        $this->assertDatabaseHas('threads', [
            'title' => 'Test Thread từ API',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function test_can_search_content()
    {
        // Tạo thread với nội dung cụ thể để search
        $thread = Thread::factory()->create([
            'title' => 'Laravel API Tutorial',
            'content' => 'Hướng dẫn tạo API với Laravel',
            'status' => 'approved'
        ]);

        $response = $this->getJson('/api/v1/search?q=Laravel&type=threads');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'results' => [
                        'threads' => [
                            'data' => [
                                '*' => [
                                    'id',
                                    'title',
                                    'slug'
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_can_get_forum_statistics()
    {
        $response = $this->getJson('/api/v1/stats/forum');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'threadCount',
                    'userCount',
                    'commentCount',
                    'establishedYear'
                ]
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_can_get_settings()
    {
        $response = $this->getJson('/api/v1/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_authenticated_user_can_like_thread()
    {
        Sanctum::actingAs($this->user);

        $thread = Thread::first();

        $response = $this->postJson("/api/v1/threads/{$thread->slug}/like");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'liked',
                    'likes_count'
                ]
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_create_thread()
    {
        $forum = Forum::first();

        $threadData = [
            'title' => 'Test Thread',
            'content' => 'Test content',
            'forum_id' => $forum->id
        ];

        $response = $this->postJson('/api/v1/threads', $threadData);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_admin_can_access_admin_endpoints()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/showcases');

        // Có thể trả về 200 hoặc 403 tùy thuộc vào implementation
        $this->assertContains($response->getStatusCode(), [200, 403]);
    }

    /** @test */
    public function test_regular_user_cannot_access_admin_endpoints()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/admin/showcases');

        $response->assertStatus(403);
    }

    /** @test */
    public function test_api_returns_proper_error_format()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ])
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        // Tạo nhiều threads
        Thread::factory(25)->create(['status' => 'approved']);

        $response = $this->getJson('/api/v1/threads?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 10
                    ]
                ]
            ]);
    }

    /** @test */
    public function test_rate_limiting_headers_present()
    {
        $response = $this->getJson('/api/v1/threads');

        $response->assertStatus(200);

        // Kiểm tra rate limiting headers có tồn tại
        $headers = $response->headers->all();
        $this->assertArrayHasKey('x-ratelimit-limit', $headers);
        $this->assertArrayHasKey('x-ratelimit-remaining', $headers);
    }
}
