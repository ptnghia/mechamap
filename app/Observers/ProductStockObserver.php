<?php

namespace App\Observers;

use App\Models\MarketplaceProduct;
use App\Services\ProductStockService;
use App\Services\PriceDropAlertService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductStockObserver
{
    /**
     * Handle the MarketplaceProduct "updating" event.
     * This fires before the model is saved, so we can capture the old values
     */
    public function updating(MarketplaceProduct $product): void
    {
        // Store original stock quantity in cache temporarily
        if ($product->isDirty('stock_quantity')) {
            $cacheKey = "product_original_stock_{$product->id}";
            Cache::put($cacheKey, $product->getOriginal('stock_quantity'), now()->addMinutes(5));
        }

        // Store original status for approval notifications
        if ($product->isDirty('status')) {
            $cacheKey = "product_original_status_{$product->id}";
            Cache::put($cacheKey, $product->getOriginal('status'), now()->addMinutes(5));
        }
    }

    /**
     * Handle the MarketplaceProduct "updated" event.
     * This fires after the model is saved, so we have the new values
     */
    public function updated(MarketplaceProduct $product): void
    {
        // Check if stock_quantity was changed
        if ($product->wasChanged('stock_quantity')) {
            $cacheKey = "product_original_stock_{$product->id}";
            $oldQuantity = Cache::pull($cacheKey, $product->getOriginal('stock_quantity'));
            $newQuantity = $product->stock_quantity;

            Log::info('Product stock quantity changed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'manage_stock' => $product->manage_stock
            ]);

            // Only handle stock changes if stock management is enabled
            if ($product->manage_stock) {
                ProductStockService::handleStockChange($product, $oldQuantity, $newQuantity);
            }
        }

        // Check if manage_stock was enabled and product is out of stock
        if ($product->wasChanged('manage_stock') && $product->manage_stock && $product->stock_quantity <= 0) {
            ProductStockService::handleStockChange($product, 1, 0); // Simulate going from 1 to 0
        }

        // Check if product status changed (approval/rejection)
        if ($product->wasChanged('status')) {
            $cacheKey = "product_original_status_{$product->id}";
            $oldStatus = Cache::pull($cacheKey, $product->getOriginal('status'));
            $newStatus = $product->status;

            Log::info('Product status changed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Handle product approval/rejection notifications
            \App\Services\MarketplaceNotificationService::handleProductApprovalStatusChange(
                $product,
                $oldStatus,
                $newStatus
            );
        }

        // Check if price changed
        if ($product->wasChanged(['price', 'sale_price', 'is_on_sale'])) {
            $oldPrice = $product->getOriginal('price') ?? $product->price;
            $newPrice = $product->price;
            $oldSalePrice = $product->getOriginal('sale_price');
            $newSalePrice = $product->sale_price;

            PriceDropAlertService::handlePriceChange(
                $product,
                $oldPrice,
                $newPrice,
                $oldSalePrice,
                $newSalePrice,
                'Product updated by seller'
            );
        }
    }

    /**
     * Handle the MarketplaceProduct "created" event.
     */
    public function created(MarketplaceProduct $product): void
    {
        // If product is created with 0 stock and stock management is enabled, handle as out of stock
        if ($product->manage_stock && $product->stock_quantity <= 0) {
            Log::info('Product created with zero stock', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => $product->stock_quantity
            ]);

            // Update in_stock status
            $product->update(['in_stock' => false]);
        }
    }

    /**
     * Handle the MarketplaceProduct "deleting" event.
     */
    public function deleting(MarketplaceProduct $product): void
    {
        // Clean up any product-related cache when product is deleted
        ProductStockService::clearProductCache($product);
    }
}
