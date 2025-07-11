<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 📋 Seller Payout Item Model
 * 
 * Chi tiết từng order item trong payout request
 * Tracking individual items được include trong payout
 */
class SellerPayoutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_request_id',
        'order_id',
        'order_item_id',
        'centralized_payment_id',
        'product_id',
        'seller_id',
        'item_price',
        'quantity',
        'item_total',
        'commission_rate',
        'commission_amount',
        'seller_earnings',
        'status',
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'item_total' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'seller_earnings' => 'decimal:2',
    ];

    /**
     * Relationship: Payout request this item belongs to
     */
    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(SellerPayoutRequest::class, 'payout_request_id');
    }

    /**
     * Relationship: Order this item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    /**
     * Relationship: Original order item
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrderItem::class, 'order_item_id');
    }

    /**
     * Relationship: Centralized payment
     */
    public function centralizedPayment(): BelongsTo
    {
        return $this->belongsTo(CentralizedPayment::class, 'centralized_payment_id');
    }

    /**
     * Relationship: Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship: Seller
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by seller
     */
    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Scope: Paid items
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Pending items
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'included' => 'info',
            'paid' => 'success',
            'disputed' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get commission percentage
     */
    public function getCommissionPercentageAttribute(): float
    {
        if ($this->item_total == 0) {
            return 0;
        }
        
        return ($this->commission_amount / $this->item_total) * 100;
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'payout_request_id' => 'required|exists:seller_payout_requests,id',
            'order_id' => 'required|exists:marketplace_orders,id',
            'order_item_id' => 'required|exists:marketplace_order_items,id',
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'item_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'item_total' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_amount' => 'required|numeric|min:0',
            'seller_earnings' => 'required|numeric|min:0',
            'status' => 'required|in:pending,included,paid,disputed',
        ];
    }
}
