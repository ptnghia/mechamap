<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarketplaceProduct;
use App\Models\TechnicalProduct;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NormalizeMarketplaceData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'marketplace:normalize-data 
                            {--dry-run : Run without making changes}
                            {--fix-digital : Fix digital product inconsistencies}
                            {--fix-arrays : Normalize array fields}
                            {--fix-slugs : Regenerate missing slugs}
                            {--fix-prices : Fix price inconsistencies}
                            {--all : Run all normalizations}';

    /**
     * The console command description.
     */
    protected $description = 'Normalize marketplace product data inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting Marketplace Data Normalization...');
        
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $stats = [
            'digital_fixed' => 0,
            'arrays_fixed' => 0,
            'slugs_fixed' => 0,
            'prices_fixed' => 0,
            'errors' => 0
        ];

        try {
            DB::beginTransaction();

            if ($this->option('fix-digital') || $this->option('all')) {
                $stats['digital_fixed'] = $this->fixDigitalProductInconsistencies($dryRun);
            }

            if ($this->option('fix-arrays') || $this->option('all')) {
                $stats['arrays_fixed'] = $this->normalizeArrayFields($dryRun);
            }

            if ($this->option('fix-slugs') || $this->option('all')) {
                $stats['slugs_fixed'] = $this->regenerateMissingSlugs($dryRun);
            }

            if ($this->option('fix-prices') || $this->option('all')) {
                $stats['prices_fixed'] = $this->fixPriceInconsistencies($dryRun);
            }

            if (!$dryRun) {
                DB::commit();
                $this->info('✅ All changes committed to database');
            } else {
                DB::rollBack();
                $this->info('🔍 Dry run completed - no changes made');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during normalization: ' . $e->getMessage());
            Log::error('Marketplace data normalization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $stats['errors']++;
        }

        $this->displayResults($stats);
        return 0;
    }

    /**
     * Fix digital product inconsistencies
     */
    private function fixDigitalProductInconsistencies($dryRun = false): int
    {
        $this->info('🔧 Fixing digital product inconsistencies...');
        
        $fixed = 0;

        // Find products marked as digital but without digital files
        $digitalWithoutFiles = MarketplaceProduct::where('product_type', 'digital')
            ->where(function($query) {
                $query->whereNull('digital_files')
                      ->orWhere('digital_files', '[]')
                      ->orWhere('digital_files', '');
            })
            ->get();

        $this->info("Found {$digitalWithoutFiles->count()} digital products without files");

        foreach ($digitalWithoutFiles as $product) {
            // Check if product has related digital files through Media model
            $hasDigitalFiles = $product->digitalFiles()->exists();
            
            if (!$hasDigitalFiles) {
                // Convert to new_product if no digital files found
                $this->warn("Converting product #{$product->id} from digital to new_product (no digital files)");
                
                if (!$dryRun) {
                    $product->update(['product_type' => 'new_product']);
                }
                $fixed++;
            }
        }

        // Find products with digital files but not marked as digital
        $nonDigitalWithFiles = MarketplaceProduct::where('product_type', '!=', 'digital')
            ->where(function($query) {
                $query->whereNotNull('digital_files')
                      ->where('digital_files', '!=', '[]')
                      ->where('digital_files', '!=', '');
            })
            ->get();

        $this->info("Found {$nonDigitalWithFiles->count()} non-digital products with digital files");

        foreach ($nonDigitalWithFiles as $product) {
            $this->warn("Converting product #{$product->id} to digital (has digital files)");
            
            if (!$dryRun) {
                $product->update(['product_type' => 'digital']);
            }
            $fixed++;
        }

        return $fixed;
    }

    /**
     * Normalize array fields
     */
    private function normalizeArrayFields($dryRun = false): int
    {
        $this->info('🔧 Normalizing array fields...');
        
        $fixed = 0;
        $arrayFields = [
            'technical_specs',
            'mechanical_properties', 
            'standards_compliance',
            'file_formats',
            'software_compatibility',
            'digital_files',
            'images',
            'attachments',
            'tags'
        ];

        foreach ($arrayFields as $field) {
            // Find products with invalid JSON in array fields
            $invalidProducts = MarketplaceProduct::whereNotNull($field)
                ->where($field, '!=', '[]')
                ->get()
                ->filter(function($product) use ($field) {
                    $value = $product->getRawOriginal($field);
                    if (is_string($value)) {
                        json_decode($value);
                        return json_last_error() !== JSON_ERROR_NONE;
                    }
                    return false;
                });

            if ($invalidProducts->count() > 0) {
                $this->warn("Found {$invalidProducts->count()} products with invalid JSON in {$field}");
                
                foreach ($invalidProducts as $product) {
                    if (!$dryRun) {
                        $product->update([$field => []]);
                    }
                    $fixed++;
                }
            }
        }

        return $fixed;
    }

    /**
     * Regenerate missing slugs
     */
    private function regenerateMissingSlugs($dryRun = false): int
    {
        $this->info('🔧 Regenerating missing slugs...');
        
        $productsWithoutSlugs = MarketplaceProduct::whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        $this->info("Found {$productsWithoutSlugs->count()} products without slugs");

        $fixed = 0;
        foreach ($productsWithoutSlugs as $product) {
            $baseSlug = Str::slug($product->name);
            $slug = $this->generateUniqueSlug($baseSlug, $product->id);
            
            $this->info("Generating slug for product #{$product->id}: {$slug}");
            
            if (!$dryRun) {
                $product->update(['slug' => $slug]);
            }
            $fixed++;
        }

        return $fixed;
    }

    /**
     * Fix price inconsistencies
     */
    private function fixPriceInconsistencies($dryRun = false): int
    {
        $this->info('🔧 Fixing price inconsistencies...');
        
        $fixed = 0;

        // Fix products on sale without sale price
        $onSaleWithoutPrice = MarketplaceProduct::where('is_on_sale', true)
            ->whereNull('sale_price')
            ->get();

        $this->info("Found {$onSaleWithoutPrice->count()} products on sale without sale price");

        foreach ($onSaleWithoutPrice as $product) {
            $this->warn("Disabling sale for product #{$product->id} (no sale price)");
            
            if (!$dryRun) {
                $product->update(['is_on_sale' => false]);
            }
            $fixed++;
        }

        // Fix products with sale price higher than regular price
        $salePriceHigher = MarketplaceProduct::whereColumn('sale_price', '>', 'price')
            ->get();

        $this->info("Found {$salePriceHigher->count()} products with sale price > regular price");

        foreach ($salePriceHigher as $product) {
            $this->warn("Swapping prices for product #{$product->id}");
            
            if (!$dryRun) {
                $regularPrice = $product->price;
                $salePrice = $product->sale_price;
                
                $product->update([
                    'price' => $salePrice,
                    'sale_price' => $regularPrice
                ]);
            }
            $fixed++;
        }

        return $fixed;
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($baseSlug, $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = MarketplaceProduct::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Display normalization results
     */
    private function displayResults($stats): void
    {
        $this->info('');
        $this->info('📊 Normalization Results:');
        $this->info('========================');
        $this->info("Digital products fixed: {$stats['digital_fixed']}");
        $this->info("Array fields fixed: {$stats['arrays_fixed']}");
        $this->info("Slugs regenerated: {$stats['slugs_fixed']}");
        $this->info("Price issues fixed: {$stats['prices_fixed']}");
        
        if ($stats['errors'] > 0) {
            $this->error("Errors encountered: {$stats['errors']}");
        }
        
        $total = $stats['digital_fixed'] + $stats['arrays_fixed'] + 
                $stats['slugs_fixed'] + $stats['prices_fixed'];
        
        $this->info("Total fixes applied: {$total}");
        $this->info('');
    }
}
