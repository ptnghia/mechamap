<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Showcase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowcaseCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test Engineer',
            'email' => 'test@mechamap.com',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function user_can_access_showcase_create_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('showcase.create'));

        $response->assertStatus(200);
        $response->assertViewIs('showcase.create');
        $response->assertSee('Tạo Showcase Mới');
    }

    /** @test */
    public function user_can_create_independent_showcase()
    {
        Storage::fake('public');

        $showcaseData = [
            'title' => 'Advanced Robotics Assembly Line',
            'description' => 'A cutting-edge robotics assembly line for automotive manufacturing with AI-powered quality control and real-time monitoring systems.',
            'location' => 'Ho Chi Minh City, Vietnam',
            'usage' => 'Automotive manufacturing and quality control',
            'floors' => 3,
            'category' => 'automation',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('showcase.store'), $showcaseData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify showcase was created
        $this->assertDatabaseHas('showcases', [
            'title' => $showcaseData['title'],
            'user_id' => $this->user->id,
            'showcaseable_type' => null, // Independent showcase
            'showcaseable_id' => null,
            'status' => 'approved',
        ]);

        $showcase = Showcase::where('title', $showcaseData['title'])->first();
        $this->assertTrue($showcase->isIndependent());
        $this->assertEquals('Original Showcase', $showcase->getTypeDisplayName());
    }

    /** @test */
    public function user_can_create_showcase_with_file_upload()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('project.jpg', 800, 600);

        $showcaseData = [
            'title' => 'CNC Machining Center Project',
            'description' => 'High-precision CNC machining center for aerospace components.',
            'location' => 'Hanoi, Vietnam',
            'usage' => 'Aerospace component manufacturing',
            'floors' => 2,
            'category' => 'manufacturing',
            'cover_image' => $file,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('showcase.store'), $showcaseData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify file was uploaded
        $showcase = Showcase::where('title', $showcaseData['title'])->first();
        $this->assertNotNull($showcase->cover_image);
        Storage::disk('public')->assertExists($showcase->cover_image);
    }

    /** @test */
    public function showcase_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post(route('showcase.store'), []);

        $response->assertSessionHasErrors(['title', 'description']);
    }

    /** @test */
    public function showcase_title_must_be_unique_slug()
    {
        // Create first showcase
        Showcase::create([
            'user_id' => $this->user->id,
            'title' => 'Test Project',
            'slug' => 'test-project',
            'description' => 'First test project',
            'status' => 'approved',
        ]);

        // Create second showcase with same title
        $showcaseData = [
            'title' => 'Test Project',
            'description' => 'Second test project with same title',
            'location' => 'Vietnam',
            'usage' => 'Testing',
            'category' => 'test',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('showcase.store'), $showcaseData);

        $response->assertRedirect();

        // Check that slug was made unique
        $secondShowcase = Showcase::where('title', $showcaseData['title'])
            ->where('description', $showcaseData['description'])
            ->first();

        $this->assertNotEquals('test-project', $secondShowcase->slug);
        $this->assertStringStartsWith('test-project-', $secondShowcase->slug);
    }

    /** @test */
    public function guest_cannot_create_showcase()
    {
        $response = $this->get(route('showcase.create'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('showcase.store'), [
            'title' => 'Test Project',
            'description' => 'Test description',
        ]);
        $response->assertRedirect(route('login'));
    }
}
