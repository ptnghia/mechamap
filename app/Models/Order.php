<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'tax_amount',
        'processing_fee',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_intent_id',
        'transaction_id',
        'status',
        'billing_address',
        'invoice_number',
        'metadata',
        'notes',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
        'metadata' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot method để tự động tạo order_number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Quan hệ với User (người mua)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với OrderItems
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Quan hệ với PaymentTransactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Tạo order number duy nhất
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'MM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Scope: Orders đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
                     ->where('payment_status', 'completed');
    }

    /**
     * Scope: Orders đang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Kiểm tra order có thể hủy không
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               in_array($this->payment_status, ['pending', 'processing']);
    }

    /**
     * Kiểm tra order có thể refund không
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' &&
               $this->payment_status === 'completed' &&
               $this->completed_at > now()->subDays(30); // 30 ngày
    }

    /**
     * Tính tổng số tiền bao gồm tax và fee
     */
    public function calculateTotal(): void
    {
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->processing_fee - $this->discount_amount;
        $this->save();
    }

    /**
     * Đánh dấu order là confirmed
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Đánh dấu order là completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'completed',
            'completed_at' => now(),
        ]);

        // Kích hoạt license cho tất cả order items
        $this->items()->update(['status' => 'active']);
    }

    /**
     * Hủy order
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $reason,
        ]);
    }

    /**
     * Tạo order từ shopping cart
     */
    public static function createFromCart(User $user, array $billingAddress): self
    {
        $cartItems = ShoppingCart::where('user_id', $user->id)
                                ->active()
                                ->with('product')
                                ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Giỏ hàng trống, không thể tạo đơn hàng');
        }

        $subtotal = $cartItems->sum('total_price');
        $processingFee = $subtotal * 0.029; // 2.9% processing fee
        $taxAmount = 0; // Tạm thời chưa tính thuế

        $order = static::create([
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'processing_fee' => $processingFee,
            'discount_amount' => 0,
            'total_amount' => $subtotal + $processingFee + $taxAmount,
            'billing_address' => $billingAddress,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Tạo order items từ cart items
        foreach ($cartItems as $cartItem) {
            $order->items()->create([
                'technical_product_id' => $cartItem->technical_product_id,
                'seller_id' => $cartItem->product->seller_id,
                'product_title' => $cartItem->product->title,
                'product_description' => $cartItem->product->description,
                'product_snapshot' => $cartItem->product_snapshot,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'total_price' => $cartItem->total_price,
                'seller_earnings' => $cartItem->total_price * 0.85, // 85% cho seller
                'platform_fee' => $cartItem->total_price * 0.15, // 15% platform fee
                'license_type' => 'single',
                'download_limit' => 10,
                'status' => 'pending',
            ]);
        }

        // Xóa cart items sau khi tạo order
        $cartItems->each->delete();

        return $order;
    }
}
