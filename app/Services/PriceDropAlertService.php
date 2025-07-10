<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\ProductPriceHistory;
use App\Models\ProductWatcher;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PriceDropAlertService
{
    /**
     * Handle price change for a product
     */
    public static function handlePriceChange(
        MarketplaceProduct $product,
        float $oldPrice,
        float $newPrice,
        ?float $oldSalePrice = null,
        ?float $newSalePrice = null,
        ?string $reason = null,
        ?int $changedBy = null
    ): void {
        try {
            // Create price history record
            $priceHistory = ProductPriceHistory::createRecord(
                $product,
                $oldPrice,
                $newPrice,
                $oldSalePrice,
                $newSalePrice,
                $reason,
                $changedBy
            );

            Log::info('Price change recorded', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'change_type' => $priceHistory->change_type,
                'change_percentage' => $priceHistory->price_change_percentage
            ]);

            // Handle notifications if price decreased
            if ($priceHistory->isPriceDecrease()) {
                static::handlePriceDropNotifications($product, $priceHistory);
            }

        } catch (\Exception $e) {
            Log::error('Price change handling failed', [
                'product_id' => $product->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle price drop notifications
     */
    private static function handlePriceDropNotifications(
        MarketplaceProduct $product,
        ProductPriceHistory $priceHistory
    ): void {
        // Get watchers who should be notified
        $watchers = ProductWatcher::getWatchersForPriceDrop(
            $product->id,
            $priceHistory->new_price,
            $priceHistory->old_price
        );

        if ($watchers->isEmpty()) {
            Log::info('No watchers to notify for price drop', [
                'product_id' => $product->id
            ]);
            return;
        }

        Log::info('Notifying watchers about price drop', [
            'product_id' => $product->id,
            'watchers_count' => $watchers->count(),
            'price_drop' => $priceHistory->formatted_price_change,
            'percentage_drop' => $priceHistory->formatted_percentage_change
        ]);

        foreach ($watchers as $watcher) {
            static::sendPriceDropNotification($watcher, $product, $priceHistory);
        }
    }

    /**
     * Send price drop notification to a watcher
     */
    private static function sendPriceDropNotification(
        ProductWatcher $watcher,
        MarketplaceProduct $product,
        ProductPriceHistory $priceHistory
    ): void {
        try {
            // Check if recently notified to avoid spam
            if ($watcher->wasRecentlyNotified()) {
                Log::info('Skipping notification - recently notified', [
                    'user_id' => $watcher->user_id,
                    'product_id' => $product->id,
                    'last_notified' => $watcher->last_notified_at
                ]);
                return;
            }

            $user = $watcher->user;
            if (!$user) {
                Log::warning('Watcher user not found', ['watcher_id' => $watcher->id]);
                return;
            }

            // Determine notification type and message
            $isTargetReached = $watcher->shouldNotifyForTargetPrice($priceHistory->new_price);
            
            if ($isTargetReached) {
                $title = 'Giá mục tiêu đã đạt được!';
                $message = "Sản phẩm \"{$product->name}\" đã giảm xuống {$priceHistory->formatted_price_change} ({$priceHistory->formatted_percentage_change}) và đạt giá mục tiêu của bạn!";
                $notificationType = 'price_target_reached';
            } else {
                $title = 'Sản phẩm theo dõi giảm giá';
                $message = "Sản phẩm \"{$product->name}\" đã giảm giá {$priceHistory->formatted_price_change} ({$priceHistory->formatted_percentage_change})";
                $notificationType = 'price_drop_alert';
            }

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'old_price' => $priceHistory->old_price,
                'new_price' => $priceHistory->new_price,
                'price_change' => $priceHistory->price_change,
                'price_change_percentage' => $priceHistory->price_change_percentage,
                'target_price' => $watcher->target_price,
                'is_target_reached' => $isTargetReached,
                'action_url' => route('marketplace.products.show', $product->slug),
                'watcher_id' => $watcher->id,
            ];

            // Send notification
            $result = NotificationService::send(
                $user,
                $notificationType,
                $title,
                $message,
                $data,
                true // Send email for price drops
            );

            if ($result) {
                // Mark watcher as notified
                $watcher->markAsNotified();
                
                Log::info('Price drop notification sent', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'notification_type' => $notificationType,
                    'is_target_reached' => $isTargetReached
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send price drop notification', [
                'watcher_id' => $watcher->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Add product watcher
     */
    public static function addWatcher(
        User $user,
        MarketplaceProduct $product,
        ?float $targetPrice = null,
        bool $notifyAnyDrop = true,
        bool $notifyStockChanges = false
    ): ProductWatcher {
        $watcher = ProductWatcher::createOrUpdate(
            $user->id,
            $product->id,
            $targetPrice,
            $notifyAnyDrop,
            $notifyStockChanges
        );

        Log::info('Product watcher added', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'target_price' => $targetPrice,
            'notify_any_drop' => $notifyAnyDrop
        ]);

        return $watcher;
    }

    /**
     * Remove product watcher
     */
    public static function removeWatcher(User $user, MarketplaceProduct $product): bool
    {
        $result = ProductWatcher::removeWatcher($user->id, $product->id);

        if ($result) {
            Log::info('Product watcher removed', [
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
        }

        return $result;
    }

    /**
     * Get user's watched products
     */
    public static function getUserWatchedProducts(User $user, int $limit = 20)
    {
        return ProductWatcher::where('user_id', $user->id)
            ->active()
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'slug', 'price', 'sale_price', 'featured_image', 'in_stock');
            }])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get price history for a product
     */
    public static function getProductPriceHistory(MarketplaceProduct $product, int $days = 30)
    {
        return ProductPriceHistory::getRecentHistory($product->id, $days);
    }

    /**
     * Get price trend analysis
     */
    public static function getPriceTrendAnalysis(MarketplaceProduct $product, int $days = 30): array
    {
        return ProductPriceHistory::getPriceTrend($product->id, $days);
    }

    /**
     * Check if user is watching a product
     */
    public static function isUserWatchingProduct(User $user, MarketplaceProduct $product): bool
    {
        return ProductWatcher::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->active()
            ->exists();
    }

    /**
     * Get watching statistics for a product
     */
    public static function getProductWatchingStats(MarketplaceProduct $product): array
    {
        $watchers = ProductWatcher::where('product_id', $product->id)->active();
        
        return [
            'total_watchers' => $watchers->count(),
            'with_target_price' => $watchers->whereNotNull('target_price')->count(),
            'notify_any_drop' => $watchers->where('notify_any_drop', true)->count(),
            'average_target_price' => $watchers->whereNotNull('target_price')->avg('target_price'),
            'lowest_target_price' => $watchers->whereNotNull('target_price')->min('target_price'),
        ];
    }

    /**
     * Bulk process price changes (for admin/system updates)
     */
    public static function bulkProcessPriceChanges(array $priceUpdates): array
    {
        $results = [];
        
        foreach ($priceUpdates as $update) {
            try {
                $product = MarketplaceProduct::find($update['product_id']);
                if (!$product) {
                    $results[] = [
                        'product_id' => $update['product_id'],
                        'success' => false,
                        'error' => 'Product not found'
                    ];
                    continue;
                }

                $oldPrice = $product->price;
                $newPrice = $update['new_price'];
                $reason = $update['reason'] ?? 'Bulk price update';
                $changedBy = $update['changed_by'] ?? null;

                // Update product price
                $product->update(['price' => $newPrice]);

                // Handle price change
                static::handlePriceChange($product, $oldPrice, $newPrice, null, null, $reason, $changedBy);

                $results[] = [
                    'product_id' => $product->id,
                    'success' => true,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice
                ];

            } catch (\Exception $e) {
                $results[] = [
                    'product_id' => $update['product_id'] ?? null,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Clean up old price history records
     */
    public static function cleanupOldPriceHistory(int $days = 365): int
    {
        return ProductPriceHistory::where('created_at', '<', now()->subDays($days))->delete();
    }
}
