<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductStockService
{
    /**
     * Check and handle stock changes for a product
     */
    public static function handleStockChange(MarketplaceProduct $product, int $oldQuantity, int $newQuantity): void
    {
        try {
            // Check if product went out of stock
            if ($oldQuantity > 0 && $newQuantity <= 0) {
                static::handleOutOfStock($product);
            }

            // Check if product went below low stock threshold
            if ($oldQuantity > $product->low_stock_threshold && $newQuantity <= $product->low_stock_threshold && $newQuantity > 0) {
                static::handleLowStock($product);
            }

            // Check if product came back in stock
            if ($oldQuantity <= 0 && $newQuantity > 0) {
                static::handleBackInStock($product);
            }

            // Update product in_stock status
            $product->update(['in_stock' => $newQuantity > 0]);

        } catch (\Exception $e) {
            Log::error('Product stock change handling failed', [
                'product_id' => $product->id,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle product going out of stock
     */
    private static function handleOutOfStock(MarketplaceProduct $product): void
    {
        Log::info('Product went out of stock', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'seller_id' => $product->seller_id
        ]);

        // Notify seller
        static::notifySellerOutOfStock($product);

        // Notify users who have this product in wishlist
        \App\Services\WishlistNotificationService::handleProductOutOfStock($product);

        // Notify users who are watching this product
        static::notifyWatchingUsers($product, 'out_of_stock');

        // Clear cache
        static::clearProductCache($product);
    }

    /**
     * Handle product going to low stock
     */
    private static function handleLowStock(MarketplaceProduct $product): void
    {
        Log::info('Product low stock alert', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock_quantity' => $product->stock_quantity,
            'threshold' => $product->low_stock_threshold
        ]);

        // Notify seller about low stock
        static::notifySellerLowStock($product);
    }

    /**
     * Handle product coming back in stock
     */
    private static function handleBackInStock(MarketplaceProduct $product): void
    {
        Log::info('Product back in stock', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock_quantity' => $product->stock_quantity
        ]);

        // Notify users who have this product in wishlist
        \App\Services\WishlistNotificationService::handleProductAvailable($product);

        // Notify users who are watching this product
        static::notifyWatchingUsers($product, 'back_in_stock');

        // Clear cache
        static::clearProductCache($product);
    }

    /**
     * Notify seller about out of stock
     */
    private static function notifySellerOutOfStock(MarketplaceProduct $product): void
    {
        $seller = $product->seller?->user;
        if (!$seller) return;

        NotificationService::send(
            $seller,
            'product_out_of_stock',
            'Sản phẩm hết hàng',
            "Sản phẩm \"{$product->name}\" đã hết hàng. Vui lòng cập nhật kho hàng.",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'sku' => $product->sku,
                'action_url' => route('marketplace.seller.products.edit', $product->id)
            ],
            true // Send email
        );
    }

    /**
     * Notify seller about low stock
     */
    private static function notifySellerLowStock(MarketplaceProduct $product): void
    {
        $seller = $product->seller?->user;
        if (!$seller) return;

        // Check if we already sent low stock notification recently (within 24 hours)
        $cacheKey = "low_stock_notified_{$product->id}";
        if (Cache::has($cacheKey)) {
            return;
        }

        NotificationService::send(
            $seller,
            'product_low_stock',
            'Sản phẩm sắp hết hàng',
            "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} sản phẩm trong kho.",
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'stock_quantity' => $product->stock_quantity,
                'threshold' => $product->low_stock_threshold,
                'action_url' => route('marketplace.seller.products.edit', $product->id)
            ],
            false // Don't send email for low stock (just notification)
        );

        // Cache to prevent spam notifications
        Cache::put($cacheKey, true, now()->addHours(24));
    }

    /**
     * Notify users who have this product in wishlist
     */
    private static function notifyWishlistUsers(MarketplaceProduct $product, string $type): void
    {
        // Get users who have this product in wishlist
        $wishlistUsers = static::getWishlistUsers($product);

        foreach ($wishlistUsers as $user) {
            if ($type === 'out_of_stock') {
                NotificationService::send(
                    $user,
                    'wishlist_out_of_stock',
                    'Sản phẩm trong wishlist hết hàng',
                    "Sản phẩm \"{$product->name}\" trong wishlist của bạn đã hết hàng.",
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'action_url' => route('marketplace.products.show', $product->slug)
                    ],
                    false
                );
            } elseif ($type === 'back_in_stock') {
                NotificationService::send(
                    $user,
                    'wishlist_available',
                    'Sản phẩm trong wishlist có hàng trở lại',
                    "Sản phẩm \"{$product->name}\" trong wishlist của bạn đã có hàng trở lại!",
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'action_url' => route('marketplace.products.show', $product->slug)
                    ],
                    true // Send email for back in stock
                );
            }
        }
    }

    /**
     * Notify users who are watching this product
     */
    private static function notifyWatchingUsers(MarketplaceProduct $product, string $type): void
    {
        // Get users who are watching this product (similar to thread followers)
        $watchingUsers = static::getProductWatchers($product);

        foreach ($watchingUsers as $user) {
            if ($type === 'out_of_stock') {
                NotificationService::send(
                    $user,
                    'watched_product_out_of_stock',
                    'Sản phẩm theo dõi hết hàng',
                    "Sản phẩm \"{$product->name}\" mà bạn đang theo dõi đã hết hàng.",
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'action_url' => route('marketplace.products.show', $product->slug)
                    ],
                    false
                );
            } elseif ($type === 'back_in_stock') {
                NotificationService::send(
                    $user,
                    'watched_product_available',
                    'Sản phẩm theo dõi có hàng trở lại',
                    "Sản phẩm \"{$product->name}\" mà bạn đang theo dõi đã có hàng trở lại!",
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'action_url' => route('marketplace.products.show', $product->slug)
                    ],
                    true
                );
            }
        }
    }

    /**
     * Get users who have this product in wishlist
     */
    private static function getWishlistUsers(MarketplaceProduct $product): \Illuminate\Support\Collection
    {
        return \App\Models\MarketplaceWishlist::getUsersToNotifyWhenAvailable($product->id);
    }

    /**
     * Get users who are watching this product
     */
    private static function getProductWatchers(MarketplaceProduct $product): \Illuminate\Support\Collection
    {
        return \App\Models\ProductWatcher::getProductWatchers($product->id);
    }

    /**
     * Clear product-related cache
     */
    public static function clearProductCache(MarketplaceProduct $product): void
    {
        $cacheKeys = [
            "product_{$product->id}",
            "product_slug_{$product->slug}",
            "seller_products_{$product->seller_id}",
            "category_products_{$product->product_category_id}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Bulk update stock for multiple products
     */
    public static function bulkUpdateStock(array $updates): array
    {
        $results = [];

        foreach ($updates as $update) {
            try {
                $product = MarketplaceProduct::find($update['product_id']);
                if (!$product) {
                    $results[] = ['product_id' => $update['product_id'], 'success' => false, 'error' => 'Product not found'];
                    continue;
                }

                $oldQuantity = $product->stock_quantity;
                $newQuantity = $update['quantity'];

                $product->update(['stock_quantity' => $newQuantity]);
                static::handleStockChange($product, $oldQuantity, $newQuantity);

                $results[] = ['product_id' => $product->id, 'success' => true];

            } catch (\Exception $e) {
                $results[] = ['product_id' => $update['product_id'], 'success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }
}
