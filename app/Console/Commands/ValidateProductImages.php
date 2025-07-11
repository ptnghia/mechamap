<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductImageValidationService;
use Illuminate\Support\Facades\Log;

class ValidateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'products:validate-images 
                            {--dry-run : Run without making changes}
                            {--stats : Show statistics only}
                            {--product= : Validate specific product ID}';

    /**
     * The console command description.
     */
    protected $description = 'Validate and fix product images, replace missing images with random images from root directory';

    private ProductImageValidationService $imageService;

    /**
     * Create a new command instance.
     */
    public function __construct(ProductImageValidationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Product Image Validation and Replacement');
        $this->info('============================================');

        $dryRun = $this->option('dry-run');
        $statsOnly = $this->option('stats');
        $productId = $this->option('product');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        try {
            if ($statsOnly) {
                $this->showStatistics();
                return 0;
            }

            if ($productId) {
                $this->validateSingleProduct($productId, $dryRun);
                return 0;
            }

            $this->validateAllProducts($dryRun);
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during image validation: ' . $e->getMessage());
            Log::error('Product image validation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Show image validation statistics
     */
    private function showStatistics(): void
    {
        $this->info('📊 Getting image validation statistics...');
        
        $stats = $this->imageService->getValidationStats();
        
        $this->info('');
        $this->info('📈 Image Validation Statistics:');
        $this->info('==============================');
        $this->info("Total products: {$stats['total_products']}");
        $this->info("Products without featured image: {$stats['products_without_featured']}");
        $this->info("Products with broken featured image: {$stats['products_with_broken_featured']}");
        $this->info("Products without any images: {$stats['products_without_images']}");
        $this->info("Products with broken images: {$stats['products_with_broken_images']}");
        $this->info("Available replacement images: {$stats['available_replacement_images']}");
        
        // Calculate percentages
        $total = $stats['total_products'];
        if ($total > 0) {
            $this->info('');
            $this->info('📊 Percentages:');
            $this->info('===============');
            $withoutFeaturedPct = round(($stats['products_without_featured'] / $total) * 100, 1);
            $withoutImagesPct = round(($stats['products_without_images'] / $total) * 100, 1);
            $this->info("Products without featured image: {$withoutFeaturedPct}%");
            $this->info("Products without any images: {$withoutImagesPct}%");
        }
        
        // Show recommendations
        $this->info('');
        $this->info('💡 Recommendations:');
        $this->info('===================');
        
        if ($stats['products_without_featured'] > 0 || $stats['products_without_images'] > 0) {
            $this->warn("⚠️  {$stats['products_without_featured']} products need featured images");
            $this->warn("⚠️  {$stats['products_without_images']} products need image arrays");
            $this->info("✅ Run: php artisan products:validate-images --dry-run");
            $this->info("✅ Then: php artisan products:validate-images");
        } else {
            $this->info("✅ All products have valid images!");
        }
    }

    /**
     * Validate a single product
     */
    private function validateSingleProduct(int $productId, bool $dryRun): void
    {
        $this->info("🔍 Validating product #{$productId}...");
        
        $product = \App\Models\MarketplaceProduct::find($productId);
        if (!$product) {
            $this->error("❌ Product #{$productId} not found");
            return;
        }
        
        $this->info("Product: {$product->name}");
        $this->info("Current featured image: " . ($product->featured_image ?: 'None'));
        $this->info("Current images count: " . (empty($product->images) ? 0 : count($product->images)));
        
        $changes = $this->imageService->validateAndFixProductImages($product, $dryRun);
        
        if (empty($changes)) {
            $this->info("✅ Product #{$productId} images are valid - no changes needed");
        } else {
            $this->info("🔧 Changes needed for product #{$productId}:");
            
            foreach ($changes as $field => $value) {
                if ($field === 'featured_image') {
                    $this->info("  - Featured image: {$value}");
                } elseif ($field === 'images') {
                    $this->info("  - Images array: " . count($value) . " images assigned");
                }
            }
            
            if (!$dryRun) {
                $product->update($changes);
                $this->info("✅ Product #{$productId} updated successfully");
            }
        }
    }

    /**
     * Validate all products
     */
    private function validateAllProducts(bool $dryRun): void
    {
        $this->info('🔍 Validating all product images...');
        
        // Show initial statistics
        $initialStats = $this->imageService->getValidationStats();
        $this->info("Found {$initialStats['products_without_featured']} products without featured images");
        $this->info("Found {$initialStats['products_without_images']} products without image arrays");
        $this->info("Available {$initialStats['available_replacement_images']} replacement images from root directory");
        
        if ($initialStats['available_replacement_images'] === 0) {
            $this->error('❌ No replacement images found in root/images directory!');
            $this->info('💡 Please ensure you have images in the following directories:');
            $this->info('   - images/showcase/');
            $this->info('   - images/threads/');
            $this->info('   - images/category-forum/');
            return;
        }
        
        $this->info('');
        $this->info('🚀 Starting validation and replacement...');
        
        // Create progress bar
        $totalProducts = $initialStats['total_products'];
        $progressBar = $this->output->createProgressBar($totalProducts);
        $progressBar->setFormat('verbose');
        
        // Process products
        $stats = $this->imageService->validateAndFixAllProducts($dryRun);
        
        $progressBar->finish();
        $this->info('');
        
        // Show results
        $this->displayResults($stats, $dryRun);
        
        // Show final statistics
        if (!$dryRun) {
            $this->info('');
            $this->info('📊 Final Statistics:');
            $finalStats = $this->imageService->getValidationStats();
            $this->info("Products without featured images: {$finalStats['products_without_featured']}");
            $this->info("Products without image arrays: {$finalStats['products_without_images']}");
        }
    }

    /**
     * Display validation results
     */
    private function displayResults(array $stats, bool $dryRun): void
    {
        $this->info('');
        $this->info('📊 Validation Results:');
        $this->info('======================');
        $this->info("Total products processed: {$stats['total_processed']}");
        $this->info("Featured images fixed: {$stats['missing_featured_fixed']}");
        $this->info("Image arrays fixed: {$stats['missing_images_fixed']}");
        
        if ($stats['errors'] > 0) {
            $this->error("Errors encountered: {$stats['errors']}");
            $this->info("💡 Check logs for error details");
        }
        
        $totalFixed = $stats['missing_featured_fixed'] + $stats['missing_images_fixed'];
        
        if ($totalFixed > 0) {
            if ($dryRun) {
                $this->warn("🔍 DRY RUN: {$totalFixed} products would be fixed");
                $this->info("✅ Run without --dry-run to apply changes");
            } else {
                $this->info("✅ Successfully fixed {$totalFixed} products");
            }
        } else {
            $this->info("✅ All products already have valid images!");
        }
        
        $this->info('');
        $this->info('🎉 Image validation completed!');
    }
}
