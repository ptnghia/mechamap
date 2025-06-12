<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'user_id',
        'payment_method',
        'gateway_transaction_id',
        'payment_intent_id',
        'charge_id',
        'type',
        'status',
        'amount',
        'currency',
        'fee_amount',
        'net_amount',
        'gateway_response',
        'failure_reason',
        'receipt_url',
        'refund_transaction_id',
        'refunded_amount',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Boot method để tự động tạo transaction_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = static::generateTransactionId();
            }
        });
    }

    /**
     * Quan hệ với Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với refund transaction
     */
    public function refundTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'refund_transaction_id');
    }

    /**
     * Tạo transaction ID duy nhất
     */
    public static function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        } while (static::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Scope: Successful transactions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Payment transactions (không phải refund)
     */
    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope: Refund transactions
     */
    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    /**
     * Kiểm tra transaction có thể refund không
     */
    public function canBeRefunded(): bool
    {
        return $this->type === 'payment' &&
               $this->status === 'completed' &&
               $this->refunded_amount < $this->amount &&
               $this->processed_at > now()->subDays(30);
    }

    /**
     * Tính số tiền có thể refund
     */
    public function getRefundableAmount(): float
    {
        return (float) ($this->amount - $this->refunded_amount);
    }

    /**
     * Đánh dấu transaction là completed
     */
    public function markAsCompleted(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
        ]);

        // Cập nhật order status nếu cần
        if ($this->type === 'payment') {
            $this->order->markAsCompleted();
        }
    }

    /**
     * Đánh dấu transaction là failed
     */
    public function markAsFailed(string $reason, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'processed_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
        ]);
    }

    /**
     * Tạo refund transaction
     */
    public function createRefund(float $amount, string $reason = null): self
    {
        if (!$this->canBeRefunded()) {
            throw new \Exception('Transaction không thể refund');
        }

        if ($amount > $this->getRefundableAmount()) {
            throw new \Exception('Số tiền refund vượt quá số tiền có thể refund');
        }

        $refund = static::create([
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'payment_method' => $this->payment_method,
            'refund_transaction_id' => $this->id,
            'type' => 'refund',
            'status' => 'pending',
            'amount' => $amount,
            'currency' => $this->currency,
            'fee_amount' => $amount * 0.029, // Phí refund
            'net_amount' => $amount * 0.971,
            'gateway_response' => ['reason' => $reason],
        ]);

        // Cập nhật refunded amount
        $this->increment('refunded_amount', $amount);

        return $refund;
    }

    /**
     * Lấy thông tin fee breakdown
     */
    public function getFeeBreakdown(): array
    {
        $feeRate = 0.029; // 2.9%
        $stripeFee = $this->amount * $feeRate;
        $platformFee = 0; // Platform không lấy thêm phí payment

        return [
            'amount' => (float) $this->amount,
            'stripe_fee' => round($stripeFee, 2),
            'platform_fee' => $platformFee,
            'total_fees' => round($stripeFee + $platformFee, 2),
            'net_amount' => round($this->amount - $stripeFee - $platformFee, 2),
        ];
    }

    /**
     * Format amount cho display
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy payment gateway display name
     */
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Stripe (Card)',
            'vnpay' => 'VNPay',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            default => 'Unknown',
        };
    }
}
