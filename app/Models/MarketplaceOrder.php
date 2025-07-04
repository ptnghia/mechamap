<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketplaceOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marketplace_orders';

    protected $fillable = [
        'uuid',
        'order_number',
        'customer_id',
        'customer_email',
        'customer_phone',
        'status',
        'payment_status',
        'fulfillment_status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'shipping_address',
        'billing_address',
        'shipping_method',
        'shipping_cost',
        'tracking_number',
        'carrier',
        'payment_method',
        'payment_gateway',
        'payment_transaction_id',
        'payment_details',
        'coupon_code',
        'discount_details',
        'customer_notes',
        'admin_notes',
        'placed_at',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'payment_details' => 'array',
        'discount_details' => 'array',
        'metadata' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'placed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->order_number)) {
                $model->order_number = 'MO-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(MarketplaceOrderItem::class, 'order_id');
    }

    public function sellers()
    {
        return $this->belongsToMany(MarketplaceSeller::class, 'marketplace_order_items', 'order_id', 'seller_id')
                    ->distinct();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi hàng',
            'delivered' => 'Đã giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            'partially_refunded' => 'Hoàn tiền một phần',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    // Methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeShipped()
    {
        return $this->status === 'processing' && $this->payment_status === 'paid';
    }

    public function canBeCompleted()
    {
        return $this->status === 'delivered';
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'dark',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPaymentStatusColor()
    {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'dark',
            'partially_refunded' => 'warning',
        ];

        return $colors[$this->payment_status] ?? 'secondary';
    }

    public function canReorder()
    {
        // Allow reorder if order is completed or delivered and payment is successful
        return in_array($this->status, ['completed', 'delivered']) &&
               $this->payment_status === 'paid';
    }

    public function hasTimeline()
    {
        // Check if order has any timeline events (you can implement this based on your timeline logic)
        return !empty($this->tracking_number) ||
               in_array($this->status, ['shipped', 'delivered', 'completed']);
    }

    public function isStatusPassed($status)
    {
        // Define status order progression
        $statusOrder = [
            'pending' => 1,
            'confirmed' => 2,
            'processing' => 3,
            'shipped' => 4,
            'delivered' => 5,
            'completed' => 6
        ];

        $currentStatusOrder = $statusOrder[$this->status] ?? 0;
        $checkStatusOrder = $statusOrder[$status] ?? 0;

        return $currentStatusOrder > $checkStatusOrder;
    }
}
