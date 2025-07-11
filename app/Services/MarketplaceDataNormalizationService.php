<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MarketplaceDataNormalizationService
{
    /**
     * Normalize a single product's data
     */
    public function normalizeProduct(MarketplaceProduct $product): array
    {
        $changes = [];

        // Normalize digital product type
        $digitalChanges = $this->normalizeDigitalProductType($product);
        if (!empty($digitalChanges)) {
            $changes = array_merge($changes, $digitalChanges);
        }

        // Normalize pricing
        $pricingChanges = $this->normalizePricing($product);
        if (!empty($pricingChanges)) {
            $changes = array_merge($changes, $pricingChanges);
        }

        // Normalize array fields
        $arrayChanges = $this->normalizeArrayFields($product);
        if (!empty($arrayChanges)) {
            $changes = array_merge($changes, $arrayChanges);
        }

        // Normalize slug
        $slugChanges = $this->normalizeSlug($product);
        if (!empty($slugChanges)) {
            $changes = array_merge($changes, $slugChanges);
        }

        // Normalize stock status
        $stockChanges = $this->normalizeStockStatus($product);
        if (!empty($stockChanges)) {
            $changes = array_merge($changes, $stockChanges);
        }

        return $changes;
    }

    /**
     * Normalize digital product type based on actual content
     */
    private function normalizeDigitalProductType(MarketplaceProduct $product): array
    {
        $changes = [];
        $hasDigitalFiles = false;

        // Check if product has digital files in JSON field
        if (!empty($product->digital_files) && is_array($product->digital_files)) {
            $hasDigitalFiles = count($product->digital_files) > 0;
        }

        // Check if product has digital files through Media relationship
        if (!$hasDigitalFiles) {
            $hasDigitalFiles = $product->digitalFiles()->exists();
        }

        // Normalize product type based on digital files presence
        if ($hasDigitalFiles && $product->product_type !== 'digital') {
            $changes['product_type'] = 'digital';
            Log::info("Product #{$product->id} converted to digital (has digital files)");
        } elseif (!$hasDigitalFiles && $product->product_type === 'digital') {
            $changes['product_type'] = 'new_product';
            Log::info("Product #{$product->id} converted from digital to new_product (no digital files)");
        }

        return $changes;
    }

    /**
     * Normalize pricing data
     */
    private function normalizePricing(MarketplaceProduct $product): array
    {
        $changes = [];

        // Fix products on sale without sale price
        if ($product->is_on_sale && empty($product->sale_price)) {
            $changes['is_on_sale'] = false;
            Log::info("Product #{$product->id} sale disabled (no sale price)");
        }

        // Fix products with sale price higher than regular price
        if ($product->sale_price && $product->sale_price > $product->price) {
            $changes['price'] = $product->sale_price;
            $changes['sale_price'] = $product->price;
            Log::info("Product #{$product->id} prices swapped (sale price was higher)");
        }

        // Ensure sale dates are logical
        if ($product->is_on_sale && $product->sale_ends_at && $product->sale_ends_at < now()) {
            $changes['is_on_sale'] = false;
            Log::info("Product #{$product->id} sale disabled (expired)");
        }

        return $changes;
    }

    /**
     * Normalize array fields to ensure valid JSON
     */
    private function normalizeArrayFields(MarketplaceProduct $product): array
    {
        $changes = [];
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
            $value = $product->getRawOriginal($field);
            
            if ($value !== null) {
                // If it's a string, try to decode it
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // Invalid JSON, reset to empty array
                        $changes[$field] = [];
                        Log::warning("Product #{$product->id} field {$field} had invalid JSON, reset to empty array");
                    }
                }
                
                // Ensure empty strings become empty arrays
                if ($value === '' || $value === '[]') {
                    $changes[$field] = [];
                }
            }
        }

        return $changes;
    }

    /**
     * Normalize slug
     */
    private function normalizeSlug(MarketplaceProduct $product): array
    {
        $changes = [];

        if (empty($product->slug)) {
            $baseSlug = Str::slug($product->name);
            $uniqueSlug = $this->generateUniqueSlug($baseSlug, $product->id);
            $changes['slug'] = $uniqueSlug;
            Log::info("Product #{$product->id} slug generated: {$uniqueSlug}");
        }

        return $changes;
    }

    /**
     * Normalize stock status
     */
    private function normalizeStockStatus(MarketplaceProduct $product): array
    {
        $changes = [];

        // Digital products should always be in stock
        if ($product->product_type === 'digital' && !$product->in_stock) {
            $changes['in_stock'] = true;
            $changes['manage_stock'] = false;
            Log::info("Product #{$product->id} stock status normalized for digital product");
        }

        // Products with stock management but zero quantity should be out of stock
        if ($product->manage_stock && $product->stock_quantity <= 0 && $product->in_stock) {
            $changes['in_stock'] = false;
            Log::info("Product #{$product->id} marked as out of stock (zero quantity)");
        }

        return $changes;
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
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
     * Batch normalize products
     */
    public function batchNormalize(int $batchSize = 100): array
    {
        $stats = [
            'processed' => 0,
            'updated' => 0,
            'errors' => 0
        ];

        MarketplaceProduct::chunk($batchSize, function ($products) use (&$stats) {
            foreach ($products as $product) {
                try {
                    $changes = $this->normalizeProduct($product);
                    
                    if (!empty($changes)) {
                        $product->update($changes);
                        $stats['updated']++;
                    }
                    
                    $stats['processed']++;
                    
                } catch (\Exception $e) {
                    Log::error("Error normalizing product #{$product->id}: " . $e->getMessage());
                    $stats['errors']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Validate product data integrity
     */
    public function validateProductIntegrity(MarketplaceProduct $product): array
    {
        $issues = [];

        // Check required fields
        if (empty($product->name)) {
            $issues[] = 'Missing product name';
        }

        if (empty($product->slug)) {
            $issues[] = 'Missing product slug';
        }

        if ($product->price < 0) {
            $issues[] = 'Negative price';
        }

        // Check digital product consistency
        $hasDigitalFiles = !empty($product->digital_files) || $product->digitalFiles()->exists();
        if ($product->product_type === 'digital' && !$hasDigitalFiles) {
            $issues[] = 'Digital product without digital files';
        }

        // Check pricing logic
        if ($product->is_on_sale && empty($product->sale_price)) {
            $issues[] = 'Product on sale without sale price';
        }

        if ($product->sale_price && $product->sale_price > $product->price) {
            $issues[] = 'Sale price higher than regular price';
        }

        // Check stock logic
        if ($product->manage_stock && $product->stock_quantity < 0) {
            $issues[] = 'Negative stock quantity';
        }

        return $issues;
    }

    /**
     * Get normalization statistics
     */
    public function getNormalizationStats(): array
    {
        return [
            'total_products' => MarketplaceProduct::count(),
            'digital_products' => MarketplaceProduct::where('product_type', 'digital')->count(),
            'products_with_digital_files' => MarketplaceProduct::whereNotNull('digital_files')
                ->where('digital_files', '!=', '[]')->count(),
            'products_on_sale' => MarketplaceProduct::where('is_on_sale', true)->count(),
            'products_without_slugs' => MarketplaceProduct::whereNull('slug')->orWhere('slug', '')->count(),
            'out_of_stock_products' => MarketplaceProduct::where('in_stock', false)->count(),
            'products_with_issues' => $this->countProductsWithIssues(),
        ];
    }

    /**
     * Count products with data integrity issues
     */
    private function countProductsWithIssues(): int
    {
        $count = 0;
        
        MarketplaceProduct::chunk(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                $issues = $this->validateProductIntegrity($product);
                if (!empty($issues)) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
