<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\Post;

class HomeEnhancedTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test enhanced home page loads successfully.
     */
    public function test_enhanced_home_page_loads_successfully()
    {
        $response = $this->get('/home-new');
        
        $response->assertStatus(200);
        $response->assertViewIs('home-new');
    }

    /**
     * Test home page contains required sections.
     */
    public function test_home_page_contains_required_sections()
    {
        $response = $this->get('/home-new');
        
        $response->assertSee('Cộng đồng Kỹ thuật Cơ khí');
        $response->assertSee('Tại sao chọn MechaMap?');
        $response->assertSee('Thao tác nhanh');
        $response->assertSee('Thảo luận mới nhất');
        $response->assertSee('Được tin tưởng bởi');
    }

    /**
     * Test live activity API endpoint.
     */
    public function test_live_activity_api_returns_json()
    {
        // Create test data
        $user = User::factory()->create();
        $forum = Forum::factory()->create();
        $thread = Thread::factory()->create([
            'user_id' => $user->id,
            'forum_id' => $forum->id,
        ]);

        $response = $this->get('/api/live-activity');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'type',
                'user',
                'action',
                'title',
                'time',
                'avatar'
            ]
        ]);
    }

    /**
     * Test newsletter subscription API.
     */
    public function test_newsletter_subscription_api()
    {
        $email = $this->faker->email;
        
        $response = $this->post('/api/newsletter-subscribe', [
            'email' => $email
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
    }

    /**
     * Test newsletter subscription validation.
     */
    public function test_newsletter_subscription_validation()
    {
        $response = $this->post('/api/newsletter-subscribe', [
            'email' => 'invalid-email'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test search suggestions API.
     */
    public function test_search_suggestions_api()
    {
        // Create test data
        $user = User::factory()->create();
        $forum = Forum::factory()->create(['name' => 'CAD Software']);
        $thread = Thread::factory()->create([
            'title' => 'SolidWorks Tutorial',
            'user_id' => $user->id,
            'forum_id' => $forum->id,
        ]);

        $response = $this->get('/api/search-suggestions?q=solid');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'threads' => [
                '*' => [
                    'id',
                    'title',
                    'view_count'
                ]
            ],
            'forums' => [
                '*' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);
    }

    /**
     * Test home page with authenticated user.
     */
    public function test_home_page_with_authenticated_user()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/home-new');
        
        $response->assertStatus(200);
        $response->assertSee('Tham gia thảo luận');
        $response->assertSee('Khám phá thị trường');
    }

    /**
     * Test home page with guest user.
     */
    public function test_home_page_with_guest_user()
    {
        $response = $this->get('/home-new');
        
        $response->assertStatus(200);
        $response->assertSee('Tham gia miễn phí');
        $response->assertSee('Khám phá cộng đồng');
    }

    /**
     * Test home page performance with cached data.
     */
    public function test_home_page_performance_with_cache()
    {
        // First request to populate cache
        $startTime = microtime(true);
        $this->get('/home-new');
        $firstRequestTime = microtime(true) - $startTime;

        // Second request should be faster due to caching
        $startTime = microtime(true);
        $this->get('/home-new');
        $secondRequestTime = microtime(true) - $startTime;

        // Second request should be significantly faster
        $this->assertLessThan($firstRequestTime * 0.8, $secondRequestTime);
    }

    /**
     * Test responsive design elements.
     */
    public function test_responsive_design_elements()
    {
        $response = $this->get('/home-new');
        
        // Check for responsive classes
        $response->assertSee('col-lg-');
        $response->assertSee('col-md-');
        $response->assertSee('d-none d-md-block');
    }

    /**
     * Test accessibility features.
     */
    public function test_accessibility_features()
    {
        $response = $this->get('/home-new');
        
        // Check for ARIA labels and semantic HTML
        $response->assertSee('role="button"');
        $response->assertSee('aria-label');
        $response->assertSee('<main');
        $response->assertSee('<section');
    }

    /**
     * Test SEO meta tags.
     */
    public function test_seo_meta_tags()
    {
        $response = $this->get('/home-new');
        
        $response->assertSee('<title>');
        $response->assertSee('meta name="description"');
        $response->assertSee('meta property="og:');
    }

    /**
     * Test JavaScript and CSS assets loading.
     */
    public function test_assets_loading()
    {
        $response = $this->get('/home-new');
        
        $response->assertSee('home-enhanced.css');
        $response->assertSee('home-enhanced.js');
        $response->assertSee('aos.css');
        $response->assertSee('aos.js');
    }

    /**
     * Test error handling for missing data.
     */
    public function test_error_handling_for_missing_data()
    {
        // Clear any existing data
        Thread::truncate();
        User::truncate();
        
        $response = $this->get('/home-new');
        
        $response->assertStatus(200);
        $response->assertSee('Chưa có thảo luận nào');
        $response->assertSee('Chưa có dữ liệu người đóng góp');
    }
}
