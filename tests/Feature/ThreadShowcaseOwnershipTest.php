<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thread;
use App\Models\Showcase;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadShowcaseOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected $threadOwner;
    protected $otherUser;
    protected $thread;
    protected $category;
    protected $forum;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->threadOwner = User::factory()->create([
            'name' => 'Thread Owner',
            'email' => 'owner@mechamap.com',
        ]);

        $this->otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@mechamap.com',
        ]);

        // Create category and forum
        $this->category = Category::factory()->create();
        $this->forum = Forum::factory()->create(['category_id' => $this->category->id]);

        // Create thread
        $this->thread = Thread::factory()->create([
            'user_id' => $this->threadOwner->id,
            'category_id' => $this->category->id,
            'forum_id' => $this->forum->id,
            'status' => 'published',
        ]);
    }

    /** @test */
    public function thread_with_valid_showcase_ownership_should_display()
    {
        // Create showcase by thread owner
        $showcase = Showcase::factory()->create([
            'user_id' => $this->threadOwner->id,
            'showcaseable_type' => Thread::class,
            'showcaseable_id' => $this->thread->id,
        ]);

        $this->assertTrue($this->thread->fresh()->hasValidShowcaseOwnership());
        $this->assertTrue($this->thread->fresh()->shouldDisplayShowcase());
    }

    /** @test */
    public function thread_with_invalid_showcase_ownership_should_not_display()
    {
        // Create showcase by different user (security issue)
        $showcase = Showcase::factory()->create([
            'user_id' => $this->otherUser->id, // Different user!
            'showcaseable_type' => Thread::class,
            'showcaseable_id' => $this->thread->id,
        ]);

        $this->assertFalse($this->thread->fresh()->hasValidShowcaseOwnership());
        $this->assertFalse($this->thread->fresh()->shouldDisplayShowcase());
    }

    /** @test */
    public function thread_without_showcase_should_not_display()
    {
        $this->assertFalse($this->thread->hasValidShowcaseOwnership());
        $this->assertFalse($this->thread->shouldDisplayShowcase());
    }

    /** @test */
    public function thread_show_page_should_not_display_invalid_showcase()
    {
        // Create showcase by different user
        $showcase = Showcase::factory()->create([
            'user_id' => $this->otherUser->id,
            'showcaseable_type' => Thread::class,
            'showcaseable_id' => $this->thread->id,
        ]);

        $response = $this->get(route('threads.show', $this->thread));

        $response->assertStatus(200);
        // Should not see showcase section
        $response->assertDontSee('Related Showcases');
        $response->assertDontSee($showcase->title);
    }

    /** @test */
    public function thread_show_page_should_display_valid_showcase()
    {
        // Create showcase by thread owner
        $showcase = Showcase::factory()->create([
            'user_id' => $this->threadOwner->id,
            'showcaseable_type' => Thread::class,
            'showcaseable_id' => $this->thread->id,
            'title' => 'Valid Showcase Title',
        ]);

        $response = $this->get(route('threads.show', $this->thread));

        $response->assertStatus(200);
        // Should see showcase section
        $response->assertSee('Related Showcases');
        $response->assertSee('Valid Showcase Title');
    }

    /** @test */
    public function user_can_create_showcase_validation()
    {
        // Thread owner can create showcase
        $this->assertTrue($this->thread->userCanCreateShowcase($this->threadOwner));
        
        // Other user cannot create showcase
        $this->assertFalse($this->thread->userCanCreateShowcase($this->otherUser));
        
        // Null user cannot create showcase
        $this->assertFalse($this->thread->userCanCreateShowcase(null));
    }

    /** @test */
    public function thread_can_create_showcase_validation()
    {
        // Thread without showcase can create one
        $this->assertTrue($this->thread->canCreateShowcase());
        
        // Thread with showcase cannot create another
        Showcase::factory()->create([
            'user_id' => $this->threadOwner->id,
            'showcaseable_type' => Thread::class,
            'showcaseable_id' => $this->thread->id,
        ]);
        
        $this->assertFalse($this->thread->fresh()->canCreateShowcase());
    }
}
