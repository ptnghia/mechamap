<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 🔐 MechaMap Permission Model
 *
 * Quản lý permissions chi tiết cho hệ thống MechaMap
 * Tương thích với cấu trúc 4 nhóm và 14 roles hiện có
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'module',
        'action',
        'metadata',
        'is_system',
        'is_active',
        'parent_id',
        'dependencies',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'dependencies' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship với roles (many-to-many)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions')
            ->withPivot(['is_granted', 'conditions', 'restrictions', 'granted_by', 'granted_at', 'grant_reason'])
            ->withTimestamps();
    }

    /**
     * Parent permission (hierarchy)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    /**
     * Child permissions
     */
    public function children(): HasMany
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    /**
     * User tạo permission
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User cập nhật permission cuối
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Chỉ permissions đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Permissions theo category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Permissions theo module
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope: Permissions hệ thống
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Lấy full name của permission (category.module.action)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->category}.{$this->module}.{$this->action}";
    }

    /**
     * Kiểm tra permission có dependencies không
     */
    public function hasDependencies(): bool
    {
        return !empty($this->dependencies);
    }

    /**
     * Lấy danh sách permissions phụ thuộc
     */
    public function getDependentPermissions()
    {
        if (!$this->hasDependencies()) {
            return collect();
        }

        return static::whereIn('name', $this->dependencies)->get();
    }

    /**
     * Kiểm tra permission có thể xóa không
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->roles()->count() === 0;
    }
}
