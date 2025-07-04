<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use App\Services\MarketplaceDownloadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MarketplaceDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $seller;
    protected $product;
    protected $order;
    protected $orderItem;
    protected $downloadService;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('private');
        Storage::fake('public');
        
        $this->downloadService = app(MarketplaceDownloadService::class);
        
        // Create test data
        $this->createTestData();
    }

    protected function createTestData()
    {
        // Create user
        $this->user = User::factory()->create([
            'role' => 'verified_partner',
            'email_verified_at' => now(),
        ]);

        // Create seller
        $this->seller = MarketplaceSeller::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        // Create category
        $category = ProductCategory::factory()->create();

        // Create digital product with files
        $this->product = MarketplaceProduct::factory()->create([
            'seller_id' => $this->seller->id,
            'product_category_id' => $category->id,
            'product_type' => 'digital',
            'seller_type' => 'manufacturer',
            'status' => 'approved',
            'digital_files' => [
                [
                    'name' => 'test-drawing.dwg',
                    'path' => 'marketplace/digital-files/test-drawing.dwg',
                    'size' => 1024000,
                    'mime_type' => 'application/acad',
                    'extension' => 'dwg',
                    'uploaded_at' => now()->toISOString(),
                ],
                [
                    'name' => 'manual.pdf',
                    'path' => 'marketplace/digital-files/manual.pdf',
                    'size' => 2048000,
                    'mime_type' => 'application/pdf',
                    'extension' => 'pdf',
                    'uploaded_at' => now()->toISOString(),
                ]
            ],
        ]);

        // Create order
        $this->order = MarketplaceOrder::factory()->create([
            'customer_id' => $this->user->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Create order item
        $this->orderItem = MarketplaceOrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'seller_id' => $this->seller->id,
        ]);

        // Create fake files
        Storage::disk('private')->put('marketplace/digital-files/test-drawing.dwg', 'fake dwg content');
        Storage::disk('private')->put('marketplace/digital-files/manual.pdf', 'fake pdf content');
    }

    /** @test */
    public function user_can_access_download_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('marketplace.downloads.index'));

        $response->assertStatus(200);
        $response->assertViewIs('marketplace.downloads.index');
    }

    /** @test */
    public function user_can_view_order_files()
    {
        $response = $this->actingAs($this->user)
            ->get(route('marketplace.downloads.order-files', $this->order));

        $response->assertStatus(200);
        $response->assertViewIs('marketplace.downloads.order-files');
        $response->assertViewHas('digitalItems');
    }

    /** @test */
    public function user_can_view_item_files()
    {
        $response = $this->actingAs($this->user)
            ->get(route('marketplace.downloads.item-files', $this->orderItem));

        $response->assertStatus(200);
        $response->assertViewIs('marketplace.downloads.item-files');
        $response->assertViewHas('files');
    }

    /** @test */
    public function user_can_generate_download_token()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('marketplace.downloads.generate-token'), [
                'order_item_id' => $this->orderItem->id,
                'file_index' => 0,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'download_url',
                'expires_at',
                'file_name',
                'file_size',
            ],
            'message'
        ]);
    }

    /** @test */
    public function user_can_download_file_with_valid_token()
    {
        // Generate token first
        $files = $this->downloadService->getDownloadableFiles($this->orderItem);
        $tokenData = $this->downloadService->generateDownloadToken($this->user, $this->orderItem, $files[0]);

        // Download file with token
        $response = $this->get(route('marketplace.download.file', ['token' => $tokenData['token']]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/acad');
    }

    /** @test */
    public function download_fails_with_invalid_token()
    {
        $response = $this->get(route('marketplace.download.file', ['token' => 'invalid-token']));

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthorized_user_cannot_access_other_user_files()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('marketplace.downloads.order-files', $this->order));

        $response->assertStatus(403);
    }

    /** @test */
    public function download_service_verifies_purchase_access()
    {
        $hasAccess = $this->downloadService->verifyPurchaseAccess($this->user, $this->orderItem);
        $this->assertTrue($hasAccess);

        // Test with unpaid order
        $this->order->update(['payment_status' => 'pending']);
        $hasAccess = $this->downloadService->verifyPurchaseAccess($this->user, $this->orderItem);
        $this->assertFalse($hasAccess);
    }

    /** @test */
    public function download_service_tracks_download_history()
    {
        $files = $this->downloadService->getDownloadableFiles($this->orderItem);
        
        $this->downloadService->trackDownload(
            $this->user,
            $this->orderItem,
            $files[0],
            '127.0.0.1',
            'Test User Agent',
            'test-token'
        );

        $this->assertDatabaseHas('marketplace_download_history', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'file_name' => 'test-drawing.dwg',
            'is_valid_download' => true,
        ]);
    }

    /** @test */
    public function download_service_gets_downloadable_files()
    {
        $files = $this->downloadService->getDownloadableFiles($this->orderItem);

        $this->assertCount(2, $files);
        $this->assertEquals('test-drawing.dwg', $files[0]['name']);
        $this->assertEquals('manual.pdf', $files[1]['name']);
    }

    /** @test */
    public function download_service_checks_user_permissions()
    {
        $result = $this->downloadService->canUserDownload($this->user, $this->orderItem);

        $this->assertTrue($result['can_download']);
        $this->assertStringContainsString('Có quyền tải xuống', $result['reason']);
    }

    /** @test */
    public function download_stats_api_returns_correct_data()
    {
        // Create some download history
        $files = $this->downloadService->getDownloadableFiles($this->orderItem);
        $this->downloadService->trackDownload(
            $this->user,
            $this->orderItem,
            $files[0],
            '127.0.0.1'
        );

        $response = $this->actingAs($this->user)
            ->get(route('marketplace.downloads.stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_downloads',
                'unique_products',
                'recent_downloads',
                'total_file_size',
            ]
        ]);
    }

    /** @test */
    public function product_model_methods_work_correctly()
    {
        $this->assertTrue($this->product->isDigitalProduct());
        $this->assertTrue($this->product->isPurchasedBy($this->user->id));
        
        $files = $this->product->getDownloadableFiles();
        $this->assertCount(2, $files);
        
        $accessCheck = $this->product->canUserDownload($this->user->id);
        $this->assertTrue($accessCheck['can_download']);
    }

    /** @test */
    public function file_helper_functions_work()
    {
        $this->assertEquals('fas fa-drafting-compass', getFileIcon('test.dwg'));
        $this->assertEquals('fas fa-file-pdf', getFileIcon('manual.pdf'));
        $this->assertEquals('1.00 MB', formatFileSize(1048576));
        $this->assertTrue(isCADFile('drawing.dwg'));
        $this->assertTrue(isDocumentFile('manual.pdf'));
    }

    /** @test */
    public function security_validation_prevents_unauthorized_access()
    {
        // Test with different user
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($otherUser)
            ->postJson(route('marketplace.downloads.generate-token'), [
                'order_item_id' => $this->orderItem->id,
                'file_index' => 0,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function token_expires_after_24_hours()
    {
        $files = $this->downloadService->getDownloadableFiles($this->orderItem);
        $tokenData = $this->downloadService->generateDownloadToken($this->user, $this->orderItem, $files[0]);

        // Simulate token expiration by manipulating cache
        cache()->forget("download_token:{$tokenData['token']}");

        $response = $this->get(route('marketplace.download.file', ['token' => $tokenData['token']]));
        $response->assertStatus(403);
    }
}
