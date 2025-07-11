<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BusinessVerificationApplication;
use App\Services\UnifiedMarketplacePermissionService;

/**
 * Verify Phase 3: Unified Marketplace Permission System Implementation
 * 
 * This command verifies that all Phase 3 fixes are working correctly
 */
class VerifyPhase3Implementation extends Command
{
    protected $signature = 'mechamap:verify-phase3';
    protected $description = 'Verify Phase 3 unified marketplace permission system implementation';

    public function handle()
    {
        $this->info('🔍 VERIFYING PHASE 3: UNIFIED MARKETPLACE PERMISSION SYSTEM');
        $this->info('================================================================');
        
        $allPassed = true;
        
        // Test 1: Guest Permissions
        $allPassed &= $this->testGuestPermissions();
        
        // Test 2: Member Permissions
        $allPassed &= $this->testMemberPermissions();
        
        // Test 3: Business Role Permissions
        $allPassed &= $this->testBusinessRolePermissions();
        
        // Test 4: Commission Rates
        $allPassed &= $this->testCommissionRates();
        
        // Test 5: Permission Caching
        $allPassed &= $this->testPermissionCaching();
        
        // Test 6: Marketplace Features
        $allPassed &= $this->testMarketplaceFeatures();
        
        $this->info('================================================================');
        
        if ($allPassed) {
            $this->info('✅ ALL TESTS PASSED! Phase 3 implementation is working correctly.');
            return 0;
        } else {
            $this->error('❌ SOME TESTS FAILED! Please check the implementation.');
            return 1;
        }
    }

