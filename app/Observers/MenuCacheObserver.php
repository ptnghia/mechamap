<?php

namespace App\Observers;

use App\Models\User;
use App\Services\MenuCacheService;
use App\Services\MenuLoggingService;
use Illuminate\Support\Facades\Log;

/**
 * Menu Cache Observer
 * 
 * Observer để tự động invalidate menu cache khi có thay đổi
 * liên quan đến user roles, permissions, hoặc business verification
 */
class MenuCacheObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user): void
    {
        $this->handleUserChanges($user, 'updated');
    }

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        $this->handleUserChanges($user, 'created');
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user): void
    {
        $this->handleUserChanges($user, 'deleted');
    }

    /**
     * Handle user changes and invalidate cache accordingly
     *
     * @param User $user
     * @param string $action
     * @return void
     */
    private function handleUserChanges(User $user, string $action): void
    {
        try {
            $changedAttributes = $user->getDirty();
            $originalAttributes = $user->getOriginal();

            // Check for role changes
            if (isset($changedAttributes['role'])) {
                $this->handleRoleChange($user, $originalAttributes['role'] ?? null, $changedAttributes['role'], $action);
            }

            // Check for business verification changes
            if (isset($changedAttributes['business_verified'])) {
                $this->handleBusinessVerificationChange($user, $action);
            }

            // Check for permission-related changes
            if ($this->hasPermissionRelatedChanges($changedAttributes)) {
                $this->handlePermissionRelatedChanges($user, $changedAttributes, $action);
            }

            // Always invalidate user-specific cache for any changes
            if (!empty($changedAttributes) || $action === 'deleted') {
                MenuCacheService::invalidateUserMenuCache($user);
                MenuCacheService::invalidateUserPermissionCache($user);
            }

        } catch (\Exception $e) {
            Log::error("Error in MenuCacheObserver", [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle role changes
     *
     * @param User $user
     * @param string|null $oldRole
     * @param string $newRole
     * @param string $action
     * @return void
     */
    private function handleRoleChange(User $user, ?string $oldRole, string $newRole, string $action): void
    {
        // Invalidate cache for both old and new roles
        if ($oldRole && $oldRole !== $newRole) {
            MenuCacheService::invalidateRoleMenuCache($oldRole);
            
            // Log role change for security
            MenuLoggingService::logConfigurationError("User role changed", [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'action' => $action
            ]);
        }

        MenuCacheService::invalidateRoleMenuCache($newRole);

        // Warm up cache for new role if it's a common role
        if ($this->isCommonRole($newRole)) {
            MenuCacheService::warmUpRoleCache($newRole);
        }

        Log::info("Menu cache invalidated due to role change", [
            'user_id' => $user->id,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'action' => $action
        ]);
    }

    /**
     * Handle business verification changes
     *
     * @param User $user
     * @param string $action
     * @return void
     */
    private function handleBusinessVerificationChange(User $user, string $action): void
    {
        // Business verification affects menu permissions for business roles
        if ($this->isBusinessRole($user->role)) {
            MenuCacheService::invalidateUserMenuCache($user);
            MenuCacheService::invalidateUserPermissionCache($user);
            MenuCacheService::invalidateRoleMenuCache($user->role);

            Log::info("Menu cache invalidated due to business verification change", [
                'user_id' => $user->id,
                'role' => $user->role,
                'business_verified' => $user->business_verified,
                'action' => $action
            ]);
        }
    }

    /**
     * Handle permission-related changes
     *
     * @param User $user
     * @param array $changedAttributes
     * @param string $action
     * @return void
     */
    private function handlePermissionRelatedChanges(User $user, array $changedAttributes, string $action): void
    {
        // Invalidate permission cache
        MenuCacheService::invalidateUserPermissionCache($user);

        // If it affects menu structure, invalidate menu cache too
        if ($this->affectsMenuStructure($changedAttributes)) {
            MenuCacheService::invalidateUserMenuCache($user);
            MenuCacheService::invalidateRoleMenuCache($user->role);
        }

        Log::info("Menu cache invalidated due to permission changes", [
            'user_id' => $user->id,
            'role' => $user->role,
            'changed_attributes' => array_keys($changedAttributes),
            'action' => $action
        ]);
    }

    /**
     * Check if changes are permission-related
     *
     * @param array $changedAttributes
     * @return bool
     */
    private function hasPermissionRelatedChanges(array $changedAttributes): bool
    {
        $permissionFields = [
            'status',           // User status affects permissions
            'email_verified_at', // Email verification affects some permissions
            'banned_at',        // Ban status affects all permissions
            'suspended_at',     // Suspension affects permissions
        ];

        return !empty(array_intersect(array_keys($changedAttributes), $permissionFields));
    }

    /**
     * Check if changes affect menu structure
     *
     * @param array $changedAttributes
     * @return bool
     */
    private function affectsMenuStructure(array $changedAttributes): bool
    {
        $menuStructureFields = [
            'status',
            'banned_at',
            'suspended_at'
        ];

        return !empty(array_intersect(array_keys($changedAttributes), $menuStructureFields));
    }

    /**
     * Check if role is a business role
     *
     * @param string $role
     * @return bool
     */
    private function isBusinessRole(string $role): bool
    {
        return in_array($role, [
            'verified_partner',
            'manufacturer',
            'supplier',
            'brand'
        ]);
    }

    /**
     * Check if role is commonly used (for cache warming)
     *
     * @param string $role
     * @return bool
     */
    private function isCommonRole(string $role): bool
    {
        return in_array($role, [
            'member',
            'senior_member',
            'verified_partner',
            'manufacturer',
            'supplier'
        ]);
    }
}

/**
 * Menu Cache Invalidation Service
 * 
 * Service để handle cache invalidation events từ các sources khác
 */
class MenuCacheInvalidationService
{
    /**
     * Invalidate cache when system settings change
     *
     * @param array $changedSettings
     * @return void
     */
    public static function handleSystemSettingsChange(array $changedSettings): void
    {
        $menuAffectingSettings = [
            'site_name',
            'logo_url',
            'banner_url',
            'maintenance_mode',
            'registration_enabled',
            'marketplace_enabled'
        ];

        if (!empty(array_intersect(array_keys($changedSettings), $menuAffectingSettings))) {
            MenuCacheService::invalidateAllMenuCache();
            
            Log::info("All menu cache invalidated due to system settings change", [
                'changed_settings' => array_keys($changedSettings)
            ]);
        }
    }

    /**
     * Invalidate cache when routes are updated
     *
     * @return void
     */
    public static function handleRoutesUpdate(): void
    {
        // Clear route validation cache
        $routeValidationKeys = ['menu_route_*'];
        foreach ($routeValidationKeys as $pattern) {
            // Implementation would depend on cache driver
            // For Redis: Redis::del(Redis::keys($pattern))
        }

        // Invalidate all menu cache since routes affect menu rendering
        MenuCacheService::invalidateAllMenuCache();

        Log::info("Menu cache invalidated due to routes update");
    }

    /**
     * Invalidate cache when permissions are updated
     *
     * @param string|null $role
     * @return void
     */
    public static function handlePermissionsUpdate(?string $role = null): void
    {
        if ($role) {
            MenuCacheService::invalidateRoleMenuCache($role);
            
            Log::info("Menu cache invalidated for role due to permissions update", [
                'role' => $role
            ]);
        } else {
            MenuCacheService::invalidateAllMenuCache();
            
            Log::info("All menu cache invalidated due to global permissions update");
        }
    }

    /**
     * Invalidate cache when menu configuration changes
     *
     * @return void
     */
    public static function handleMenuConfigurationChange(): void
    {
        MenuCacheService::invalidateAllMenuCache();
        
        // Warm up cache for common roles
        $commonRoles = ['member', 'senior_member', 'verified_partner'];
        foreach ($commonRoles as $role) {
            MenuCacheService::warmUpRoleCache($role);
        }

        Log::info("Menu cache invalidated and warmed up due to configuration change");
    }

    /**
     * Schedule cache invalidation
     *
     * @param string $type
     * @param array $params
     * @param int $delaySeconds
     * @return void
     */
    public static function scheduleInvalidation(string $type, array $params = [], int $delaySeconds = 0): void
    {
        // This could be implemented with Laravel queues for delayed invalidation
        // For now, we'll just log the scheduled invalidation
        
        Log::info("Menu cache invalidation scheduled", [
            'type' => $type,
            'params' => $params,
            'delay_seconds' => $delaySeconds,
            'scheduled_at' => now()->addSeconds($delaySeconds)->toISOString()
        ]);

        // Immediate invalidation for now
        switch ($type) {
            case 'user':
                if (isset($params['user_id'])) {
                    $user = User::find($params['user_id']);
                    if ($user) {
                        MenuCacheService::invalidateUserMenuCache($user);
                    }
                }
                break;
                
            case 'role':
                if (isset($params['role'])) {
                    MenuCacheService::invalidateRoleMenuCache($params['role']);
                }
                break;
                
            case 'all':
                MenuCacheService::invalidateAllMenuCache();
                break;
        }
    }

    /**
     * Bulk invalidate cache for multiple users
     *
     * @param array $userIds
     * @return array
     */
    public static function bulkInvalidateUsers(array $userIds): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if ($user) {
                    MenuCacheService::invalidateUserMenuCache($user);
                    MenuCacheService::invalidateUserPermissionCache($user);
                    $results[$userId] = true;
                } else {
                    $results[$userId] = false;
                }
            } catch (\Exception $e) {
                $results[$userId] = false;
                Log::error("Failed to invalidate cache for user", [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Bulk menu cache invalidation completed", [
            'user_count' => count($userIds),
            'success_count' => count(array_filter($results)),
            'failure_count' => count($userIds) - count(array_filter($results))
        ]);

        return $results;
    }

    /**
     * Get cache invalidation statistics
     *
     * @return array
     */
    public static function getInvalidationStats(): array
    {
        // This would be implemented with proper tracking in production
        return [
            'total_invalidations' => 0,
            'user_invalidations' => 0,
            'role_invalidations' => 0,
            'global_invalidations' => 0,
            'last_invalidation' => null,
            'average_invalidations_per_hour' => 0
        ];
    }
}
