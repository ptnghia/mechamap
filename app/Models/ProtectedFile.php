<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProtectedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'original_filename',
        'encrypted_filename',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'file_type',
        'software_required',
        'description',
        'encryption_key',
        'encryption_method',
        'access_level',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'file_type' => 'cad_file',
        'encryption_method' => 'AES-256-CBC',
        'access_level' => 'full_access',
        'download_count' => 0,
        'is_active' => true,
    ];

    protected $hidden = [
        'encryption_key',
        'encrypted_filename',
        'file_path',
    ];

    /**
     * Get the product this file belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(TechnicalProduct::class, 'product_id');
    }

    /**
     * Get secure downloads for this file
     */
    public function secureDownloads(): HasMany
    {
        return $this->hasMany(SecureDownload::class, 'protected_file_id');
    }

    /**
     * Get completed downloads
     */
    public function completedDownloads(): HasMany
    {
        return $this->secureDownloads()->where('is_completed', true);
    }

    /**
     * Scope for active files
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by file type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope by access level
     */
    public function scopeByAccessLevel($query, string $accessLevel)
    {
        return $query->where('access_level', $accessLevel);
    }

    /**
     * Get CAD files
     */
    public function scopeCadFiles($query)
    {
        return $query->where('file_type', 'cad_file');
    }

    /**
     * Get documentation files
     */
    public function scopeDocumentation($query)
    {
        return $query->where('file_type', 'documentation');
    }

    /**
     * Get sample files (free preview)
     */
    public function scopeSamples($query)
    {
        return $query->where('access_level', 'sample');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_filename, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is CAD file
     */
    public function isCadFile(): bool
    {
        $cadExtensions = ['dwg', 'step', 'stp', 'iges', 'igs', 'stl', 'obj', 'sldprt', 'sldasm'];
        return in_array(strtolower($this->extension), $cadExtensions);
    }

    /**
     * Check if file is document
     */
    public function isDocument(): bool
    {
        $docExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        return in_array(strtolower($this->extension), $docExtensions);
    }

    /**
     * Check if file is image
     */
    public function isImage(): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        return in_array(strtolower($this->extension), $imageExtensions);
    }

    /**
     * Increment download count
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * Check if user can access this file
     */
    public function canBeAccessedBy(?User $user): bool
    {
        // Sample files can be accessed by anyone
        if ($this->access_level === 'sample') {
            return true;
        }

        // Preview files can be accessed by anyone
        if ($this->access_level === 'preview') {
            return true;
        }

        // Full access files require purchase
        if ($this->access_level === 'full_access') {
            if (!$user) {
                return false;
            }

            // Check if user is the seller
            if ($user->id === $this->product->seller_id) {
                return true;
            }

            // Check if user has purchased the product
            return $this->product->isPurchasedBy($user);
        }

        return false;
    }

    /**
     * Get file icon based on type
     */
    public function getIconAttribute(): string
    {
        $iconMap = [
            'cad_file' => 'https://api.iconify.design/file-icons:solidworks.svg',
            'documentation' => 'https://api.iconify.design/vscode-icons:file-type-pdf2.svg',
            'calculation' => 'https://api.iconify.design/vscode-icons:file-type-excel2.svg',
            'tutorial' => 'https://api.iconify.design/vscode-icons:file-type-video.svg',
            'sample' => 'https://api.iconify.design/vscode-icons:default-file.svg',
        ];

        return $iconMap[$this->file_type] ?? $iconMap['sample'];
    }
}
