<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceOrderItem extends Model
{
    use HasFactory;

    protected $table = 'marketplace_order_items';

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'seller_id',
        'product_name',
        'product_sku',
        'product_image',
        'product_description',
        'quantity',
        'unit_price',
        'sale_price',
        'total_price',
        'product_options',
        'product_specifications',
        'fulfillment_status',
        'quantity_shipped',
        'quantity_delivered',
        'quantity_refunded',
        'tracking_number',
        'carrier',
        'shipped_at',
        'delivered_at',
        'download_url',
        'download_count',
        'download_limit',
        'download_expires_at',
        'metadata',
    ];

    protected $casts = [
        'product_options' => 'array',
        'product_specifications' => 'array',
        'metadata' => 'array',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'download_expires_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
    }

    public function seller()
    {
        return $this->belongsTo(MarketplaceSeller::class, 'seller_id');
    }

    // Scopes
    public function scopeByFulfillmentStatus($query, $status)
    {
        return $query->where('fulfillment_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('fulfillment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('fulfillment_status', 'completed');
    }

    // Accessors
    public function getFulfillmentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'ready_to_ship' => 'Sẵn sàng gửi hàng',
            'shipped' => 'Đã gửi hàng',
            'delivered' => 'Đã giao hàng',
            'downloaded' => 'Đã tải xuống',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
        ];

        return $labels[$this->fulfillment_status] ?? $this->fulfillment_status;
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . ' VND';
    }

    // Methods
    public function canBeShipped()
    {
        return $this->fulfillment_status === 'ready_to_ship';
    }

    public function canBeCompleted()
    {
        return in_array($this->fulfillment_status, ['delivered', 'downloaded']);
    }

    public function isDigitalProduct()
    {
        return $this->product && $this->product->product_type === 'digital';
    }
}
