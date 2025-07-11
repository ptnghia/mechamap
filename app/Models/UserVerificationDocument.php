<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * 📄 User Verification Document Model
 * 
 * Manages business verification documents uploaded by users
 */
class UserVerificationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'original_name',
        'file_path',
        'file_hash',
        'file_size',
        'mime_type',
        'verification_status',
        'verification_notes',
        'verified_at',
        'verified_by',
        'metadata',
        'expires_at',
        'is_primary',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_primary' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Document types available
     */
    public const DOCUMENT_TYPES = [
        'business_license' => 'Giấy phép kinh doanh',
        'tax_certificate' => 'Giấy chứng nhận thuế',
        'company_registration' => 'Giấy đăng ký doanh nghiệp',
        'vat_certificate' => 'Giấy chứng nhận VAT',
        'industry_license' => 'Giấy phép ngành nghề',
        'quality_certificate' => 'Chứng chỉ chất lượng',
        'other' => 'Tài liệu khác',
    ];

    /**
     * Verification statuses
     */
    public const VERIFICATION_STATUSES = [
        'pending' => 'Chờ xác minh',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
        'expired' => 'Hết hạn',
    ];

    /**
     * Get the user that owns the document
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified this document
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get access logs for this document
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(UserDocumentAccessLog::class, 'document_id');
    }

    /**
     * Scope: Get documents by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope: Get primary documents
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Get documents by verification status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('verification_status', $status);
    }

    /**
     * Scope: Get pending documents
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope: Get approved documents
     */
    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope: Get expired documents
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('verification_status', '!=', 'expired');
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabel(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get verification status label
     */
    public function getVerificationStatusLabel(): string
    {
        return self::VERIFICATION_STATUSES[$this->verification_status] ?? $this->verification_status;
    }

    /**
     * Get file URL
     */
    public function getFileUrl(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHuman(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if document is pending verification
     */
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if document is approved
     */
    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if document is rejected
     */
    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Approve the document
     */
    public function approve(User $verifier, string $notes = null): bool
    {
        return $this->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Reject the document
     */
    public function reject(User $verifier, string $notes): bool
    {
        return $this->update([
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Mark document as expired
     */
    public function markExpired(): bool
    {
        return $this->update([
            'verification_status' => 'expired',
        ]);
    }

    /**
     * Set as primary document for this type
     */
    public function setAsPrimary(): bool
    {
        // Remove primary flag from other documents of same type
        static::where('user_id', $this->user_id)
              ->where('document_type', $this->document_type)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);

        return $this->update(['is_primary' => true]);
    }

    /**
     * Generate file hash
     */
    public function generateFileHash(): string
    {
        if (Storage::exists($this->file_path)) {
            return hash_file('sha256', Storage::path($this->file_path));
        }
        
        return '';
    }

    /**
     * Verify file integrity
     */
    public function verifyFileIntegrity(): bool
    {
        if (!$this->file_hash) {
            return false;
        }
        
        return $this->file_hash === $this->generateFileHash();
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }
        
        return true;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate file hash when creating
        static::creating(function ($document) {
            if (!$document->file_hash && $document->file_path) {
                $document->file_hash = $document->generateFileHash();
            }
        });

        // Delete file when model is deleted
        static::deleting(function ($document) {
            $document->deleteFile();
        });

        // Update expired documents
        static::updating(function ($document) {
            if ($document->isExpired() && $document->verification_status !== 'expired') {
                $document->verification_status = 'expired';
            }
        });
    }
}
