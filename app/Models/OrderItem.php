<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'technical_product_id',
        'seller_id',
        'product_title',
        'product_description',
        'product_snapshot',
        'quantity',
        'unit_price',
        'total_price',
        'seller_earnings',
        'platform_fee',
        'license_type',
        'license_terms',
        'license_expires_at',
        'download_count',
        'download_limit',
        'first_downloaded_at',
        'last_downloaded_at',
        'status',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'seller_earnings' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'license_terms' => 'array',
        'license_expires_at' => 'datetime',
        'first_downloaded_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    /**
     * Quan hệ với Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ với TechnicalProduct
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(TechnicalProduct::class, 'technical_product_id');
    }

    /**
     * Quan hệ với User (seller)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Quan hệ với SellerEarnings
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(SellerEarning::class);
    }

    /**
     * Quan hệ với SecureDownloads
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(SecureDownload::class);
    }

    /**
     * Scope: Items đang active (có thể download)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Items của một seller cụ thể
     */
    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Kiểm tra có thể download không
     */
    public function canDownload(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Kiểm tra giới hạn download
        if ($this->download_limit && $this->download_count >= $this->download_limit) {
            return false;
        }

        // Kiểm tra license expiry
        if ($this->license_expires_at && $this->license_expires_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * Tăng download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');

        if ($this->first_downloaded_at === null) {
            $this->update(['first_downloaded_at' => now()]);
        }

        $this->update(['last_downloaded_at' => now()]);
    }

    /**
     * Tạo earning record cho seller
     */
    public function createSellerEarning(): SellerEarning
    {
        return SellerEarning::create([
            'seller_id' => $this->seller_id,
            'order_item_id' => $this->id,
            'technical_product_id' => $this->technical_product_id,
            'gross_amount' => $this->total_price,
            'platform_fee' => $this->platform_fee,
            'payment_fee' => 0,
            'tax_amount' => 0,
            'net_amount' => $this->seller_earnings,
            'platform_fee_rate' => $this->platform_fee / $this->total_price,
            'payment_fee_rate' => 0,
            'payout_status' => 'pending',
            'available_at' => now()->addDays(7), // Có thể rút sau 7 ngày
        ]);
    }

    /**
     * Kích hoạt license
     */
    public function activateLicense(): void
    {
        $this->update([
            'status' => 'active',
            'license_expires_at' => $this->license_type === 'single'
                ? now()->addYears(10) // Single license: 10 năm
                : null, // Commercial license: không hết hạn
        ]);

        // Tạo earning record cho seller
        $this->createSellerEarning();
    }

    /**
     * Thu hồi license
     */
    public function revokeLicense(string $reason = null): void
    {
        $this->update([
            'status' => 'revoked',
            'license_expires_at' => now(),
        ]);

        // Cập nhật earning status
        $this->earnings()->update(['payout_status' => 'failed']);
    }

    /**
     * Lấy thông tin download còn lại
     */
    public function getDownloadsRemaining(): ?int
    {
        if ($this->download_limit === null) {
            return null; // Unlimited
        }

        return max(0, $this->download_limit - $this->download_count);
    }

    /**
     * Lấy thông tin license status
     */
    public function getLicenseStatus(): array
    {
        $status = $this->status;
        $canDownload = $this->canDownload();
        $downloadsRemaining = $this->getDownloadsRemaining();

        $expiresIn = null;
        if ($this->license_expires_at) {
            $expiresIn = $this->license_expires_at->diffInDays(now());
        }

        return [
            'status' => $status,
            'can_download' => $canDownload,
            'downloads_remaining' => $downloadsRemaining,
            'expires_in_days' => $expiresIn,
            'last_downloaded' => $this->last_downloaded_at?->format('Y-m-d H:i:s'),
        ];
    }
}
