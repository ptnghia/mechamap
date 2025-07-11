<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BusinessVerificationApplication;
use App\Services\UnifiedMarketplacePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Phase 3: Unified Marketplace Permission System Tests
 * 
 * Comprehensive testing for the unified permission system
 * that integrates with business verification from Phase 2
 */
class Phase3UnifiedPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users for different roles
        $this->createTestUsers();
    }

    /** @test */
    public function guest_cannot_buy_or_sell_anything()
    {
        // Guest user (not authenticated)
        $this->assertFalse(UnifiedMarketplacePermissionService::canBuy(null, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell(null, 'digital'));
        $this->assertEmpty(UnifiedMarketplacePermissionService::getAllowedBuyTypes(null));
        $this->assertEmpty(UnifiedMarketplacePermissionService::getAllowedSellTypes(null));
    }

    /** @test */
    public function member_can_buy_digital_but_cannot_sell()
    {
        $member = User::factory()->create(['role' => 'member']);
        
        // Member can buy digital products
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($member, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canBuy($member, 'new_product'));
        
        // Member cannot sell anything
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($member, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($member, 'new_product'));
        
        // Check allowed types
        $this->assertEquals(['digital'], UnifiedMarketplacePermissionService::getAllowedBuyTypes($member));
        $this->assertEmpty(UnifiedMarketplacePermissionService::getAllowedSellTypes($member));
    }

    /** @test */
    public function unverified_business_users_have_limited_permissions()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        // Unverified business users can only buy digital
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($manufacturer, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canBuy($manufacturer, 'new_product'));
        
        // Unverified business users cannot sell
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($manufacturer, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($supplier, 'new_product'));
    }

    /** @test */
    public function verified_business_users_have_full_permissions()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        // Create approved verification applications
        BusinessVerificationApplication::factory()->create([
            'user_id' => $manufacturer->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        BusinessVerificationApplication::factory()->create([
            'user_id' => $supplier->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        // Verified manufacturer permissions
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($manufacturer, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($manufacturer, 'new_product'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($manufacturer, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($manufacturer, 'new_product'));
        
        // Verified supplier permissions
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($supplier, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($supplier, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($supplier, 'new_product'));
    }

    /** @test */
    public function commission_rates_vary_by_verification_status()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        // Unverified users have higher commission rates
        $this->assertEquals(10.0, UnifiedMarketplacePermissionService::getCommissionRate($manufacturer));
        $this->assertEquals(10.0, UnifiedMarketplacePermissionService::getCommissionRate($supplier));
        
        // Create approved verification applications
        BusinessVerificationApplication::factory()->create([
            'user_id' => $manufacturer->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        BusinessVerificationApplication::factory()->create([
            'user_id' => $supplier->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        // Clear cache to get updated rates
        UnifiedMarketplacePermissionService::clearUserPermissionCache($manufacturer);
        UnifiedMarketplacePermissionService::clearUserPermissionCache($supplier);
        
        // Verified users have lower commission rates
        $this->assertEquals(5.0, UnifiedMarketplacePermissionService::getCommissionRate($manufacturer));
        $this->assertEquals(3.0, UnifiedMarketplacePermissionService::getCommissionRate($supplier));
    }

    /** @test */
    public function marketplace_features_reflect_permissions()
    {
        $member = User::factory()->create(['role' => 'member']);
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        // Member features
        $memberFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($member);
        $this->assertFalse($memberFeatures['can_create_products']);
        $this->assertTrue($memberFeatures['can_view_cart']);
        $this->assertTrue($memberFeatures['can_checkout']);
        $this->assertFalse($memberFeatures['can_manage_seller_account']);
        
        // Unverified manufacturer features
        $manufacturerFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($manufacturer);
        $this->assertFalse($manufacturerFeatures['can_create_products']); // Cannot sell until verified
        $this->assertTrue($manufacturerFeatures['can_view_cart']);
        $this->assertTrue($manufacturerFeatures['can_manage_business_profile']);
        
        // Verify manufacturer
        BusinessVerificationApplication::factory()->create([
            'user_id' => $manufacturer->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        UnifiedMarketplacePermissionService::clearUserPermissionCache($manufacturer);
        
        // Verified manufacturer features
        $verifiedFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($manufacturer);
        $this->assertTrue($verifiedFeatures['can_create_products']);
        $this->assertTrue($verifiedFeatures['can_manage_seller_account']);
    }

    /** @test */
    public function product_creation_validation_works_correctly()
    {
        $member = User::factory()->create(['role' => 'member']);
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        // Member cannot create products
        $errors = UnifiedMarketplacePermissionService::validateProductCreation($member, [
            'product_type' => 'digital'
        ]);
        $this->assertNotEmpty($errors);
        
        // Unverified manufacturer cannot create products
        $errors = UnifiedMarketplacePermissionService::validateProductCreation($manufacturer, [
            'product_type' => 'digital'
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('verification', $errors[0]);
        
        // Verify manufacturer
        BusinessVerificationApplication::factory()->create([
            'user_id' => $manufacturer->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        // Verified manufacturer can create products
        $errors = UnifiedMarketplacePermissionService::validateProductCreation($manufacturer, [
            'product_type' => 'digital'
        ]);
        $this->assertEmpty($errors);
    }

    /** @test */
    public function permission_caching_works_correctly()
    {
        $user = User::factory()->create(['role' => 'manufacturer']);
        
        // First call should cache permissions
        $permissions1 = UnifiedMarketplacePermissionService::getUserPermissions($user);
        
        // Second call should use cache
        $permissions2 = UnifiedMarketplacePermissionService::getUserPermissions($user);
        
        $this->assertEquals($permissions1, $permissions2);
        
        // Clear cache
        UnifiedMarketplacePermissionService::clearUserPermissionCache($user);
        
        // Should work after cache clear
        $permissions3 = UnifiedMarketplacePermissionService::getUserPermissions($user);
        $this->assertEquals($permissions1, $permissions3);
    }

    /** @test */
    public function admin_roles_have_full_permissions()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $moderator = User::factory()->create(['role' => 'marketplace_moderator']);
        
        // Admin can buy and sell everything
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($admin, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($admin, 'new_product'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($admin, 'used_product'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($admin, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($admin, 'new_product'));
        
        // Moderator has appropriate permissions
        $this->assertTrue(UnifiedMarketplacePermissionService::canBuy($moderator, 'digital'));
        $this->assertTrue(UnifiedMarketplacePermissionService::canSell($moderator, 'digital'));
        
        // Admin commission rate is 0
        $this->assertEquals(0.0, UnifiedMarketplacePermissionService::getCommissionRate($admin));
    }

    /** @test */
    public function brand_role_has_correct_permissions()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        // Brand cannot buy or sell (even when verified)
        $this->assertFalse(UnifiedMarketplacePermissionService::canBuy($brand, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($brand, 'digital'));
        
        // Verify brand
        BusinessVerificationApplication::factory()->create([
            'user_id' => $brand->id,
            'status' => BusinessVerificationApplication::STATUS_APPROVED,
        ]);
        
        // Still cannot buy or sell (brands are for promotion only)
        $this->assertFalse(UnifiedMarketplacePermissionService::canBuy($brand, 'digital'));
        $this->assertFalse(UnifiedMarketplacePermissionService::canSell($brand, 'digital'));
        
        // Commission rate is 0 for brands
        $this->assertEquals(0.0, UnifiedMarketplacePermissionService::getCommissionRate($brand));
    }

    /** @test */
    public function permission_summary_provides_complete_information()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        $summary = UnifiedMarketplacePermissionService::getPermissionSummary($manufacturer);
        
        $this->assertArrayHasKey('user_id', $summary);
        $this->assertArrayHasKey('role', $summary);
        $this->assertArrayHasKey('effective_role', $summary);
        $this->assertArrayHasKey('is_verified', $summary);
        $this->assertArrayHasKey('permissions', $summary);
        $this->assertArrayHasKey('features', $summary);
        $this->assertArrayHasKey('commission_rate', $summary);
        $this->assertArrayHasKey('can_access_marketplace', $summary);
        
        $this->assertEquals($manufacturer->id, $summary['user_id']);
        $this->assertEquals('manufacturer', $summary['role']);
        $this->assertEquals('manufacturer_unverified', $summary['effective_role']);
        $this->assertFalse($summary['is_verified']);
    }

    private function createTestUsers(): void
    {
        // Create users for different roles
        User::factory()->create(['role' => 'guest', 'email' => 'guest@test.com']);
        User::factory()->create(['role' => 'member', 'email' => 'member@test.com']);
        User::factory()->create(['role' => 'senior_member', 'email' => 'senior@test.com']);
        User::factory()->create(['role' => 'manufacturer', 'email' => 'manufacturer@test.com']);
        User::factory()->create(['role' => 'supplier', 'email' => 'supplier@test.com']);
        User::factory()->create(['role' => 'brand', 'email' => 'brand@test.com']);
        User::factory()->create(['role' => 'verified_partner', 'email' => 'partner@test.com']);
        User::factory()->create(['role' => 'super_admin', 'email' => 'admin@test.com']);
    }
}
