<?php

namespace App\Services;

use App\Models\MarketplaceShoppingCart;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class CartPersistenceService
{
    /**
     * Merge guest cart with user cart when user logs in
     */
    public function mergeGuestCartOnLogin(User $user): MarketplaceShoppingCart
    {
        $sessionId = Session::getId();
        $guestCart = MarketplaceShoppingCart::forSession($sessionId)->active()->first();
        
        if (!$guestCart || $guestCart->isEmpty()) {
            return MarketplaceShoppingCart::getOrCreateForUser($user->id);
        }

        // Get or create user cart
        $userCart = MarketplaceShoppingCart::getOrCreateForUser($user->id);

        // Merge guest cart items into user cart
        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existingItem) {
                // Update quantity if item already exists
                $newQuantity = $existingItem->quantity + $guestItem->quantity;
                $existingItem->setQuantity($newQuantity);
            } else {
                // Create new item in user cart
                $userCart->items()->create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'product_id' => $guestItem->product_id,
                    'quantity' => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                    'sale_price' => $guestItem->sale_price,
                    'total_price' => $guestItem->total_price,
                    'product_name' => $guestItem->product_name,
                    'product_sku' => $guestItem->product_sku,
                    'product_image' => $guestItem->product_image,
                    'product_options' => $guestItem->product_options,
                ]);
            }
        }

        // Recalculate user cart totals
        $userCart->calculateTotals();

        // Delete guest cart
        $guestCart->delete();

        return $userCart;
    }

    /**
     * Save cart to session for guest users
     */
    public function saveGuestCartToSession(MarketplaceShoppingCart $cart): void
    {
        Session::put('guest_cart_id', $cart->id);
        Session::put('guest_cart_updated', now()->timestamp);
    }

    /**
     * Get guest cart from session
     */
    public function getGuestCartFromSession(): ?MarketplaceShoppingCart
    {
        $cartId = Session::get('guest_cart_id');
        
        if (!$cartId) {
            return null;
        }

        return MarketplaceShoppingCart::find($cartId);
    }

    /**
     * Clean up expired carts
     */
    public function cleanupExpiredCarts(): int
    {
        $expiredCarts = MarketplaceShoppingCart::where('expires_at', '<', now())
            ->where('status', 'active')
            ->get();

        $count = 0;
        foreach ($expiredCarts as $cart) {
            $cart->markAsAbandoned();
            $count++;
        }

        return $count;
    }

    /**
     * Clean up abandoned carts older than specified days
     */
    public function cleanupAbandonedCarts(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $abandonedCarts = MarketplaceShoppingCart::where('status', 'abandoned')
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $count = 0;
        foreach ($abandonedCarts as $cart) {
            $cart->items()->delete();
            $cart->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Backup cart data to cookies as fallback
     */
    public function backupCartToCookie(MarketplaceShoppingCart $cart): void
    {
        $cartData = [
            'id' => $cart->id,
            'items' => $cart->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'sale_price' => $item->sale_price,
                ];
            })->toArray(),
            'timestamp' => now()->timestamp,
        ];

        Cookie::queue('cart_backup', json_encode($cartData), 60 * 24 * 7); // 7 days
    }

    /**
     * Restore cart from cookie backup
     */
    public function restoreCartFromCookie(): ?array
    {
        $cookieData = Cookie::get('cart_backup');
        
        if (!$cookieData) {
            return null;
        }

        try {
            $cartData = json_decode($cookieData, true);
            
            // Check if backup is not too old (7 days)
            if (isset($cartData['timestamp']) && 
                now()->timestamp - $cartData['timestamp'] > 60 * 60 * 24 * 7) {
                return null;
            }

            return $cartData;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Sync cart prices with current product prices
     */
    public function syncCartPrices(MarketplaceShoppingCart $cart): bool
    {
        $hasChanges = false;

        foreach ($cart->items as $item) {
            if ($item->product) {
                $currentPrice = $item->product->price;
                $currentSalePrice = $item->product->is_on_sale ? $item->product->sale_price : null;

                if ($item->unit_price != $currentPrice || 
                    $item->sale_price != $currentSalePrice) {
                    
                    $item->unit_price = $currentPrice;
                    $item->sale_price = $currentSalePrice;
                    $item->updateTotal();
                    $hasChanges = true;
                }
            }
        }

        if ($hasChanges) {
            $cart->calculateTotals();
        }

        return $hasChanges;
    }

    /**
     * Get cart statistics for analytics
     */
    public function getCartStatistics(): array
    {
        return [
            'total_active_carts' => MarketplaceShoppingCart::where('status', 'active')->count(),
            'total_abandoned_carts' => MarketplaceShoppingCart::where('status', 'abandoned')->count(),
            'total_converted_carts' => MarketplaceShoppingCart::where('status', 'converted')->count(),
            'average_cart_value' => MarketplaceShoppingCart::where('status', 'active')
                ->avg('total_amount'),
            'average_items_per_cart' => MarketplaceShoppingCart::where('status', 'active')
                ->withCount('items')
                ->avg('items_count'),
            'carts_with_items' => MarketplaceShoppingCart::where('status', 'active')
                ->whereHas('items')
                ->count(),
            'empty_carts' => MarketplaceShoppingCart::where('status', 'active')
                ->whereDoesntHave('items')
                ->count(),
        ];
    }

    /**
     * Get abandoned cart recovery data
     */
    public function getAbandonedCartRecoveryData(int $days = 7): array
    {
        $cutoffDate = now()->subDays($days);
        
        $abandonedCarts = MarketplaceShoppingCart::where('status', 'abandoned')
            ->where('updated_at', '>=', $cutoffDate)
            ->whereNotNull('user_id')
            ->with(['user', 'items.product'])
            ->get();

        return $abandonedCarts->map(function ($cart) {
            return [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'user_email' => $cart->user->email ?? null,
                'user_name' => $cart->user->name ?? null,
                'total_amount' => $cart->total_amount,
                'items_count' => $cart->items->count(),
                'abandoned_at' => $cart->updated_at,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }),
            ];
        })->toArray();
    }

    /**
     * Mark cart as recovered (when user completes purchase after abandonment)
     */
    public function markCartAsRecovered(MarketplaceShoppingCart $cart): void
    {
        $cart->status = 'converted';
        $cart->metadata = array_merge($cart->metadata ?? [], [
            'recovered_at' => now()->toISOString(),
            'recovery_method' => 'email_campaign', // Can be customized
        ]);
        $cart->save();
    }
}
