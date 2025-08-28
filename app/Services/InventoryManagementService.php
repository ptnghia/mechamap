<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InventoryManagementService
{
    /**
     * Update product stock quantity
     */
    public function updateStock(MarketplaceProduct $product, int $quantity, string $reason = 'manual_update', ?User $user = null): bool
    {
        try {
            DB::beginTransaction();

            $oldQuantity = $product->stock_quantity;
            $product->update(['stock_quantity' => $quantity]);

            // Log stock change
            $this->logStockChange($product, $oldQuantity, $quantity, $reason, $user);

            // Check low stock alert
            $this->checkLowStockAlert($product);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating stock: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'old_quantity' => $oldQuantity ?? 0,
                'new_quantity' => $quantity,
                'reason' => $reason
            ]);
            return false;
        }
    }

    /**
     * Reduce stock quantity (for sales)
     */
    public function reduceStock(MarketplaceProduct $product, int $quantity, string $orderId = null): bool
    {
        if (!$product->manage_stock) {
            return true; // Stock management disabled
        }

        if ($product->stock_quantity < $quantity) {
            Log::warning('Insufficient stock for product', [
                'product_id' => $product->id,
                'requested' => $quantity,
                'available' => $product->stock_quantity
            ]);
            return false;
        }

        $newQuantity = $product->stock_quantity - $quantity;
        $reason = $orderId ? "sale_order_{$orderId}" : 'sale';

        return $this->updateStock($product, $newQuantity, $reason);
    }

    /**
     * Increase stock quantity (for restocking)
     */
    public function increaseStock(MarketplaceProduct $product, int $quantity, string $reason = 'restock', ?User $user = null): bool
    {
        $newQuantity = $product->stock_quantity + $quantity;
        return $this->updateStock($product, $newQuantity, $reason, $user);
    }

    /**
     * Check if product has sufficient stock
     */
    public function hasStock(MarketplaceProduct $product, int $quantity = 1): bool
    {
        if (!$product->manage_stock) {
            return true; // Stock management disabled
        }

        return $product->stock_quantity >= $quantity;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(MarketplaceProduct $product): array
    {
        if (!$product->manage_stock) {
            return [
                'status' => 'unlimited',
                'message' => 'Không giới hạn',
                'class' => 'text-success'
            ];
        }

        $quantity = $product->stock_quantity;
        $threshold = $product->low_stock_threshold ?? 10;

        if ($quantity <= 0) {
            return [
                'status' => 'out_of_stock',
                'message' => 'Hết hàng',
                'class' => 'text-danger'
            ];
        } elseif ($quantity <= $threshold) {
            return [
                'status' => 'low_stock',
                'message' => "Sắp hết ({$quantity} còn lại)",
                'class' => 'text-warning'
            ];
        } else {
            return [
                'status' => 'in_stock',
                'message' => "Còn hàng ({$quantity})",
                'class' => 'text-success'
            ];
        }
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts(?User $seller = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = MarketplaceProduct::where('manage_stock', true)
            ->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 10)')
            ->where('stock_quantity', '>', 0);

        if ($seller) {
            $query->where('seller_id', $seller->id);
        }

        return $query->with(['seller', 'category'])->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(?User $seller = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = MarketplaceProduct::where('manage_stock', true)
            ->where('stock_quantity', '<=', 0);

        if ($seller) {
            $query->where('seller_id', $seller->id);
        }

        return $query->with(['seller', 'category'])->get();
    }

    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(array $updates, ?User $user = null): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($updates as $update) {
            $productId = $update['product_id'];
            $quantity = $update['quantity'];
            $reason = $update['reason'] ?? 'bulk_update';

            try {
                $product = MarketplaceProduct::findOrFail($productId);
                
                if ($this->updateStock($product, $quantity, $reason, $user)) {
                    $results['success'][] = $productId;
                } else {
                    $results['failed'][] = $productId;
                }
            } catch (\Exception $e) {
                Log::error('Bulk stock update failed for product: ' . $productId, [
                    'error' => $e->getMessage()
                ]);
                $results['failed'][] = $productId;
            }
        }

        return $results;
    }

    /**
     * Log stock changes
     */
    private function logStockChange(MarketplaceProduct $product, int $oldQuantity, int $newQuantity, string $reason, ?User $user = null): void
    {
        try {
            // You can implement a stock_logs table if needed
            Log::info('Stock quantity changed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'change' => $newQuantity - $oldQuantity,
                'reason' => $reason,
                'user_id' => $user?->id,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging stock change: ' . $e->getMessage());
        }
    }

    /**
     * Check and send low stock alerts
     */
    private function checkLowStockAlert(MarketplaceProduct $product): void
    {
        if (!$product->manage_stock) {
            return;
        }

        $threshold = $product->low_stock_threshold ?? 10;
        
        if ($product->stock_quantity <= $threshold && $product->stock_quantity > 0) {
            // Send low stock notification
            $this->sendLowStockNotification($product);
        } elseif ($product->stock_quantity <= 0) {
            // Send out of stock notification
            $this->sendOutOfStockNotification($product);
        }
    }

    /**
     * Send low stock notification
     */
    private function sendLowStockNotification(MarketplaceProduct $product): void
    {
        try {
            // Implement notification logic here
            Log::info('Low stock alert', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => $product->stock_quantity,
                'threshold' => $product->low_stock_threshold
            ]);

            // You can integrate with notification system here
            // NotificationService::send($product->seller, 'low_stock', $product);
            
        } catch (\Exception $e) {
            Log::error('Error sending low stock notification: ' . $e->getMessage());
        }
    }

    /**
     * Send out of stock notification
     */
    private function sendOutOfStockNotification(MarketplaceProduct $product): void
    {
        try {
            Log::warning('Out of stock alert', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => $product->stock_quantity
            ]);

            // You can integrate with notification system here
            // NotificationService::send($product->seller, 'out_of_stock', $product);
            
        } catch (\Exception $e) {
            Log::error('Error sending out of stock notification: ' . $e->getMessage());
        }
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats(?User $seller = null): array
    {
        $query = MarketplaceProduct::where('manage_stock', true);
        
        if ($seller) {
            $query->where('seller_id', $seller->id);
        }

        $totalProducts = $query->count();
        $inStock = $query->where('stock_quantity', '>', 0)->count();
        $lowStock = $query->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 10)')
                         ->where('stock_quantity', '>', 0)->count();
        $outOfStock = $query->where('stock_quantity', '<=', 0)->count();
        $totalValue = $query->sum(DB::raw('stock_quantity * price'));

        return [
            'total_products' => $totalProducts,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_inventory_value' => $totalValue,
            'stock_percentage' => $totalProducts > 0 ? round(($inStock / $totalProducts) * 100, 2) : 0
        ];
    }

    /**
     * Validate stock operation
     */
    public function validateStockOperation(MarketplaceProduct $product, int $quantity, string $operation = 'reduce'): array
    {
        $errors = [];

        if (!$product->manage_stock && $operation === 'reduce') {
            return $errors; // No validation needed if stock management is disabled
        }

        if ($quantity < 0) {
            $errors[] = 'Số lượng không được âm.';
        }

        if ($operation === 'reduce' && $product->stock_quantity < $quantity) {
            $errors[] = "Không đủ hàng trong kho. Có sẵn: {$product->stock_quantity}, yêu cầu: {$quantity}";
        }

        if ($operation === 'set' && $quantity > 999999) {
            $errors[] = 'Số lượng tồn kho không được vượt quá 999,999.';
        }

        return $errors;
    }
}
