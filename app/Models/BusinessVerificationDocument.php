<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Business Verification Document Model
 * 
 * Manages documents uploaded for business verification applications
 * with security, access control, and verification tracking
 */
class BusinessVerificationDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'document_type',
        'document_name',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'file_extension',
        'document_description',
        'document_date',
        'expiry_date',
        'issuing_authority',
        'document_number',
        'verification_status',
        'verification_notes',
        'verified_by',
        'verified_at',
        'file_hash',
        'is_encrypted',
        'encryption_key',
        'access_count',
        'last_accessed_at',
        'access_log',
        'has_thumbnail',
        'thumbnail_path',
        'is_processed',
        'ocr_text',
        'metadata',
        'quality_score',
        'is_legible',
        'is_complete',
        'is_authentic',
        'quality_notes',
        'contains_sensitive_data',
        'retention_until',
        'gdpr_compliant',
        'compliance_flags',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'retention_until' => 'date',
        'access_log' => 'array',
        'ocr_text' => 'array',
        'metadata' => 'array',
        'compliance_flags' => 'array',
        'is_encrypted' => 'boolean',
        'has_thumbnail' => 'boolean',
        'is_processed' => 'boolean',
        'is_legible' => 'boolean',
        'is_complete' => 'boolean',
        'is_authentic' => 'boolean',
        'contains_sensitive_data' => 'boolean',
        'gdpr_compliant' => 'boolean',
    ];

    // Document type constants
    const TYPE_BUSINESS_LICENSE = 'business_license';
    const TYPE_TAX_CERTIFICATE = 'tax_certificate';
    const TYPE_REGISTRATION_CERTIFICATE = 'registration_certificate';
    const TYPE_IDENTITY_DOCUMENT = 'identity_document';
    const TYPE_BANK_STATEMENT = 'bank_statement';
    const TYPE_UTILITY_BILL = 'utility_bill';
    const TYPE_INSURANCE_CERTIFICATE = 'insurance_certificate';
    const TYPE_QUALITY_CERTIFICATE = 'quality_certificate';
    const TYPE_TRADE_LICENSE = 'trade_license';
    const TYPE_VAT_CERTIFICATE = 'vat_certificate';
    const TYPE_OTHER = 'other';

    // Verification status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REQUIRES_RESUBMISSION = 'requires_resubmission';

    // Allowed file types
    const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    /**
     * Relationships
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(BusinessVerificationApplication::class, 'application_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', self::STATUS_PENDING);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', self::STATUS_VERIFIED);
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', self::STATUS_REJECTED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now());
    }

    public function scopeExpiringWithin($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>=', now());
    }

    /**
     * Accessors & Mutators
     */
    public function getDocumentTypeDisplayAttribute(): string
    {
        $types = [
            self::TYPE_BUSINESS_LICENSE => 'Giấy phép kinh doanh',
            self::TYPE_TAX_CERTIFICATE => 'Giấy chứng nhận thuế',
            self::TYPE_REGISTRATION_CERTIFICATE => 'Giấy chứng nhận đăng ký',
            self::TYPE_IDENTITY_DOCUMENT => 'Giấy tờ tùy thân',
            self::TYPE_BANK_STATEMENT => 'Sao kê ngân hàng',
            self::TYPE_UTILITY_BILL => 'Hóa đơn tiện ích',
            self::TYPE_INSURANCE_CERTIFICATE => 'Giấy chứng nhận bảo hiểm',
            self::TYPE_QUALITY_CERTIFICATE => 'Chứng nhận chất lượng',
            self::TYPE_TRADE_LICENSE => 'Giấy phép thương mại',
            self::TYPE_VAT_CERTIFICATE => 'Giấy chứng nhận VAT',
            self::TYPE_OTHER => 'Khác',
        ];

        return $types[$this->document_type] ?? 'Không xác định';
    }

    public function getVerificationStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Chờ xác minh</span>',
            self::STATUS_VERIFIED => '<span class="badge bg-success">Đã xác minh</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">Từ chối</span>',
            self::STATUS_REQUIRES_RESUBMISSION => '<span class="badge bg-secondary">Cần nộp lại</span>',
        ];

        return $badges[$this->verification_status] ?? '<span class="badge bg-light">Không xác định</span>';
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('admin.verification.documents.download', [
            'application' => $this->application_id,
            'document' => $this->id
        ]);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if (!$this->canBePreewed()) {
            return null;
        }

        return route('admin.verification.documents.preview', [
            'application' => $this->application_id,
            'document' => $this->id
        ]);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->has_thumbnail || !$this->thumbnail_path) {
            return null;
        }

        return Storage::url($this->thumbnail_path);
    }

    /**
     * Business Logic Methods
     */
    public function canBePreewed(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ]);
    }

    public function canBeVerified(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    public function canBeDownloaded(): bool
    {
        return Storage::exists($this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ]);
    }

    public function logAccess(User $user): void
    {
        $this->increment('access_count');
        $this->update(['last_accessed_at' => now()]);

        // Add to access log
        $accessLog = $this->access_log ?? [];
        $accessLog[] = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'accessed_at' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Keep only last 50 access records
        if (count($accessLog) > 50) {
            $accessLog = array_slice($accessLog, -50);
        }

        $this->update(['access_log' => $accessLog]);
    }

    public function generateSecureUrl(int $expiresInMinutes = 60): string
    {
        return Storage::temporaryUrl(
            $this->file_path,
            now()->addMinutes($expiresInMinutes)
        );
    }

    public function verify(User $verifier, string $status, ?string $notes = null): bool
    {
        if (!$this->canBeVerified() && $status !== self::STATUS_VERIFIED) {
            return false;
        }

        $this->update([
            'verification_status' => $status,
            'verification_notes' => $notes,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        // Log the verification action
        BusinessVerificationAuditTrail::create([
            'application_id' => $this->application_id,
            'document_id' => $this->id,
            'action_type' => 'document_verified',
            'performed_by' => $verifier->id,
            'action_description' => "Document {$this->document_name} was {$status}",
            'old_status' => self::STATUS_PENDING,
            'new_status' => $status,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return true;
    }

    public function delete(): bool
    {
        // Delete physical file
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        // Delete thumbnail if exists
        if ($this->has_thumbnail && $this->thumbnail_path && Storage::exists($this->thumbnail_path)) {
            Storage::delete($this->thumbnail_path);
        }

        return parent::delete();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            // Set retention date (7 years for business documents)
            if (!$document->retention_until) {
                $document->retention_until = now()->addYears(7);
            }

            // Generate file hash
            if (!$document->file_hash && $document->file_path) {
                $document->file_hash = hash_file('sha256', Storage::path($document->file_path));
            }
        });
    }
}
