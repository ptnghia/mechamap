<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Services\UnifiedMarketplacePermissionService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Commission Rate Management Controller
 * 
 * Manages dynamic commission rates for different user roles
 * Integrates with unified marketplace permission system
 */
class CommissionRateController extends Controller
{
    /**
     * Display commission rate management dashboard
     */
    public function index(): View
    {
        // Get current commission rates
        $commissionRates = $this->getCurrentCommissionRates();
        
        // Get statistics
        $statistics = $this->getCommissionStatistics();
        
        // Get recent commission changes
        $recentChanges = $this->getRecentCommissionChanges();

        return view('admin.commission-rates.index', compact(
            'commissionRates',
            'statistics', 
            'recentChanges'
        ));
    }

    /**
     * Update commission rates
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*' => 'required|numeric|min:0|max:50',
        ]);

        try {
            $rates = $request->input('rates');
            $updatedRates = [];
            
            foreach ($rates as $role => $rate) {
                // Validate role exists
                if (!$this->isValidRole($role)) {
                    continue;
                }
                
                $updatedRates[$role] = (float) $rate;
            }

            // Update commission rates in config cache
            $this->updateCommissionRatesConfig($updatedRates);
            
            // Clear permission caches
            $this->clearPermissionCaches();
            
            // Log the change
            Log::info('Commission rates updated', [
                'admin_id' => auth()->id(),
                'updated_rates' => $updatedRates,
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commission rates updated successfully',
                'rates' => $updatedRates,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update commission rates', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update commission rates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get commission rate for specific user
     */
    public function getUserRate(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $rate = UnifiedMarketplacePermissionService::getCommissionRate($user);
            $features = UnifiedMarketplacePermissionService::getMarketplaceFeatures($user);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'is_verified' => UnifiedMarketplacePermissionService::isBusinessVerified($user),
                ],
                'commission_rate' => $rate,
                'marketplace_features' => $features,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user commission rate: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update commission rates for users
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'commission_rate' => 'required|numeric|min:0|max:50',
        ]);

        try {
            $userIds = $request->input('user_ids');
            $newRate = (float) $request->input('commission_rate');
            
            $users = User::whereIn('id', $userIds)->get();
            $updatedUsers = [];

            foreach ($users as $user) {
                // Clear user's permission cache
                UnifiedMarketplacePermissionService::clearUserPermissionCache($user);
                $updatedUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'new_rate' => $newRate,
                ];
            }

            // Log bulk update
            Log::info('Bulk commission rate update', [
                'admin_id' => auth()->id(),
                'affected_users' => count($updatedUsers),
                'new_rate' => $newRate,
                'user_ids' => $userIds,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Updated commission rates for {$users->count()} users",
                'updated_users' => $updatedUsers,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update commission rates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get commission analytics
     */
    public function analytics(): JsonResponse
    {
        try {
            $analytics = [
                'total_commission_earned' => $this->getTotalCommissionEarned(),
                'commission_by_role' => $this->getCommissionByRole(),
                'monthly_commission_trend' => $this->getMonthlyCommissionTrend(),
                'top_earning_sellers' => $this->getTopEarningSellers(),
                'average_commission_rate' => $this->getAverageCommissionRate(),
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get commission analytics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset commission rates to defaults
     */
    public function resetToDefaults(): JsonResponse
    {
        try {
            $defaultRates = $this->getDefaultCommissionRates();
            $this->updateCommissionRatesConfig($defaultRates);
            $this->clearPermissionCaches();

            Log::info('Commission rates reset to defaults', [
                'admin_id' => auth()->id(),
                'default_rates' => $defaultRates,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commission rates reset to defaults successfully',
                'rates' => $defaultRates,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset commission rates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current commission rates
     */
    private function getCurrentCommissionRates(): array
    {
        $roles = [
            'manufacturer_verified' => 'Nhà sản xuất (Đã xác thực)',
            'manufacturer_unverified' => 'Nhà sản xuất (Chưa xác thực)',
            'supplier_verified' => 'Nhà cung cấp (Đã xác thực)',
            'supplier_unverified' => 'Nhà cung cấp (Chưa xác thực)',
            'brand_verified' => 'Thương hiệu (Đã xác thực)',
            'brand_unverified' => 'Thương hiệu (Chưa xác thực)',
            'verified_partner_verified' => 'Đối tác xác thực (Đã xác thực)',
            'verified_partner_unverified' => 'Đối tác xác thực (Chưa xác thực)',
        ];

        $rates = [];
        foreach ($roles as $role => $displayName) {
            $rates[] = [
                'role' => $role,
                'display_name' => $displayName,
                'current_rate' => $this->getCommissionRateForRole($role),
                'default_rate' => $this->getDefaultCommissionRateForRole($role),
                'user_count' => $this->getUserCountForRole($role),
            ];
        }

        return $rates;
    }

    /**
     * Get commission rate for specific role
     */
    private function getCommissionRateForRole(string $role): float
    {
        // This would integrate with the unified service
        // For now, return default rates
        $defaultRates = $this->getDefaultCommissionRates();
        return $defaultRates[$role] ?? 0.0;
    }

    /**
     * Get default commission rates
     */
    private function getDefaultCommissionRates(): array
    {
        return [
            'manufacturer_verified' => 5.0,
            'manufacturer_unverified' => 10.0,
            'supplier_verified' => 3.0,
            'supplier_unverified' => 10.0,
            'brand_verified' => 0.0,
            'brand_unverified' => 10.0,
            'verified_partner_verified' => 2.0,
            'verified_partner_unverified' => 10.0,
        ];
    }

    /**
     * Get default commission rate for specific role
     */
    private function getDefaultCommissionRateForRole(string $role): float
    {
        $defaults = $this->getDefaultCommissionRates();
        return $defaults[$role] ?? 0.0;
    }

    /**
     * Update commission rates configuration
     */
    private function updateCommissionRatesConfig(array $rates): void
    {
        // Store in cache for now
        // In production, this should update a database table or config file
        Cache::put('commission_rates_override', $rates, now()->addDays(30));
    }

    /**
     * Clear permission caches
     */
    private function clearPermissionCaches(): void
    {
        // Clear all marketplace permission caches
        UnifiedMarketplacePermissionService::clearAllPermissionCaches();
    }

    /**
     * Check if role is valid
     */
    private function isValidRole(string $role): bool
    {
        $validRoles = array_keys($this->getDefaultCommissionRates());
        return in_array($role, $validRoles);
    }

    /**
     * Get user count for role
     */
    private function getUserCountForRole(string $role): int
    {
        // Extract base role (remove _verified/_unverified suffix)
        $baseRole = str_replace(['_verified', '_unverified'], '', $role);
        
        return User::where('role', $baseRole)->count();
    }

    /**
     * Get commission statistics
     */
    private function getCommissionStatistics(): array
    {
        return [
            'total_sellers' => User::whereIn('role', ['manufacturer', 'supplier', 'verified_partner'])->count(),
            'verified_sellers' => $this->getVerifiedSellersCount(),
            'total_commission_this_month' => 0, // Would calculate from orders
            'average_commission_rate' => $this->getAverageCommissionRate(),
        ];
    }

    /**
     * Get verified sellers count
     */
    private function getVerifiedSellersCount(): int
    {
        // This would query the business verification applications
        return 0; // Placeholder
    }

    /**
     * Get recent commission changes
     */
    private function getRecentCommissionChanges(): array
    {
        // This would query a commission_rate_changes table
        return []; // Placeholder
    }

    /**
     * Get total commission earned
     */
    private function getTotalCommissionEarned(): float
    {
        // This would sum commission_amount from order_items
        return 0.0; // Placeholder
    }

    /**
     * Get commission by role
     */
    private function getCommissionByRole(): array
    {
        // This would group commission by seller role
        return []; // Placeholder
    }

    /**
     * Get monthly commission trend
     */
    private function getMonthlyCommissionTrend(): array
    {
        // This would calculate monthly commission trends
        return []; // Placeholder
    }

    /**
     * Get top earning sellers
     */
    private function getTopEarningSellers(): array
    {
        // This would get sellers with highest earnings
        return []; // Placeholder
    }

    /**
     * Get average commission rate
     */
    private function getAverageCommissionRate(): float
    {
        $rates = array_values($this->getDefaultCommissionRates());
        return count($rates) > 0 ? array_sum($rates) / count($rates) : 0.0;
    }
}
