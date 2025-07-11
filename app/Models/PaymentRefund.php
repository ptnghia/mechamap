<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * 💰 Payment Refund Model
 * 
 * Quản lý refund processing, tracking và seller adjustments
 * Tích hợp với payment gateways cho automated refunds
 */
class PaymentRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'refund_reference',
        'centralized_payment_id',
        'order_id',
        'customer_id',
        'dispute_id',
        'refund_type',
        'reason',
        'status',
        'original_amount',
        'refund_amount',
        'gateway_fee',
        'net_refund',
        'currency',
        'payment_method',
        'gateway_refund_id',
        'gateway_response',
        'gateway_error',
        'customer_reason',
        'admin_reason',
        'refund_items',
        'requested_by',
        'approved_by',
        'processed_by',
        'admin_notes',
        'internal_notes',
        'seller_adjustments',
        'adjust_seller_earnings',
        'seller_deduction',
        'requested_at',
        'approved_at',
        'processed_at',
        'completed_at',
        'failed_at',
        'customer_notified',
        'seller_notified',
        'customer_notified_at',
        'seller_notified_at',
        'metadata',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_refund' => 'decimal:2',
        'seller_deduction' => 'decimal:2',
        'gateway_response' => 'array',
        'refund_items' => 'array',
        'internal_notes' => 'array',
        'seller_adjustments' => 'array',
        'metadata' => 'array',
        'adjust_seller_earnings' => 'boolean',
        'customer_notified' => 'boolean',
        'seller_notified' => 'boolean',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'customer_notified_at' => 'datetime',
        'seller_notified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refund) {
            if (empty($refund->uuid)) {
                $refund->uuid = Str::uuid();
            }
            if (empty($refund->refund_reference)) {
                $refund->refund_reference = self::generateRefundReference();
            }
            if (empty($refund->requested_at)) {
                $refund->requested_at = now();
            }
            
            // Calculate net refund if not set
            if (empty($refund->net_refund)) {
                $refund->net_refund = $refund->refund_amount - $refund->gateway_fee;
            }
        });
    }

    /**
     * Generate unique refund reference
     */
    public static function generateRefundReference(): string
    {
        do {
            $reference = 'REF-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (self::where('refund_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Relationships
     */
    public function centralizedPayment(): BelongsTo
    {
        return $this->belongsTo(CentralizedPayment::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(PaymentDispute::class, 'dispute_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'processing' => 'primary',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getRefundTypeDisplayAttribute(): string
    {
        return match($this->refund_type) {
            'full' => 'Full Refund',
            'partial' => 'Partial Refund',
            'shipping' => 'Shipping Refund',
            'tax' => 'Tax Refund',
            'item' => 'Item Refund',
            'goodwill' => 'Goodwill Refund',
            'chargeback' => 'Chargeback Refund',
            'error' => 'Error Correction',
            default => ucfirst($this->refund_type)
        };
    }

    public function getReasonDisplayAttribute(): string
    {
        return match($this->reason) {
            'customer_request' => 'Customer Request',
            'product_defective' => 'Product Defective',
            'wrong_item' => 'Wrong Item',
            'not_delivered' => 'Not Delivered',
            'damaged_shipping' => 'Damaged in Shipping',
            'duplicate_payment' => 'Duplicate Payment',
            'billing_error' => 'Billing Error',
            'fraud_prevention' => 'Fraud Prevention',
            'dispute_resolution' => 'Dispute Resolution',
            'goodwill' => 'Goodwill',
            'admin_error' => 'Admin Error',
            'other' => 'Other',
            default => ucfirst($this->reason)
        };
    }

    public function getRefundPercentageAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 0;
        }

        return ($this->refund_amount / $this->original_amount) * 100;
    }

    /**
     * Business Logic Methods
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function approve(User $admin, string $notes = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);

        $this->addInternalNote("Refund approved by {$admin->name}", $admin);
        return true;
    }

    public function reject(User $admin, string $reason): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_notes' => $reason,
        ]);

        $this->addInternalNote("Refund rejected by {$admin->name}: {$reason}", $admin);
        return true;
    }

    public function markAsProcessing(User $admin): bool
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        $this->update([
            'status' => 'processing',
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);

        $this->addInternalNote("Refund processing started by {$admin->name}", $admin);
        return true;
    }

    public function markAsCompleted(string $gatewayRefundId = null, array $gatewayResponse = null): bool
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'gateway_refund_id' => $gatewayRefundId,
            'gateway_response' => $gatewayResponse,
        ]);

        $this->addInternalNote("Refund completed successfully");
        return true;
    }

    public function markAsFailed(string $error, array $gatewayResponse = null): bool
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'gateway_error' => $error,
            'gateway_response' => $gatewayResponse,
        ]);

        $this->addInternalNote("Refund failed: {$error}");
        return true;
    }

    public function addInternalNote(string $note, User $admin = null): void
    {
        $notes = $this->internal_notes ?? [];
        $notes[] = [
            'note' => $note,
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['internal_notes' => $notes]);
    }

    /**
     * Validation Rules
     */
    public static function validationRules(): array
    {
        return [
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
            'order_id' => 'required|exists:marketplace_orders,id',
            'customer_id' => 'required|exists:users,id',
            'refund_type' => 'required|in:full,partial,shipping,tax,item,goodwill,chargeback,error',
            'reason' => 'required|in:customer_request,product_defective,wrong_item,not_delivered,damaged_shipping,duplicate_payment,billing_error,fraud_prevention,dispute_resolution,goodwill,admin_error,other',
            'refund_amount' => 'required|numeric|min:0',
            'original_amount' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get refund statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'pending' => self::where('status', 'pending')->count(),
            'approved' => self::where('status', 'approved')->count(),
            'completed' => self::where('status', 'completed')->count(),
            'failed' => self::where('status', 'failed')->count(),
            'total_amount' => self::where('status', 'completed')->sum('refund_amount'),
            'average_amount' => self::where('status', 'completed')->avg('refund_amount'),
        ];
    }

    /**
     * Get refund types for dropdown
     */
    public static function getRefundTypes(): array
    {
        return [
            'full' => 'Full Refund',
            'partial' => 'Partial Refund',
            'shipping' => 'Shipping Refund',
            'tax' => 'Tax Refund',
            'item' => 'Item Refund',
            'goodwill' => 'Goodwill Refund',
            'chargeback' => 'Chargeback Refund',
            'error' => 'Error Correction',
        ];
    }

    /**
     * Get refund reasons for dropdown
     */
    public static function getRefundReasons(): array
    {
        return [
            'customer_request' => 'Customer Request',
            'product_defective' => 'Product Defective',
            'wrong_item' => 'Wrong Item',
            'not_delivered' => 'Not Delivered',
            'damaged_shipping' => 'Damaged in Shipping',
            'duplicate_payment' => 'Duplicate Payment',
            'billing_error' => 'Billing Error',
            'fraud_prevention' => 'Fraud Prevention',
            'dispute_resolution' => 'Dispute Resolution',
            'goodwill' => 'Goodwill',
            'admin_error' => 'Admin Error',
            'other' => 'Other',
        ];
    }
}
