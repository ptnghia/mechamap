<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Region Model
 *
 * Quản lý khu vực/vùng miền trong từng quốc gia
 * Hỗ trợ phân loại forum theo địa lý và chuyên môn
 */
class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'name_local',
        'code',
        'type',
        'timezone',
        'latitude',
        'longitude',
        'industrial_zones',
        'universities',
        'major_companies',
        'specialization_areas',
        'forum_moderator_timezone',
        'local_standards',
        'common_materials',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'is_featured',
        'forum_count',
        'user_count',
        'thread_count',
    ];

    protected $casts = [
        'industrial_zones' => 'array',
        'universities' => 'array',
        'major_companies' => 'array',
        'specialization_areas' => 'array',
        'local_standards' => 'array',
        'common_materials' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ===================================================================
    // RELATIONSHIPS
    // ===================================================================

    /**
     * Quốc gia chứa khu vực này
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Các forum trong khu vực này
     */
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class)->orderBy('order')->orderBy('name');
    }

    /**
     * Các forum đang hoạt động
     */
    public function activeForums(): HasMany
    {
        return $this->forums()->where('is_active', true);
    }

    /**
     * Users thuộc khu vực này
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Active users trong khu vực
     */
    public function activeUsers(): HasMany
    {
        return $this->users()->where('is_active', true);
    }

    /**
     * Threads trong tất cả forum của khu vực
     */
    public function threads(): HasMany
    {
        return $this->hasManyThrough(Thread::class, Forum::class);
    }

    // ===================================================================
    // SCOPES
    // ===================================================================

    /**
     * Scope cho khu vực đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope cho khu vực nổi bật
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope theo loại khu vực
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope theo quốc gia
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope cho khu vực có tọa độ
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // ===================================================================
    // MECHANICAL ENGINEERING METHODS
    // ===================================================================

    /**
     * Lấy danh sách khu công nghiệp
     */
    public function getIndustrialZones(): array
    {
        return $this->industrial_zones ?? [];
    }

    /**
     * Lấy danh sách trường đại học kỹ thuật
     */
    public function getUniversities(): array
    {
        return $this->universities ?? [];
    }

    /**
     * Lấy danh sách công ty lớn
     */
    public function getMajorCompanies(): array
    {
        return $this->major_companies ?? [];
    }

    /**
     * Lấy các lĩnh vực chuyên môn
     */
    public function getSpecializationAreas(): array
    {
        return $this->specialization_areas ?? [];
    }

    /**
     * Lấy tiêu chuẩn địa phương
     */
    public function getLocalStandards(): array
    {
        return $this->local_standards ?? [];
    }

    /**
     * Lấy vật liệu phổ biến trong khu vực
     */
    public function getCommonMaterials(): array
    {
        return $this->common_materials ?? [];
    }

    /**
     * Kiểm tra có chuyên về một lĩnh vực không
     */
    public function specializesIn(string $area): bool
    {
        $areas = $this->getSpecializationAreas();
        return in_array($area, $areas, true);
    }

    /**
     * Kiểm tra có khu công nghiệp không
     */
    public function hasIndustrialZones(): bool
    {
        return !empty($this->getIndustrialZones());
    }

    /**
     * Kiểm tra có trường đại học kỹ thuật không
     */
    public function hasUniversities(): bool
    {
        return !empty($this->getUniversities());
    }

    // ===================================================================
    // LOCATION METHODS
    // ===================================================================

    /**
     * Lấy tọa độ GPS
     */
    public function getCoordinates(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }
        return null;
    }

    /**
     * Kiểm tra có tọa độ không
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Tính khoảng cách đến khu vực khác (km)
     */
    public function distanceTo(Region $other): ?float
    {
        if (!$this->hasCoordinates() || !$other->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // km

        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($other->latitude);
        $lon2 = deg2rad($other->longitude);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon/2) * sin($deltaLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Format tên hiển thị với icon
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if ($this->icon) {
            $name = $this->icon . ' ' . $name;
        }
        return $name;
    }

    /**
     * Lấy tên đầy đủ với quốc gia
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->country->name;
    }

    /**
     * Lấy URL icon
     */
    public function getIconUrlAttribute(): ?string
    {
        if ($this->icon && str_starts_with($this->icon, 'http')) {
            return $this->icon;
        }
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        return null;
    }

    /**
     * Lấy timezone hiệu quả (của region hoặc country)
     */
    public function getEffectiveTimezone(): string
    {
        return $this->timezone ?? $this->country->timezone ?? 'UTC';
    }

    /**
     * Lấy loại khu vực đã format
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'province' => 'Tỉnh/Thành phố',
            'state' => 'Bang',
            'prefecture' => 'Tỉnh',
            'region' => 'Vùng',
            'city' => 'Thành phố',
            'zone' => 'Khu vực',
            default => ucfirst($this->type)
        };
    }

    // ===================================================================
    // STATISTICAL METHODS
    // ===================================================================

    /**
     * Cập nhật statistics counters
     */
    public function updateStatistics(): void
    {
        $this->update([
            'forum_count' => $this->forums()->count(),
            'user_count' => $this->users()->count(),
            'thread_count' => $this->threads()->count(),
        ]);
    }

    /**
     * Lấy forum phổ biến nhất
     */
    public function getMostPopularForum(): ?Forum
    {
        return $this->forums()
            ->withCount('threads')
            ->orderByDesc('threads_count')
            ->first();
    }

    // ===================================================================
    // QUERY METHODS
    // ===================================================================

    /**
     * Lấy danh sách khu vực cho dropdown
     */
    public static function getForDropdown(?int $countryId = null): array
    {
        $query = static::active()->orderBy('sort_order')->orderBy('name');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Lấy khu vực theo code trong quốc gia
     */
    public static function findByCode(string $code, int $countryId): ?static
    {
        return static::where('code', strtoupper($code))
            ->where('country_id', $countryId)
            ->first();
    }

    /**
     * Tìm khu vực gần nhất theo tọa độ
     */
    public static function findNearest(float $lat, float $lng, int $limit = 5): Collection
    {
        // Simple distance calculation - for production use proper geo queries
        return static::withCoordinates()
            ->get()
            ->map(function ($region) use ($lat, $lng) {
                $distance = sqrt(
                    pow($region->latitude - $lat, 2) +
                    pow($region->longitude - $lng, 2)
                );
                $region->calculated_distance = $distance;
                return $region;
            })
            ->sortBy('calculated_distance')
            ->take($limit);
    }

    /**
     * Lấy khu vực với statistics
     */
    public static function withStatistics()
    {
        return static::withCount([
            'forums',
            'activeForums',
            'users',
            'activeUsers',
            'threads'
        ]);
    }
}
