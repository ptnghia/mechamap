<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Services\ProductImageValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class ProductImageValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ProductImageValidationService $service;
    private MarketplaceSeller $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProductImageValidationService::class);
        
        // Create a test seller
        $this->seller = MarketplaceSeller::factory()->create();
    }

    /** @test */
    public function it_detects_products_without_featured_images()
    {
        // Create products without featured images
        MarketplaceProduct::factory()->count(3)->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null
        ]);

        $stats = $this->service->getValidationStats();

        $this->assertEquals(3, $stats['products_without_featured']);
    }

    /** @test */
    public function it_detects_products_without_images_array()
    {
        // Create products without images array
        MarketplaceProduct::factory()->count(2)->create([
            'seller_id' => $this->seller->id,
            'images' => null
        ]);

        $stats = $this->service->getValidationStats();

        $this->assertEquals(2, $stats['products_without_images']);
    }

    /** @test */
    public function it_validates_and_fixes_product_with_missing_featured_image()
    {
        // Create a product without featured image
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => ['existing-image.jpg']
        ]);

        $changes = $this->service->validateAndFixProductImages($product, false);

        $this->assertArrayHasKey('featured_image', $changes);
        $this->assertNotEmpty($changes['featured_image']);
        $this->assertStringContains('images/', $changes['featured_image']);
    }

    /** @test */
    public function it_validates_and_fixes_product_with_missing_images_array()
    {
        // Create a product without images array
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => 'existing-featured.jpg',
            'images' => null
        ]);

        $changes = $this->service->validateAndFixProductImages($product, false);

        $this->assertArrayHasKey('images', $changes);
        $this->assertIsArray($changes['images']);
        $this->assertGreaterThan(0, count($changes['images']));
    }

    /** @test */
    public function it_does_not_change_products_with_valid_images()
    {
        // Create a product with valid images (using existing showcase images)
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => 'images/showcase/Mechanical-Engineering.jpg',
            'images' => [
                'images/showcase/DesignEngineer.jpg',
                'images/threads/Mechanical-Engineer-1-1024x536.webp'
            ]
        ]);

        $changes = $this->service->validateAndFixProductImages($product, false);

        $this->assertEmpty($changes);
    }

    /** @test */
    public function it_validates_uploaded_image_file()
    {
        // Create a valid test image
        $file = UploadedFile::fake()->image('test.jpg', 500, 500)->size(1000);

        $errors = $this->service->validateUploadedImage($file);

        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_rejects_invalid_image_file()
    {
        // Create an invalid file (too small)
        $file = UploadedFile::fake()->image('test.jpg', 50, 50);

        $errors = $this->service->validateUploadedImage($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContains('dimensions too small', implode(' ', $errors));
    }

    /** @test */
    public function it_rejects_oversized_image_file()
    {
        // Create an oversized file
        $file = UploadedFile::fake()->image('test.jpg', 1000, 1000)->size(6000); // 6MB

        $errors = $this->service->validateUploadedImage($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContains('File size too large', implode(' ', $errors));
    }

    /** @test */
    public function it_rejects_invalid_file_extension()
    {
        // Create a file with invalid extension
        $file = UploadedFile::fake()->create('test.txt', 1000);

        $errors = $this->service->validateUploadedImage($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContains('Invalid file extension', implode(' ', $errors));
    }

    /** @test */
    public function it_processes_uploaded_image_successfully()
    {
        // Create a valid test image
        $file = UploadedFile::fake()->image('test.jpg', 500, 500);

        $relativePath = $this->service->processUploadedImage($file, 'test');

        $this->assertStringStartsWith('images/test/', $relativePath);
        $this->assertStringEndsWith('.jpg', $relativePath);
        
        // Check if file was actually created
        $fullPath = public_path($relativePath);
        $this->assertTrue(File::exists($fullPath));
        
        // Clean up
        File::delete($fullPath);
    }

    /** @test */
    public function it_provides_comprehensive_statistics()
    {
        // Create various types of products
        MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => null
        ]);

        MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => 'images/showcase/existing.jpg',
            'images' => ['images/showcase/existing.jpg']
        ]);

        $stats = $this->service->getValidationStats();

        $this->assertArrayHasKey('total_products', $stats);
        $this->assertArrayHasKey('products_without_featured', $stats);
        $this->assertArrayHasKey('products_with_broken_featured', $stats);
        $this->assertArrayHasKey('products_without_images', $stats);
        $this->assertArrayHasKey('products_with_broken_images', $stats);
        $this->assertArrayHasKey('available_replacement_images', $stats);
        
        $this->assertEquals(2, $stats['total_products']);
        $this->assertEquals(1, $stats['products_without_featured']);
        $this->assertEquals(1, $stats['products_without_images']);
    }

    /** @test */
    public function it_handles_batch_validation_correctly()
    {
        // Create multiple products without images
        MarketplaceProduct::factory()->count(5)->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => null
        ]);

        $stats = $this->service->validateAndFixAllProducts(true); // dry run

        $this->assertEquals(5, $stats['total_processed']);
        $this->assertEquals(5, $stats['missing_featured_fixed']);
        $this->assertEquals(5, $stats['missing_images_fixed']);
        $this->assertEquals(0, $stats['errors']);
    }

    /** @test */
    public function command_shows_statistics_correctly()
    {
        // Create test products
        MarketplaceProduct::factory()->count(2)->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => null
        ]);

        $this->artisan('products:validate-images --stats')
            ->expectsOutput('📊 Getting image validation statistics...')
            ->assertExitCode(0);
    }

    /** @test */
    public function command_validates_single_product()
    {
        // Create a test product
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => null
        ]);

        $this->artisan("products:validate-images --product={$product->id} --dry-run")
            ->expectsOutput("🔍 Validating product #{$product->id}...")
            ->assertExitCode(0);
    }

    /** @test */
    public function command_handles_nonexistent_product()
    {
        $this->artisan('products:validate-images --product=99999')
            ->expectsOutput('❌ Product #99999 not found')
            ->assertExitCode(0);
    }

    /** @test */
    public function command_performs_dry_run_correctly()
    {
        // Create products without images
        MarketplaceProduct::factory()->count(2)->create([
            'seller_id' => $this->seller->id,
            'featured_image' => null,
            'images' => null
        ]);

        $this->artisan('products:validate-images --dry-run')
            ->expectsOutput('🔍 DRY RUN MODE - No changes will be made')
            ->expectsOutput('🔍 DRY RUN: 4 products would be fixed')
            ->assertExitCode(0);
    }
}
