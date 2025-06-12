<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technical_product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_snapshot',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Quan hệ với User (người mua)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với TechnicalProduct
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(TechnicalProduct::class, 'technical_product_id');
    }

    /**
     * Scope: Cart items đang active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Kiểm tra cart item có còn hợp lệ không
     */
    public function isValid(): bool
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at > now());
    }

    /**
     * Cập nhật tổng giá dựa trên quantity và unit_price
     */
    public function updateTotal(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    /**
     * Thêm sản phẩm vào cart hoặc cập nhật quantity
     */
    public static function addOrUpdate(int $userId, int $productId, int $quantity = 1, string $licenseType = 'standard'): self
    {
        $product = TechnicalProduct::findOrFail($productId);

        $cartItem = static::where('user_id', $userId)
                          ->where('technical_product_id', $productId)
                          ->where('license_type', $licenseType)
                          ->first();

        if ($cartItem) {
            // Cập nhật quantity và price
            $cartItem->quantity = $quantity;
            $cartItem->unit_price = $product->sale_price ?? $product->price;
            $cartItem->updateTotal();
            $cartItem->save();
            return $cartItem;
        }

        // Tạo mới
        return static::create([
            'user_id' => $userId,
            'technical_product_id' => $productId,
            'quantity' => $quantity,
            'license_type' => $licenseType,
            'unit_price' => $product->sale_price ?? $product->price,
            'total_price' => $quantity * ($product->sale_price ?? $product->price),
            'product_snapshot' => [
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'is_active' => $product->status === 'approved',
            ],
            'status' => 'active',
            'expires_at' => now()->addDays(7), // Cart expires sau 7 ngày
        ]);
    }
}
