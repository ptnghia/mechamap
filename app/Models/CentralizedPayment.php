<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 🏦 Centralized Payment Model
 *
 * Quản lý tất cả payments từ customers đi về Admin account
 * Trước khi distribute cho sellers thông qua payout system
 */
class CentralizedPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'order_id',
        'customer_id',
        'customer_email',
        'payment_method',
        'gateway_transaction_id',
        'gateway_payment_intent_id',
        'gateway_response',
        'gross_amount',
        'gateway_fee',
        'net_received',
        'status',
        'paid_at',
        'confirmed_at',
        'failed_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'gross_amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_received' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Relationship: Order this payment belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    /**
     * Relationship: Customer who made the payment
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship: Payment audit logs
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(PaymentAuditLog::class, 'entity_id')
                    ->where('entity_type', 'centralized_payment');
    }

    /**
     * Relationship: Related payment transactions
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'centralized_payment_id');
    }

    /**
     * Scope: Successful payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Filter by payment method
     */
    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('paid_at', [$startDate, $endDate]);
    }

    /**
     * Generate unique payment reference
     */
    public static function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY-' . strtoupper(uniqid());
        } while (static::where('payment_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(array $gatewayResponse = []): bool
    {
        $this->update([
            'status' => 'completed',
            'confirmed_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse)
        ]);

        // Log the completion
        PaymentAuditLog::create([
            'event_type' => 'payment_completed',
            'entity_type' => 'centralized_payment',
            'entity_id' => $this->id,
            'user_id' => $this->customer_id,
            'new_values' => ['status' => 'completed'],
            'amount_impact' => $this->net_received,
            'description' => 'Payment completed successfully',
        ]);

        // Update related order
        if ($this->order) {
            $this->order->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);
        }

        return true;
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason = '', array $gatewayResponse = []): bool
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse)
        ]);

        // Log the failure
        PaymentAuditLog::create([
            'event_type' => 'payment_failed',
            'entity_type' => 'centralized_payment',
            'entity_id' => $this->id,
            'user_id' => $this->customer_id,
            'new_values' => ['status' => 'failed', 'failure_reason' => $reason],
            'description' => 'Payment failed: ' . $reason,
        ]);

        // Update related order
        if ($this->order) {
            $this->order->update([
                'payment_status' => 'failed'
            ]);
        }

        return true;
    }

    /**
     * Calculate total commission for this payment
     */
    public function calculateTotalCommission(): array
    {
        if (!$this->order) {
            return ['total_commission' => 0, 'breakdown' => []];
        }

        $totalCommission = 0;
        $breakdown = [];

        foreach ($this->order->items as $item) {
            $commissionData = CommissionSetting::getCommissionRate(
                $item->seller->role,
                $item->product->product_type,
                $item->item_total
            );

            $commission = ($item->item_total * $commissionData['commission_rate']) / 100;
            $totalCommission += $commission;

            $breakdown[] = [
                'item_id' => $item->id,
                'seller_id' => $item->seller_id,
                'item_total' => $item->item_total,
                'commission_rate' => $commissionData['commission_rate'],
                'commission_amount' => $commission,
            ];
        }

        return [
            'total_commission' => $totalCommission,
            'breakdown' => $breakdown,
            'admin_earnings' => $totalCommission,
            'seller_earnings' => $this->gross_amount - $totalCommission,
        ];
    }

    /**
     * Get payment status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'processing' => 'info',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Stripe (International)',
            'sepay' => 'SePay (Vietnam)',
            default => ucfirst($this->payment_method)
        };
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'payment_reference' => 'required|string|unique:centralized_payments',
            'order_id' => 'required|exists:marketplace_orders,id',
            'customer_id' => 'required|exists:users,id',
            'customer_email' => 'required|email',
            'payment_method' => 'required|in:stripe,sepay',
            'gateway_transaction_id' => 'nullable|string',
            'gateway_payment_intent_id' => 'nullable|string',
            'gateway_response' => 'nullable|array',
            'gross_amount' => 'required|numeric|min:0',
            'gateway_fee' => 'nullable|numeric|min:0',
            'net_received' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,failed,cancelled,refunded',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->payment_reference)) {
                $model->payment_reference = static::generatePaymentReference();
            }
        });
    }
}
