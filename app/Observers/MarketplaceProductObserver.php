<?php

namespace App\Observers;

use App\Models\MarketplaceProduct;
use App\Services\MarketplaceDataNormalizationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MarketplaceProductObserver
{
    private MarketplaceDataNormalizationService $normalizationService;

    public function __construct(MarketplaceDataNormalizationService $normalizationService)
    {
        $this->normalizationService = $normalizationService;
    }

    /**
     * Handle the MarketplaceProduct "creating" event.
     */
    public function creating(MarketplaceProduct $product): void
    {
        // Generate UUID if not provided
        if (empty($product->uuid)) {
            $product->uuid = Str::uuid();
        }

        // Generate slug if not provided
        if (empty($product->slug) && !empty($product->name)) {
            $baseSlug = Str::slug($product->name);
            $product->slug = $this->generateUniqueSlug($baseSlug);
        }

        // Generate SKU if not provided
        if (empty($product->sku)) {
            $product->sku = $this->generateUniqueSku();
        }

        // Auto-normalize data before creation
        $this->normalizeProductData($product);
    }

    /**
     * Handle the MarketplaceProduct "updating" event.
     */
    public function updating(MarketplaceProduct $product): void
    {
        // Regenerate slug if name changed
        if ($product->isDirty('name') && !$product->isDirty('slug')) {
            $baseSlug = Str::slug($product->name);
            $product->slug = $this->generateUniqueSlug($baseSlug, $product->id);
        }

        // Auto-normalize data before update
        $this->normalizeProductData($product);
    }

    /**
     * Handle the MarketplaceProduct "created" event.
     */
    public function created(MarketplaceProduct $product): void
    {
        Log::info("MarketplaceProduct created", [
            'id' => $product->id,
            'name' => $product->name,
            'product_type' => $product->product_type,
            'seller_type' => $product->seller_type
        ]);
    }

    /**
     * Handle the MarketplaceProduct "updated" event.
     */
    public function updated(MarketplaceProduct $product): void
    {
        // Log significant changes
        $changes = $product->getChanges();
        $significantFields = ['status', 'is_active', 'price', 'sale_price', 'product_type'];
        
        $significantChanges = array_intersect_key($changes, array_flip($significantFields));
        
        if (!empty($significantChanges)) {
            Log::info("MarketplaceProduct updated with significant changes", [
                'id' => $product->id,
                'changes' => $significantChanges
            ]);
        }
    }

    /**
     * Handle the MarketplaceProduct "deleted" event.
     */
    public function deleted(MarketplaceProduct $product): void
    {
        Log::info("MarketplaceProduct deleted", [
            'id' => $product->id,
            'name' => $product->name
        ]);
    }

    /**
     * Normalize product data
     */
    private function normalizeProductData(MarketplaceProduct $product): void
    {
        // Normalize digital product type
        $this->normalizeDigitalProductType($product);
        
        // Normalize pricing
        $this->normalizePricing($product);
        
        // Normalize stock status
        $this->normalizeStockStatus($product);
        
        // Normalize array fields
        $this->normalizeArrayFields($product);
    }

    /**
     * Normalize digital product type
     */
    private function normalizeDigitalProductType(MarketplaceProduct $product): void
    {
        // If product has digital files, ensure it's marked as digital
        if (!empty($product->digital_files) && is_array($product->digital_files) && count($product->digital_files) > 0) {
            if ($product->product_type !== 'digital') {
                $product->product_type = 'digital';
                Log::info("Auto-converted product to digital type (has digital files)", ['id' => $product->id ?? 'new']);
            }
        }
    }

    /**
     * Normalize pricing
     */
    private function normalizePricing(MarketplaceProduct $product): void
    {
        // Disable sale if no sale price
        if ($product->is_on_sale && empty($product->sale_price)) {
            $product->is_on_sale = false;
            Log::info("Auto-disabled sale (no sale price)", ['id' => $product->id ?? 'new']);
        }

        // Swap prices if sale price is higher
        if ($product->sale_price && $product->sale_price > $product->price) {
            $temp = $product->price;
            $product->price = $product->sale_price;
            $product->sale_price = $temp;
            Log::info("Auto-swapped prices (sale price was higher)", ['id' => $product->id ?? 'new']);
        }

        // Disable expired sales
        if ($product->is_on_sale && $product->sale_ends_at && $product->sale_ends_at < now()) {
            $product->is_on_sale = false;
            Log::info("Auto-disabled expired sale", ['id' => $product->id ?? 'new']);
        }
    }

    /**
     * Normalize stock status
     */
    private function normalizeStockStatus(MarketplaceProduct $product): void
    {
        // Digital products should always be in stock and not manage stock
        if ($product->product_type === 'digital') {
            $product->in_stock = true;
            $product->manage_stock = false;
            $product->stock_quantity = 0; // Not applicable for digital products
        }

        // Products with stock management but zero quantity should be out of stock
        if ($product->manage_stock && $product->stock_quantity <= 0) {
            $product->in_stock = false;
        }
    }

    /**
     * Normalize array fields
     */
    private function normalizeArrayFields(MarketplaceProduct $product): void
    {
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
            $value = $product->getAttribute($field);
            
            // Convert empty strings to empty arrays
            if ($value === '' || $value === null) {
                $product->setAttribute($field, []);
            }
            
            // Ensure valid arrays
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $product->setAttribute($field, $decoded);
                } else {
                    $product->setAttribute($field, []);
                    Log::warning("Invalid JSON in field {$field}, reset to empty array", ['id' => $product->id ?? 'new']);
                }
            }
        }
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
     * Generate unique SKU
     */
    private function generateUniqueSku(): string
    {
        do {
            $sku = 'MP-' . strtoupper(Str::random(8));
        } while (MarketplaceProduct::where('sku', $sku)->exists());

        return $sku;
    }
}
