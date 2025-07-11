<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 💸 Seller Payout Request Model
 *
 * Quản lý yêu cầu thanh toán cho sellers
 * Admin sẽ review và approve các payout requests
 */
class SellerPayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_reference',
        'seller_id',
        'seller_account_id',
        'total_sales',
        'commission_amount',
        'net_payout',
        'order_count',
        'period_from',
        'period_to',
        'bank_details',
        'status',
        'processed_by',
        'approved_at',
        'processed_at',
        'completed_at',
        'admin_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_payout' => 'decimal:2',
        'bank_details' => 'array',
        'period_from' => 'date',
        'period_to' => 'date',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship: Seller who requested payout
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relationship: Seller account details
     */
    public function sellerAccount(): BelongsTo
    {
        return $this->belongsTo(MarketplaceSeller::class, 'seller_account_id');
    }

    /**
     * Relationship: Admin who processed the request
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Relationship: Payout items (order items included in this payout)
     */
    public function payoutItems(): HasMany
    {
        return $this->hasMany(SellerPayoutItem::class, 'payout_request_id');
    }

    /**
     * Relationship: Audit logs
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(PaymentAuditLog::class, 'entity_id')
                    ->where('entity_type', 'payout_request');
    }

    /**
     * Scope: Pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Filter by seller
     */
    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Scope: Filter by period
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_from', [$startDate, $endDate])
                    ->orWhereBetween('period_to', [$startDate, $endDate]);
    }

    /**
     * Generate unique payout reference
     */
    public static function generatePayoutReference(): string
    {
        do {
            $reference = 'PAYOUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('payout_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Create payout request for seller
     */
    public static function createForSeller(int $sellerId, array $data): self
    {
        $seller = User::findOrFail($sellerId);
        $sellerAccount = MarketplaceSeller::where('user_id', $sellerId)->firstOrFail();

        // Get unpaid order items for this seller
        $unpaidItems = \App\Models\MarketplaceOrderItem::where('seller_id', $sellerId)
            ->where('included_in_payout', false)
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'completed')
                      ->where('status', 'completed');
            })
            ->whereBetween('created_at', [$data['period_from'], $data['period_to']])
            ->get();

        if ($unpaidItems->isEmpty()) {
            throw new \Exception('No unpaid items found for this period');
        }

        $totalSales = $unpaidItems->sum('item_total');
        $commissionAmount = $unpaidItems->sum('commission_amount');
        $netPayout = $totalSales - $commissionAmount;

        // Check minimum payout amount
        if ($netPayout < $sellerAccount->minimum_payout_amount) {
            throw new \Exception("Payout amount ({$netPayout} VNĐ) is below minimum ({$sellerAccount->minimum_payout_amount} VNĐ)");
        }

        $payoutRequest = static::create([
            'payout_reference' => static::generatePayoutReference(),
            'seller_id' => $sellerId,
            'seller_account_id' => $sellerAccount->id,
            'total_sales' => $totalSales,
            'commission_amount' => $commissionAmount,
            'net_payout' => $netPayout,
            'order_count' => $unpaidItems->count(),
            'period_from' => $data['period_from'],
            'period_to' => $data['period_to'],
            'bank_details' => $sellerAccount->bank_information,
            'status' => 'pending',
        ]);

        // Create payout items
        foreach ($unpaidItems as $item) {
            SellerPayoutItem::create([
                'payout_request_id' => $payoutRequest->id,
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'centralized_payment_id' => $item->order->centralized_payment_id,
                'product_id' => $item->product_id,
                'seller_id' => $item->seller_id,
                'item_price' => $item->price,
                'quantity' => $item->quantity,
                'item_total' => $item->item_total,
                'commission_rate' => $item->commission_rate,
                'commission_amount' => $item->commission_amount,
                'seller_earnings' => $item->seller_earnings,
                'status' => 'included',
            ]);

            // Mark item as included in payout
            $item->update([
                'included_in_payout' => true,
                'payout_included_at' => now(),
                'payout_request_id' => $payoutRequest->id,
            ]);
        }

        // Update seller pending payout
        $sellerAccount->increment('pending_payout', $netPayout);

        // Log the creation
        PaymentAuditLog::create([
            'event_type' => 'payout_request_created',
            'entity_type' => 'payout_request',
            'entity_id' => $payoutRequest->id,
            'user_id' => $sellerId,
            'new_values' => $payoutRequest->toArray(),
            'amount_impact' => $netPayout,
            'description' => "Payout request created for period {$data['period_from']} to {$data['period_to']}",
        ]);

        return $payoutRequest;
    }

    /**
     * Approve payout request
     */
    public function approve(int $adminId, string $notes = ''): bool
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending requests can be approved');
        }

        $this->update([
            'status' => 'approved',
            'processed_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Log the approval
        PaymentAuditLog::create([
            'event_type' => 'payout_approved',
            'entity_type' => 'payout_request',
            'entity_id' => $this->id,
            'admin_id' => $adminId,
            'new_values' => ['status' => 'approved'],
            'amount_impact' => $this->net_payout,
            'description' => 'Payout request approved by admin',
            'admin_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Reject payout request
     */
    public function reject(int $adminId, string $reason): bool
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending requests can be rejected');
        }

        $this->update([
            'status' => 'rejected',
            'processed_by' => $adminId,
            'processed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Revert order items
        $this->payoutItems()->update(['status' => 'pending']);
        MarketplaceOrderItem::whereIn('id', $this->payoutItems()->pluck('order_item_id'))
            ->update([
                'included_in_payout' => false,
                'payout_included_at' => null,
                'payout_request_id' => null,
            ]);

        // Update seller pending payout
        $this->sellerAccount->decrement('pending_payout', $this->net_payout);

        // Log the rejection
        PaymentAuditLog::create([
            'event_type' => 'payout_rejected',
            'entity_type' => 'payout_request',
            'entity_id' => $this->id,
            'admin_id' => $adminId,
            'new_values' => ['status' => 'rejected'],
            'amount_impact' => -$this->net_payout,
            'description' => 'Payout request rejected: ' . $reason,
            'admin_notes' => $reason,
        ]);

        return true;
    }

    /**
     * Mark as completed (payment sent)
     */
    public function markAsCompleted(int $adminId, string $notes = ''): bool
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Only approved requests can be marked as completed');
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'admin_notes' => $this->admin_notes . "\n" . $notes,
        ]);

        // Update payout items
        $this->payoutItems()->update(['status' => 'paid']);

        // Update seller account
        $this->sellerAccount->update([
            'total_earnings' => $this->sellerAccount->total_earnings + $this->net_payout,
            'pending_payout' => $this->sellerAccount->pending_payout - $this->net_payout,
            'last_payout_at' => now(),
        ]);

        // Update related orders
        MarketplaceOrder::whereIn('id', $this->payoutItems()->pluck('order_id'))
            ->update([
                'seller_paid' => true,
                'seller_paid_at' => now(),
            ]);

        // Log the completion
        PaymentAuditLog::create([
            'event_type' => 'payout_completed',
            'entity_type' => 'payout_request',
            'entity_id' => $this->id,
            'admin_id' => $adminId,
            'new_values' => ['status' => 'completed'],
            'amount_impact' => $this->net_payout,
            'description' => 'Payout completed and sent to seller',
            'admin_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'processing' => 'primary',
            'completed' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'seller_id' => 'required|exists:users,id',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'bank_details' => 'required|array',
            'bank_details.bank_name' => 'required|string',
            'bank_details.account_number' => 'required|string',
            'bank_details.account_name' => 'required|string',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->payout_reference)) {
                $model->payout_reference = static::generatePayoutReference();
            }
        });
    }
}
