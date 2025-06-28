<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TechnicalDrawing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'drawing_number',
        'description',
        'revision',
        'created_by',
        'company_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'drawing_type',
        'scale',
        'units',
        'dimensions',
        'sheet_size',
        'project_name',
        'part_number',
        'material_specification',
        'tolerances',
        'surface_finish',
        'drawing_standards',
        'material_standards',
        'manufacturing_notes',
        'version_number',
        'parent_drawing_id',
        'revision_notes',
        'approved_at',
        'approved_by',
        'visibility',
        'license_type',
        'price',
        'tags',
        'keywords',
        'industry_category',
        'application_area',
        'download_count',
        'view_count',
        'like_count',
        'rating_average',
        'rating_count',
        'status',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'tolerances' => 'array',
        'surface_finish' => 'array',
        'drawing_standards' => 'array',
        'material_standards' => 'array',
        'manufacturing_notes' => 'array',
        'tags' => 'array',
        'keywords' => 'array',
        'price' => 'decimal:2',
        'sheet_size' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->drawing_number)) {
                $model->drawing_number = 'TD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parentDrawing()
    {
        return $this->belongsTo(TechnicalDrawing::class, 'parent_drawing_id');
    }

    public function childDrawings()
    {
        return $this->hasMany(TechnicalDrawing::class, 'parent_drawing_id');
    }

    public function cadFiles()
    {
        return $this->hasMany(CADFile::class, 'technical_drawing_id');
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('drawing_type', $type);
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

    public function getVisibilityLabelAttribute()
    {
        $labels = [
            'public' => 'Công khai',
            'private' => 'Riêng tư',
            'company_only' => 'Chỉ công ty',
        ];

        return $labels[$this->visibility] ?? $this->visibility;
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
        return $this->is_active && $this->status === 'approved';
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
