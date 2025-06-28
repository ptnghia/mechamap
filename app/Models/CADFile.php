<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CADFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cad_files';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'file_number',
        'version',
        'created_by',
        'company_id',
        'file_path',
        'original_filename',
        'file_extension',
        'file_size',
        'mime_type',
        'checksum',
        'cad_software',
        'software_version',
        'compatible_software',
        'model_type',
        'geometry_type',
        'units',
        'bounding_box',
        'volume',
        'surface_area',
        'mass',
        'material_type',
        'material_properties',
        'manufacturing_methods',
        'manufacturing_constraints',
        'design_intent',
        'features',
        'parameters',
        'configurations',
        'technical_drawing_id',
        'related_files',
        'thumbnail_path',
        'design_standards',
        'tolerance_standards',
        'quality_requirements',
        'version_number',
        'parent_file_id',
        'version_notes',
        'approved_at',
        'approved_by',
        'visibility',
        'license_type',
        'price',
        'usage_rights',
        'tags',
        'keywords',
        'industry_category',
        'application_area',
        'complexity_level',
        'download_count',
        'view_count',
        'like_count',
        'rating_average',
        'rating_count',
        'processing_status',
        'processing_log',
        'processed_at',
        'status',
        'is_featured',
        'is_active',
        'virus_scanned',
        'virus_scan_at',
    ];

    protected $casts = [
        'compatible_software' => 'array',
        'units' => 'array',
        'bounding_box' => 'array',
        'material_properties' => 'array',
        'manufacturing_methods' => 'array',
        'manufacturing_constraints' => 'array',
        'features' => 'array',
        'parameters' => 'array',
        'configurations' => 'array',
        'related_files' => 'array',
        'design_standards' => 'array',
        'tolerance_standards' => 'array',
        'quality_requirements' => 'array',
        'usage_rights' => 'array',
        'tags' => 'array',
        'keywords' => 'array',
        'processing_log' => 'array',
        'volume' => 'decimal:6',
        'surface_area' => 'decimal:6',
        'mass' => 'decimal:6',
        'price' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'virus_scanned' => 'boolean',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'virus_scan_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->file_number)) {
                $model->file_number = 'CAD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(MarketplaceSeller::class, 'company_id');
    }

    public function technicalDrawing()
    {
        return $this->belongsTo(TechnicalDrawing::class, 'technical_drawing_id');
    }

    public function parentFile()
    {
        return $this->belongsTo(CADFile::class, 'parent_file_id');
    }

    public function childFiles()
    {
        return $this->hasMany(CADFile::class, 'parent_file_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeProcessed($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeVirusScanned($query)
    {
        return $query->where('virus_scanned', true);
    }

    public function scopeByModelType($query, $type)
    {
        return $query->where('model_type', $type);
    }

    public function scopeBySoftware($query, $software)
    {
        return $query->where('cad_software', $software);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Nháp',
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'archived' => 'Lưu trữ',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getProcessingStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
        ];

        return $labels[$this->processing_status] ?? $this->processing_status;
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Methods
    public function canBeDownloaded()
    {
        return $this->is_active &&
               $this->status === 'approved' &&
               $this->processing_status === 'completed' &&
               $this->virus_scanned;
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function markAsProcessed()
    {
        $this->update([
            'processing_status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsVirusScanned()
    {
        $this->update([
            'virus_scanned' => true,
            'virus_scan_at' => now(),
        ]);
    }
}
