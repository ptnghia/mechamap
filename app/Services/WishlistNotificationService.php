<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\MarketplaceWishlist;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WishlistNotificationService
{
    /**
     * Handle product becoming available (back in stock)
     */
    public static function handleProductAvailable(MarketplaceProduct $product): void
    {
        try {
            // Get users who should be notified when product becomes available
            $wishlistItems = MarketplaceWishlist::getUsersToNotifyWhenAvailable($product->id);

            if ($wishlistItems->isEmpty()) {
                Log::info('No wishlist users to notify for product availability', [
                    'product_id' => $product->id
                ]);
                return;
            }

            Log::info('Notifying wishlist users about product availability', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'users_count' => $wishlistItems->count()
            ]);

            foreach ($wishlistItems as $wishlistItem) {
                static::sendAvailabilityNotification($wishlistItem, $product);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle product availability notifications', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle product going out of stock
     */
    public static function handleProductOutOfStock(MarketplaceProduct $product): void
    {
        try {
            // Get users who have this product in wishlist
            $wishlistItems = MarketplaceWishlist::getProductWishlistUsers($product->id);

            if ($wishlistItems->isEmpty()) {
                return;
            }

            Log::info('Notifying wishlist users about product out of stock', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'users_count' => $wishlistItems->count()
            ]);

            foreach ($wishlistItems as $wishlistItem) {
                static::sendOutOfStockNotification($wishlistItem, $product);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle product out of stock notifications', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send availability notification to a wishlist user
     */
    private static function sendAvailabilityNotification(
        MarketplaceWishlist $wishlistItem,
        MarketplaceProduct $product
    ): void {
        try {
            // Check if recently notified to avoid spam
            if ($wishlistItem->wasRecentlyNotified()) {
                Log::info('Skipping availability notification - recently notified', [
                    'user_id' => $wishlistItem->user_id,
                    'product_id' => $product->id,
                    'last_notified' => $wishlistItem->last_notified_at
                ]);
                return;
            }

            $user = $wishlistItem->user;
            if (!$user) {
                Log::warning('Wishlist user not found', ['wishlist_id' => $wishlistItem->id]);
                return;
            }

            $title = 'Sản phẩm trong wishlist có hàng trở lại!';
            $message = "Sản phẩm \"{$product->name}\" trong wishlist của bạn đã có hàng trở lại. Đặt hàng ngay để không bỏ lỡ!";

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'current_price' => $product->getCurrentPrice(),
                'stock_quantity' => $product->stock_quantity,
                'action_url' => route('marketplace.products.show', $product->slug),
                'wishlist_id' => $wishlistItem->id,
                'target_price' => $wishlistItem->target_price,
                'is_target_reached' => $wishlistItem->isTargetPriceReached(),
            ];

            // Send notification
            $result = NotificationService::send(
                $user,
                'wishlist_available',
                $title,
                $message,
                $data,
                true // Send email for availability notifications
            );

            if ($result) {
                // Mark wishlist item as notified
                $wishlistItem->markAsNotified();
                
                Log::info('Wishlist availability notification sent', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'wishlist_id' => $wishlistItem->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send wishlist availability notification', [
                'wishlist_id' => $wishlistItem->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send out of stock notification to a wishlist user
     */
    private static function sendOutOfStockNotification(
        MarketplaceWishlist $wishlistItem,
        MarketplaceProduct $product
    ): void {
        try {
            $user = $wishlistItem->user;
            if (!$user) {
                return;
            }

            // Only send if user wants to be notified about stock changes
            if (!$wishlistItem->notify_when_available) {
                return;
            }

            $title = 'Sản phẩm trong wishlist hết hàng';
            $message = "Sản phẩm \"{$product->name}\" trong wishlist của bạn đã hết hàng. Chúng tôi sẽ thông báo khi có hàng trở lại.";

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'action_url' => route('marketplace.products.show', $product->slug),
                'wishlist_id' => $wishlistItem->id,
            ];

            // Send notification (no email for out of stock, just in-app notification)
            NotificationService::send(
                $user,
                'wishlist_out_of_stock',
                $title,
                $message,
                $data,
                false
            );

            Log::info('Wishlist out of stock notification sent', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'wishlist_id' => $wishlistItem->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send wishlist out of stock notification', [
                'wishlist_id' => $wishlistItem->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle price drop for wishlist items
     */
    public static function handlePriceDrop(
        MarketplaceProduct $product,
        float $oldPrice,
        float $newPrice
    ): void {
        try {
            // Get users who should be notified for price drops
            $wishlistItems = MarketplaceWishlist::getUsersToNotifyForPriceDrops($product->id);

            if ($wishlistItems->isEmpty()) {
                return;
            }

            $priceChange = $oldPrice - $newPrice;
            $priceChangePercentage = $oldPrice > 0 ? (($oldPrice - $newPrice) / $oldPrice) * 100 : 0;

            Log::info('Notifying wishlist users about price drop', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'users_count' => $wishlistItems->count(),
                'price_change' => $priceChange,
                'percentage_change' => $priceChangePercentage
            ]);

            foreach ($wishlistItems as $wishlistItem) {
                static::sendPriceDropNotification($wishlistItem, $product, $oldPrice, $newPrice);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle wishlist price drop notifications', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send price drop notification to a wishlist user
     */
    private static function sendPriceDropNotification(
        MarketplaceWishlist $wishlistItem,
        MarketplaceProduct $product,
        float $oldPrice,
        float $newPrice
    ): void {
        try {
            // Check if recently notified to avoid spam
            if ($wishlistItem->wasRecentlyNotified()) {
                return;
            }

            $user = $wishlistItem->user;
            if (!$user) {
                return;
            }

            $priceChange = $oldPrice - $newPrice;
            $priceChangePercentage = $oldPrice > 0 ? (($oldPrice - $newPrice) / $oldPrice) * 100 : 0;
            $isTargetReached = $wishlistItem->isTargetPriceReached();

            if ($isTargetReached) {
                $title = 'Giá mục tiêu đã đạt được!';
                $message = "Sản phẩm \"{$product->name}\" trong wishlist đã giảm xuống giá mục tiêu của bạn!";
                $notificationType = 'wishlist_target_reached';
            } else {
                $title = 'Sản phẩm trong wishlist giảm giá';
                $message = "Sản phẩm \"{$product->name}\" trong wishlist đã giảm giá " . number_format($priceChange, 0, ',', '.') . "₫ (" . number_format($priceChangePercentage, 1) . "%)";
                $notificationType = 'wishlist_price_drop';
            }

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'price_change' => $priceChange,
                'price_change_percentage' => $priceChangePercentage,
                'target_price' => $wishlistItem->target_price,
                'is_target_reached' => $isTargetReached,
                'action_url' => route('marketplace.products.show', $product->slug),
                'wishlist_id' => $wishlistItem->id,
            ];

            // Send notification
            $result = NotificationService::send(
                $user,
                $notificationType,
                $title,
                $message,
                $data,
                $isTargetReached // Send email only if target price reached
            );

            if ($result) {
                // Mark wishlist item as notified
                $wishlistItem->markAsNotified();
                
                Log::info('Wishlist price drop notification sent', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'notification_type' => $notificationType,
                    'is_target_reached' => $isTargetReached
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send wishlist price drop notification', [
                'wishlist_id' => $wishlistItem->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get wishlist statistics
     */
    public static function getWishlistStats(): array
    {
        return MarketplaceWishlist::getStats();
    }

    /**
     * Clean up old wishlist notifications
     */
    public static function cleanupOldWishlistItems(): int
    {
        return MarketplaceWishlist::cleanupOldItems();
    }
}
