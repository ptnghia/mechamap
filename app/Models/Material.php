<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'category',
        'subcategory',
        'material_type',
        'grade',
        'alternative_designations',
        'density',
        'melting_point',
        'thermal_conductivity',
        'thermal_expansion',
        'specific_heat',
        'electrical_resistivity',
        'youngs_modulus',
        'shear_modulus',
        'bulk_modulus',
        'poissons_ratio',
        'yield_strength',
        'tensile_strength',
        'compressive_strength',
        'fatigue_strength',
        'hardness_hb',
        'hardness_hrc',
        'impact_energy',
        'elongation',
        'chemical_composition',
        'impurities',
        'machinability',
        'weldability',
        'formability',
        'heat_treatment',
        'standards',
        'specifications',
        'certifications',
        'typical_applications',
        'industries',
        'manufacturing_processes',
        'suppliers',
        'forms_available',
        'cost_per_kg',
        'availability',
        'environmental_impact',
        'safety_considerations',
        'recycling_info',
        'hazardous',
        'datasheet_path',
        'reference_documents',
        'test_reports',
        'tags',
        'keywords',
        'created_by_user',
        'verified_by',
        'verified_at',
        'status',
        'is_active',
        'is_featured',
        'usage_count',
        'view_count',
    ];

    protected $casts = [
        'alternative_designations' => 'array',
        'chemical_composition' => 'array',
        'impurities' => 'array',
        'machinability' => 'array',
        'weldability' => 'array',
        'formability' => 'array',
        'heat_treatment' => 'array',
        'standards' => 'array',
        'specifications' => 'array',
        'certifications' => 'array',
        'typical_applications' => 'array',
        'industries' => 'array',
        'manufacturing_processes' => 'array',
        'suppliers' => 'array',
        'forms_available' => 'array',
        'environmental_impact' => 'array',
        'safety_considerations' => 'array',
        'recycling_info' => 'array',
        'reference_documents' => 'array',
        'test_reports' => 'array',
        'tags' => 'array',
        'keywords' => 'array',
        'density' => 'decimal:4',
        'melting_point' => 'decimal:2',
        'thermal_conductivity' => 'decimal:4',
        'thermal_expansion' => 'decimal:8',
        'specific_heat' => 'decimal:4',
        'electrical_resistivity' => 'decimal:8',
        'youngs_modulus' => 'decimal:2',
        'shear_modulus' => 'decimal:2',
        'bulk_modulus' => 'decimal:2',
        'poissons_ratio' => 'decimal:4',
        'yield_strength' => 'decimal:2',
        'tensile_strength' => 'decimal:2',
        'compressive_strength' => 'decimal:2',
        'fatigue_strength' => 'decimal:2',
        'hardness_hb' => 'decimal:2',
        'hardness_hrc' => 'decimal:2',
        'impact_energy' => 'decimal:2',
        'elongation' => 'decimal:2',
        'cost_per_kg' => 'decimal:4',
        'hazardous' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    public function scopeHazardous($query)
    {
        return $query->where('hazardous', true);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Nháp',
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'deprecated' => 'Không còn sử dụng',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'Metal' => 'Kim loại',
            'Polymer' => 'Polyme',
            'Ceramic' => 'Gốm sứ',
            'Composite' => 'Vật liệu tổng hợp',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    public function getAvailabilityLabelAttribute()
    {
        $labels = [
            'Common' => 'Phổ biến',
            'Special Order' => 'Đặt hàng đặc biệt',
            'Limited' => 'Hạn chế',
            'Discontinued' => 'Ngừng sản xuất',
        ];

        return $labels[$this->availability] ?? $this->availability;
    }

    // Methods
    public function incrementUsageCount()
    {
        $this->increment('usage_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public function getStrengthToWeightRatio()
    {
        if ($this->tensile_strength && $this->density) {
            return round($this->tensile_strength / ($this->density * 9.81), 2);
        }
        return null;
    }

    public function getStiffnessToWeightRatio()
    {
        if ($this->youngs_modulus && $this->density) {
            return round(($this->youngs_modulus * 1000) / ($this->density * 9.81), 2);
        }
        return null;
    }
}
