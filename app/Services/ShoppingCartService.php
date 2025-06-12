<?php

namespace App\Services;

use App\Models\ShoppingCart;
use App\Models\TechnicalProduct;
use App\Models\User;
use Illuminate\Support\Collection;

class ShoppingCartService
{
    /**
     * Lấy cart items của user
     */
    public function getCartItems(int $userId): Collection
    {
        return ShoppingCart::where('user_id', $userId)
                          ->active()
                          ->with(['product' => function ($query) {
                              $query->select('id', 'title', 'description', 'price', 'sale_price', 'discount_percentage', 'status', 'seller_id')
                                    ->with('seller:id,name');
                          }])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Thêm sản phẩm vào cart
     */
    public function addToCart(int $userId, int $productId, int $quantity = 1, string $licenseType = 'standard'): ShoppingCart
    {
        $product = TechnicalProduct::findOrFail($productId);

        // Kiểm tra sản phẩm có approved không
        if ($product->status !== 'approved') {
            throw new \Exception('Sản phẩm không còn hoạt động');
        }

        // Kiểm tra user đã mua sản phẩm này chưa
        if ($this->hasUserPurchased($userId, $productId)) {
            throw new \Exception('Bạn đã sở hữu sản phẩm này');
        }

        // Kiểm tra user có phải là seller của sản phẩm không
        if ($product->seller_id === $userId) {
            throw new \Exception('Bạn không thể mua sản phẩm của chính mình');
        }

        return ShoppingCart::addOrUpdate($userId, $productId, $quantity, $licenseType);
    }

    /**
     * Cập nhật quantity trong cart
     */
    public function updateQuantity(int $userId, int $cartItemId, int $quantity): ShoppingCart
    {
        $cartItem = ShoppingCart::where('user_id', $userId)
                               ->where('id', $cartItemId)
                               ->firstOrFail();

        $cartItem->quantity = $quantity;
        $cartItem->updateTotal();

        return $cartItem;
    }

    /**
     * Cập nhật cart item với quantity và license type
     */
    public function updateCartItem(int $cartItemId, int $userId, int $quantity, string $licenseType = null): ShoppingCart
    {
        $cartItem = ShoppingCart::where('user_id', $userId)
                               ->where('id', $cartItemId)
                               ->firstOrFail();

        $cartItem->quantity = $quantity;

        if ($licenseType) {
            $cartItem->license_type = $licenseType;
        }

        $cartItem->updateTotal();

        // Refresh the model to ensure we have the latest data
        $cartItem->refresh();

        return $cartItem;
    }

    /**
     * Xóa item khỏi cart
     */
    public function removeFromCart(int $userId, int $cartItemId): bool
    {
        return ShoppingCart::where('user_id', $userId)
                          ->where('id', $cartItemId)
                          ->delete() > 0;
    }

    /**
     * Xóa tất cả items trong cart
     */
    public function clearCart(int $userId): bool
    {
        return ShoppingCart::where('user_id', $userId)->delete() > 0;
    }

    /**
     * Tính tổng cart
     */
    public function getCartSummary(int $userId): array
    {
        $cartItems = $this->getCartItems($userId);

        $subtotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->count();
        $totalQuantity = $cartItems->sum('quantity');

        // Tính phí xử lý (2.9% của subtotal)
        $processingFee = $subtotal * 0.029;

        // Thuế VAT (tạm thời 0%)
        $taxAmount = 0;

        // Tổng tiền
        $total = $subtotal + $processingFee + $taxAmount;

        return [
            'items' => $cartItems,
            'item_count' => $itemCount,
            'total_quantity' => $totalQuantity,
            'subtotal' => round($subtotal, 2),
            'processing_fee' => round($processingFee, 2),
            'tax_amount' => $taxAmount,
            'total' => round($total, 2),
            'formatted' => [
                'subtotal' => number_format($subtotal, 0, ',', '.') . ' ₫',
                'processing_fee' => number_format($processingFee, 0, ',', '.') . ' ₫',
                'tax_amount' => number_format($taxAmount, 0, ',', '.') . ' ₫',
                'total' => number_format($total, 0, ',', '.') . ' ₫',
            ]
        ];
    }

    /**
     * Kiểm tra user đã mua sản phẩm chưa
     */
    public function hasUserPurchased(int $userId, int $productId): bool
    {
        return \App\Models\OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })
        ->where('technical_product_id', $productId)
        ->exists();
    }

    /**
     * Validate cart trước khi checkout
     */
    public function validateCartForCheckout(int $userId): array
    {
        $cartItems = $this->getCartItems($userId);
        $errors = [];
        $warnings = [];
        $invalidItems = [];

        if ($cartItems->isEmpty()) {
            $errors[] = 'Giỏ hàng trống';
            return ['is_valid' => false, 'issues' => $errors, 'warnings' => [], 'invalid_items' => []];
        }

        foreach ($cartItems as $item) {
            // Kiểm tra sản phẩm còn active không
            if ($item->product->status !== 'approved') {
                $errors[] = "Sản phẩm '{$item->product->title}' không còn hoạt động";
                $invalidItems[] = $item->id;
            }

            // Kiểm tra giá có thay đổi không (sử dụng tolerance cho floating point)
            $currentPrice = $item->product->sale_price ?? $item->product->price;
            $priceDifference = abs($item->unit_price - $currentPrice);
            if ($priceDifference > 0.01) { // Tolerance 0.01 for floating point comparison
                $oldPrice = number_format($item->unit_price, 0, ',', '.') . ' ₫';
                $newPrice = number_format($currentPrice, 0, ',', '.') . ' ₫';
                $warnings[] = "Giá sản phẩm '{$item->product->title}' đã thay đổi từ {$oldPrice} thành {$newPrice}";
            }

            // Kiểm tra user đã mua sản phẩm chưa
            if ($this->hasUserPurchased($userId, $item->technical_product_id)) {
                $errors[] = "Bạn đã sở hữu sản phẩm '{$item->product->title}'";
                $invalidItems[] = $item->id;
            }
        }

        // Nếu chỉ có warnings về giá, cart vẫn valid nhưng cần confirm
        $isValid = empty($errors);
        $needsPriceUpdate = !empty($warnings);

        return [
            'is_valid' => $isValid,
            'needs_price_update' => $needsPriceUpdate,
            'issues' => array_merge($errors, $warnings),
            'errors' => $errors,
            'warnings' => $warnings,
            'invalid_items' => $invalidItems,
            'items' => $cartItems
        ];
    }

    /**
     * Cập nhật giá cho tất cả items trong cart
     */
    public function updatePrices(int $userId): array
    {
        $cartItems = ShoppingCart::where('user_id', $userId)
                                ->active()
                                ->with('product')
                                ->get();

        $updated = [];

        foreach ($cartItems as $item) {
            $currentPrice = $item->product->sale_price ?? $item->product->price;

            if ($item->unit_price != $currentPrice) {
                $oldPrice = $item->unit_price;
                $item->unit_price = $currentPrice;
                $item->updateTotal();

                $updated[] = [
                    'product_id' => $item->technical_product_id,
                    'product_title' => $item->product->title,
                    'old_price' => $oldPrice,
                    'new_price' => $currentPrice,
                    'old_total' => $oldPrice * $item->quantity,
                    'new_total' => $currentPrice * $item->quantity,
                ];
            }
        }

        return [
            'updated_count' => count($updated),
            'updated_items' => $updated,
        ];
    }

    /**
     * Dọn dẹp expired cart items
     */
    public function cleanupExpiredItems(): int
    {
        return ShoppingCart::where('expires_at', '<', now())
                          ->delete();
    }

    /**
     * Lấy cart count cho user
     */
    public function getCartCount(int $userId): int
    {
        return ShoppingCart::where('user_id', $userId)
                          ->active()
                          ->count();
    }

    /**
     * Move item to saved for later
     */
    public function saveForLater(int $userId, int $cartItemId): ShoppingCart
    {
        $cartItem = ShoppingCart::where('user_id', $userId)
                               ->where('id', $cartItemId)
                               ->firstOrFail();

        $cartItem->update(['status' => 'saved_for_later']);

        return $cartItem;
    }

    /**
     * Move item back to active cart
     */
    public function moveToCart(int $userId, int $cartItemId): ShoppingCart
    {
        $cartItem = ShoppingCart::where('user_id', $userId)
                               ->where('id', $cartItemId)
                               ->firstOrFail();

        $cartItem->update([
            'status' => 'active',
            'expires_at' => now()->addDays(7),
        ]);

        return $cartItem;
    }

    /**
     * Lấy saved for later items
     */
    public function getSavedItems(int $userId): Collection
    {
        return ShoppingCart::where('user_id', $userId)
                          ->where('status', 'saved_for_later')
                          ->with(['product' => function ($query) {
                              $query->select('id', 'title', 'description', 'price', 'sale_price', 'status', 'seller_id')
                                    ->with('seller:id,name');
                          }])
                          ->orderBy('updated_at', 'desc')
                          ->get();
    }

    /**
     * Validate cart for general operations
     */
    public function validateCart(int $userId): array
    {
        return $this->validateCartForCheckout($userId);
    }
}