    private function testGuestPermissions(): bool
    {
        $this->info('🧪 Testing Guest Permissions...');
        
        try {
            // Guest (null user) should not be able to buy or sell anything
            $canBuyDigital = UnifiedMarketplacePermissionService::canBuy(null, 'digital');
            $canSellDigital = UnifiedMarketplacePermissionService::canSell(null, 'digital');
            $allowedBuyTypes = UnifiedMarketplacePermissionService::getAllowedBuyTypes(null);
            $allowedSellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes(null);
            
            if ($canBuyDigital || $canSellDigital || !empty($allowedBuyTypes) || !empty($allowedSellTypes)) {
                $this->error('   ❌ FAILED: Guest has permissions they should not have');
                return false;
            }
            
            $this->info('   ✅ PASSED: Guest permissions are correctly restricted');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testMemberPermissions(): bool
    {
        $this->info('🧪 Testing Member Permissions...');
        
        try {
            // Create a test member
            $member = new User(['role' => 'member']);
            
            $canBuyDigital = UnifiedMarketplacePermissionService::canBuy($member, 'digital');
            $canBuyNew = UnifiedMarketplacePermissionService::canBuy($member, 'new_product');
            $canSellDigital = UnifiedMarketplacePermissionService::canSell($member, 'digital');
            $allowedBuyTypes = UnifiedMarketplacePermissionService::getAllowedBuyTypes($member);
            $allowedSellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($member);
            
            if (!$canBuyDigital || $canBuyNew || $canSellDigital || !in_array('digital', $allowedBuyTypes) || !empty($allowedSellTypes)) {
                $this->error('   ❌ FAILED: Member permissions are incorrect');
                $this->error('      canBuyDigital: ' . ($canBuyDigital ? 'true' : 'false'));
                $this->error('      canBuyNew: ' . ($canBuyNew ? 'true' : 'false'));
                $this->error('      canSellDigital: ' . ($canSellDigital ? 'true' : 'false'));
                $this->error('      allowedBuyTypes: ' . implode(', ', $allowedBuyTypes));
                $this->error('      allowedSellTypes: ' . implode(', ', $allowedSellTypes));
                return false;
            }
            
            $this->info('   ✅ PASSED: Member can buy digital only, cannot sell');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testBusinessRolePermissions(): bool
    {
        $this->info('🧪 Testing Business Role Permissions...');
        
        try {
            // Test unverified manufacturer
            $manufacturer = new User(['role' => 'manufacturer']);
            
            $canBuyDigital = UnifiedMarketplacePermissionService::canBuy($manufacturer, 'digital');
            $canSellDigital = UnifiedMarketplacePermissionService::canSell($manufacturer, 'digital');
            $allowedSellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($manufacturer);
            
            if (!$canBuyDigital || $canSellDigital || !empty($allowedSellTypes)) {
                $this->error('   ❌ FAILED: Unverified manufacturer should only buy digital, not sell');
                return false;
            }
            
            $this->info('   ✅ PASSED: Unverified business roles have limited permissions');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testCommissionRates(): bool
    {
        $this->info('🧪 Testing Commission Rates...');
        
        try {
            // Test different roles
            $member = new User(['role' => 'member']);
            $manufacturer = new User(['role' => 'manufacturer']);
            $supplier = new User(['role' => 'supplier']);
            $admin = new User(['role' => 'super_admin']);
            
            $memberRate = UnifiedMarketplacePermissionService::getCommissionRate($member);
            $manufacturerRate = UnifiedMarketplacePermissionService::getCommissionRate($manufacturer);
            $supplierRate = UnifiedMarketplacePermissionService::getCommissionRate($supplier);
            $adminRate = UnifiedMarketplacePermissionService::getCommissionRate($admin);
            
            if ($memberRate !== 0.0 || $manufacturerRate !== 10.0 || $supplierRate !== 10.0 || $adminRate !== 0.0) {
                $this->error('   ❌ FAILED: Commission rates are incorrect');
                $this->error('      Member: ' . $memberRate . '% (expected 0%)');
                $this->error('      Manufacturer: ' . $manufacturerRate . '% (expected 10%)');
                $this->error('      Supplier: ' . $supplierRate . '% (expected 10%)');
                $this->error('      Admin: ' . $adminRate . '% (expected 0%)');
                return false;
            }
            
            $this->info('   ✅ PASSED: Commission rates are correct');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testPermissionCaching(): bool
    {
        $this->info('🧪 Testing Permission Caching...');
        
        try {
            $user = new User(['id' => 999, 'role' => 'member']);
            
            // First call should work
            $permissions1 = UnifiedMarketplacePermissionService::getUserPermissions($user);
            
            // Second call should also work (from cache)
            $permissions2 = UnifiedMarketplacePermissionService::getUserPermissions($user);
            
            if ($permissions1 !== $permissions2) {
                $this->error('   ❌ FAILED: Permission caching is not consistent');
                return false;
            }
            
            // Clear cache should work
            UnifiedMarketplacePermissionService::clearUserPermissionCache($user);
            
            // Should still work after cache clear
            $permissions3 = UnifiedMarketplacePermissionService::getUserPermissions($user);
            
            if ($permissions1 !== $permissions3) {
                $this->error('   ❌ FAILED: Permissions changed after cache clear');
                return false;
            }
            
            $this->info('   ✅ PASSED: Permission caching works correctly');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testMarketplaceFeatures(): bool
    {
        $this->info('🧪 Testing Marketplace Features...');
        
        try {
            $member = new User(['role' => 'member']);
            $manufacturer = new User(['role' => 'manufacturer']);
            
            $memberFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($member);
            $manufacturerFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($manufacturer);
            
            // Member should be able to view cart and checkout but not create products
            if (!$memberFeatures['can_view_cart'] || !$memberFeatures['can_checkout'] || $memberFeatures['can_create_products']) {
                $this->error('   ❌ FAILED: Member marketplace features are incorrect');
                return false;
            }
            
            // Unverified manufacturer should not be able to create products
            if ($manufacturerFeatures['can_create_products']) {
                $this->error('   ❌ FAILED: Unverified manufacturer should not be able to create products');
                return false;
            }
            
            $this->info('   ✅ PASSED: Marketplace features are correct');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }
}
