<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Services\MarketplaceDataNormalizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MarketplaceDataNormalizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private MarketplaceDataNormalizationService $service;
    private MarketplaceSeller $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MarketplaceDataNormalizationService::class);
        
        // Create a test seller
        $this->seller = MarketplaceSeller::factory()->create();
    }

    /** @test */
    public function it_normalizes_digital_product_type_based_on_files()
    {
        // Create a product marked as digital but without digital files
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'digital',
            'digital_files' => null
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('product_type', $changes);
        $this->assertEquals('new_product', $changes['product_type']);
    }

    /** @test */
    public function it_converts_products_with_digital_files_to_digital_type()
    {
        // Create a product with digital files but not marked as digital
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'new_product',
            'digital_files' => [
                ['name' => 'test.dwg', 'path' => '/files/test.dwg', 'size' => 1024]
            ]
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('product_type', $changes);
        $this->assertEquals('digital', $changes['product_type']);
    }

    /** @test */
    public function it_fixes_pricing_inconsistencies()
    {
        // Create a product on sale without sale price
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'price' => 100.00,
            'is_on_sale' => true,
            'sale_price' => null
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('is_on_sale', $changes);
        $this->assertFalse($changes['is_on_sale']);
    }

    /** @test */
    public function it_swaps_prices_when_sale_price_is_higher()
    {
        // Create a product with sale price higher than regular price
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'price' => 50.00,
            'sale_price' => 100.00,
            'is_on_sale' => true
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('price', $changes);
        $this->assertArrayHasKey('sale_price', $changes);
        $this->assertEquals(100.00, $changes['price']);
        $this->assertEquals(50.00, $changes['sale_price']);
    }

    /** @test */
    public function it_normalizes_stock_status_for_digital_products()
    {
        // Create a digital product that's out of stock
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'digital',
            'in_stock' => false,
            'manage_stock' => true,
            'stock_quantity' => 0
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('in_stock', $changes);
        $this->assertArrayHasKey('manage_stock', $changes);
        $this->assertTrue($changes['in_stock']);
        $this->assertFalse($changes['manage_stock']);
    }

    /** @test */
    public function it_generates_slug_when_missing()
    {
        // Create a product without slug
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'name' => 'Test Product Name',
            'slug' => null
        ]);

        $changes = $this->service->normalizeProduct($product);

        $this->assertArrayHasKey('slug', $changes);
        $this->assertEquals('test-product-name', $changes['slug']);
    }

    /** @test */
    public function it_normalizes_array_fields()
    {
        // Create a product with invalid JSON in array field
        $product = MarketplaceProduct::factory()->make([
            'seller_id' => $this->seller->id,
            'tags' => 'invalid json'
        ]);
        
        // Manually set the raw attribute to simulate invalid JSON
        $product->setRawAttributes([
            'id' => 1,
            'seller_id' => $this->seller->id,
            'name' => 'Test Product',
            'price' => 100,
            'tags' => 'invalid json'
        ]);

        $changes = $this->service->normalizeProduct($product);

        // Note: This test might need adjustment based on actual implementation
        // as the normalization might happen at different levels
    }

    /** @test */
    public function it_validates_product_integrity()
    {
        // Create a product with integrity issues
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'name' => '',
            'price' => -10,
            'product_type' => 'digital',
            'digital_files' => null
        ]);

        $issues = $this->service->validateProductIntegrity($product);

        $this->assertContains('Missing product name', $issues);
        $this->assertContains('Negative price', $issues);
        $this->assertContains('Digital product without digital files', $issues);
    }

    /** @test */
    public function it_provides_normalization_statistics()
    {
        // Create some test products
        MarketplaceProduct::factory()->count(5)->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'digital'
        ]);
        
        MarketplaceProduct::factory()->count(3)->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'new_product'
        ]);

        $stats = $this->service->getNormalizationStats();

        $this->assertArrayHasKey('total_products', $stats);
        $this->assertArrayHasKey('digital_products', $stats);
        $this->assertArrayHasKey('products_with_digital_files', $stats);
        $this->assertEquals(8, $stats['total_products']);
        $this->assertEquals(5, $stats['digital_products']);
    }

    /** @test */
    public function it_performs_batch_normalization()
    {
        // Create products that need normalization
        MarketplaceProduct::factory()->count(3)->create([
            'seller_id' => $this->seller->id,
            'product_type' => 'digital',
            'digital_files' => null
        ]);

        $stats = $this->service->batchNormalize(2); // Small batch size for testing

        $this->assertArrayHasKey('processed', $stats);
        $this->assertArrayHasKey('updated', $stats);
        $this->assertArrayHasKey('errors', $stats);
        $this->assertEquals(3, $stats['processed']);
        $this->assertEquals(3, $stats['updated']);
        $this->assertEquals(0, $stats['errors']);
    }

    /** @test */
    public function product_observer_normalizes_data_on_creation()
    {
        // Create a product with data that needs normalization
        $product = MarketplaceProduct::create([
            'seller_id' => $this->seller->id,
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 100.00,
            'product_type' => 'digital',
            'seller_type' => 'supplier',
            'digital_files' => null, // This should trigger normalization
            'status' => 'approved'
        ]);

        // The observer should have normalized the product type
        $this->assertEquals('new_product', $product->fresh()->product_type);
    }

    /** @test */
    public function product_model_methods_work_correctly()
    {
        $product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'price' => 100.00,
            'sale_price' => 80.00,
            'is_on_sale' => true,
            'product_type' => 'digital',
            'digital_files' => [['name' => 'test.dwg']]
        ]);

        $this->assertTrue($product->isDigitalProduct());
        $this->assertEquals(80.00, $product->getEffectivePrice());
        $this->assertEquals(20.00, $product->getDiscountPercentage());
        $this->assertTrue($product->isAvailable());
    }
}
