<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 👑 MechaMap Role Model
 *
 * Quản lý roles theo cấu trúc 4 nhóm của MechaMap:
 * - system_management (Level 1-3)
 * - community_management (Level 4-6)
 * - community_members (Level 7-10)
 * - business_partners (Level 11-14)
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'role_group',
        'hierarchy_level',
        'default_permissions',
        'restricted_permissions',
        'color',
        'icon',
        'is_visible',
        'is_system',
        'is_active',
        'can_be_assigned',
        'max_users',
        'business_rules',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'default_permissions' => 'array',
        'restricted_permissions' => 'array',
        'business_rules' => 'array',
        'is_visible' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'can_be_assigned' => 'boolean',
    ];

    /**
     * Relationship với permissions (many-to-many)
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions')
            ->withPivot(['is_granted', 'conditions', 'restrictions', 'granted_by', 'granted_at', 'grant_reason'])
            ->withTimestamps();
    }

    /**
     * Relationship với users (many-to-many)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_roles')
            ->withPivot(['is_primary', 'assigned_at', 'expires_at', 'assigned_by', 'assignment_reason', 'assignment_conditions', 'is_active', 'deactivated_at', 'deactivated_by'])
            ->withTimestamps();
    }

    /**
     * User tạo role
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User cập nhật role cuối
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Chỉ roles đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Roles có thể hiển thị
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope: Roles có thể gán cho user
     */
    public function scopeAssignable($query)
    {
        return $query->where('can_be_assigned', true);
    }

    /**
     * Scope: Roles theo nhóm
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('role_group', $group);
    }

    /**
     * Scope: Roles hệ thống
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Lấy tên hiển thị của role group
     */
    public function getRoleGroupDisplayNameAttribute(): string
    {
        $groups = config('mechamap_permissions.role_groups');
        return $groups[$this->role_group]['name'] ?? 'Không xác định';
    }

    /**
     * Lấy màu của role group
     */
    public function getRoleGroupColorAttribute(): string
    {
        $groups = config('mechamap_permissions.role_groups');
        return $groups[$this->role_group]['color'] ?? 'secondary';
    }

    /**
     * Lấy icon của role group
     */
    public function getRoleGroupIconAttribute(): string
    {
        $groups = config('mechamap_permissions.role_groups');
        return $groups[$this->role_group]['icon'] ?? 'fas fa-user';
    }

    /**
     * Kiểm tra role có thể xóa không
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->users()->count() === 0;
    }

    /**
     * Lấy số lượng users hiện tại
     */
    public function getUsersCountAttribute(): int
    {
        return $this->users()->wherePivot('is_active', true)->count();
    }



    /**
     * Kiểm tra có thể gán thêm user không
     */
    public function canAssignMoreUsers(): bool
    {
        if ($this->max_users === null) {
            return true;
        }

        return $this->users_count < $this->max_users;
    }

    /**
     * Lấy tất cả permissions của role (bao gồm default + assigned)
     */
    public function getAllPermissions()
    {
        $assignedPermissions = $this->permissions()->where('is_granted', true)->pluck('name')->toArray();
        $defaultPermissions = $this->default_permissions ?? [];

        return array_unique(array_merge($defaultPermissions, $assignedPermissions));
    }

    /**
     * Kiểm tra role có permission không
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getAllPermissions());
    }
}
