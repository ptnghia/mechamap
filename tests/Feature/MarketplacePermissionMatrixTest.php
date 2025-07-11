<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Services\MarketplacePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MarketplacePermissionMatrixTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guest_can_buy_and_sell_digital_products_only()
    {
        // Test Guest permissions
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('guest');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('guest');

        $this->assertEquals(['digital'], $allowedBuyTypes);
        $this->assertEquals(['digital'], $allowedSellTypes);

        // Test specific permissions
        $this->assertTrue(MarketplacePermissionService::canBuy('guest', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canBuy('guest', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy('guest', 'used_product'));

        $this->assertTrue(MarketplacePermissionService::canSell('guest', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell('guest', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell('guest', 'used_product'));
    }

    /** @test */
    public function member_has_no_marketplace_permissions()
    {
        // Test Member permissions - should be empty
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('member');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('member');

        $this->assertEquals([], $allowedBuyTypes);
        $this->assertEquals([], $allowedSellTypes);

        // Test specific permissions - all should be false
        $this->assertFalse(MarketplacePermissionService::canBuy('member', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canBuy('member', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy('member', 'used_product'));

        $this->assertFalse(MarketplacePermissionService::canSell('member', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell('member', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell('member', 'used_product'));
    }

    /** @test */
    public function senior_member_has_no_marketplace_permissions()
    {
        // Test Senior Member permissions - should be empty like member
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('senior_member');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('senior_member');

        $this->assertEquals([], $allowedBuyTypes);
        $this->assertEquals([], $allowedSellTypes);

        // Test specific permissions - all should be false
        $this->assertFalse(MarketplacePermissionService::canBuy('senior_member', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell('senior_member', 'digital'));
    }

    /** @test */
    public function supplier_can_sell_digital_and_new_products()
    {
        // Test Supplier permissions
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('supplier');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('supplier');

        $this->assertEquals(['digital'], $allowedBuyTypes);
        $this->assertEquals(['digital', 'new_product'], $allowedSellTypes);

        // Test specific permissions
        $this->assertTrue(MarketplacePermissionService::canBuy('supplier', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canBuy('supplier', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy('supplier', 'used_product'));

        $this->assertTrue(MarketplacePermissionService::canSell('supplier', 'digital'));
        $this->assertTrue(MarketplacePermissionService::canSell('supplier', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell('supplier', 'used_product'));
    }

    /** @test */
    public function manufacturer_can_buy_and_sell_digital_and_new_products()
    {
        // Test Manufacturer permissions
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('manufacturer');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('manufacturer');

        $this->assertEquals(['digital', 'new_product'], $allowedBuyTypes);
        $this->assertEquals(['digital', 'new_product'], $allowedSellTypes);

        // Test specific permissions
        $this->assertTrue(MarketplacePermissionService::canBuy('manufacturer', 'digital'));
        $this->assertTrue(MarketplacePermissionService::canBuy('manufacturer', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy('manufacturer', 'used_product'));

        $this->assertTrue(MarketplacePermissionService::canSell('manufacturer', 'digital'));
        $this->assertTrue(MarketplacePermissionService::canSell('manufacturer', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell('manufacturer', 'used_product'));
    }

    /** @test */
    public function brand_has_no_marketplace_permissions()
    {
        // Test Brand permissions - should be empty (read-only)
        $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes('brand');
        $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes('brand');

        $this->assertEquals([], $allowedBuyTypes);
        $this->assertEquals([], $allowedSellTypes);

        // Test specific permissions - all should be false
        $this->assertFalse(MarketplacePermissionService::canBuy('brand', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canBuy('brand', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy('brand', 'used_product'));

        $this->assertFalse(MarketplacePermissionService::canSell('brand', 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell('brand', 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell('brand', 'used_product'));
    }

    /** @test */
    public function admin_roles_have_full_permissions()
    {
        $adminRoles = ['super_admin', 'system_admin', 'admin', 'content_admin', 'marketplace_moderator', 'moderator'];

        foreach ($adminRoles as $role) {
            $allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes($role);
            $allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes($role);

            $this->assertEquals(['digital', 'new_product', 'used_product'], $allowedBuyTypes, "Admin role {$role} should have full buy permissions");
            $this->assertEquals(['digital', 'new_product', 'used_product'], $allowedSellTypes, "Admin role {$role} should have full sell permissions");

            // Test specific permissions
            $this->assertTrue(MarketplacePermissionService::canBuy($role, 'digital'));
            $this->assertTrue(MarketplacePermissionService::canBuy($role, 'new_product'));
            $this->assertTrue(MarketplacePermissionService::canBuy($role, 'used_product'));

            $this->assertTrue(MarketplacePermissionService::canSell($role, 'digital'));
            $this->assertTrue(MarketplacePermissionService::canSell($role, 'new_product'));
            $this->assertTrue(MarketplacePermissionService::canSell($role, 'used_product'));
        }
    }

    /** @test */
    public function validate_product_creation_enforces_permissions()
    {
        // Test Guest creating digital product (allowed)
        $guestUser = (object) ['role' => 'guest'];
        $digitalProductData = ['product_type' => 'digital'];
        
        $errors = MarketplacePermissionService::validateProductCreation($guestUser, $digitalProductData);
        $this->assertEmpty($errors);

        // Test Guest creating new_product (not allowed)
        $newProductData = ['product_type' => 'new_product'];
        $errors = MarketplacePermissionService::validateProductCreation($guestUser, $newProductData);
        $this->assertNotEmpty($errors);

        // Test Member creating any product (not allowed)
        $memberUser = (object) ['role' => 'member'];
        $errors = MarketplacePermissionService::validateProductCreation($memberUser, $digitalProductData);
        $this->assertNotEmpty($errors);

        // Test Supplier creating new_product (allowed)
        $supplierUser = (object) ['role' => 'supplier'];
        $errors = MarketplacePermissionService::validateProductCreation($supplierUser, $newProductData);
        $this->assertEmpty($errors);

        // Test Supplier creating used_product (not allowed)
        $usedProductData = ['product_type' => 'used_product'];
        $errors = MarketplacePermissionService::validateProductCreation($supplierUser, $usedProductData);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function permission_matrix_is_comprehensive()
    {
        // Test that all expected roles are defined in the matrix
        $expectedRoles = [
            'guest', 'member', 'senior_member', 'supplier', 'manufacturer', 'brand',
            'super_admin', 'system_admin', 'admin', 'content_admin', 'marketplace_moderator', 'moderator'
        ];

        foreach ($expectedRoles as $role) {
            $buyTypes = MarketplacePermissionService::getAllowedBuyTypes($role);
            $sellTypes = MarketplacePermissionService::getAllowedSellTypes($role);

            // Should return an array (even if empty)
            $this->assertIsArray($buyTypes, "Role {$role} should have buy permissions array");
            $this->assertIsArray($sellTypes, "Role {$role} should have sell permissions array");
        }
    }

    /** @test */
    public function unknown_role_has_no_permissions()
    {
        $unknownRole = 'unknown_role';
        
        $buyTypes = MarketplacePermissionService::getAllowedBuyTypes($unknownRole);
        $sellTypes = MarketplacePermissionService::getAllowedSellTypes($unknownRole);

        $this->assertEquals([], $buyTypes);
        $this->assertEquals([], $sellTypes);

        $this->assertFalse(MarketplacePermissionService::canBuy($unknownRole, 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell($unknownRole, 'digital'));
    }

    /** @test */
    public function permission_matrix_summary_is_correct()
    {
        // This test documents the expected permission matrix
        $expectedMatrix = [
            'guest' => ['buy' => ['digital'], 'sell' => ['digital']],
            'member' => ['buy' => [], 'sell' => []],
            'senior_member' => ['buy' => [], 'sell' => []],
            'supplier' => ['buy' => ['digital'], 'sell' => ['digital', 'new_product']],
            'manufacturer' => ['buy' => ['digital', 'new_product'], 'sell' => ['digital', 'new_product']],
            'brand' => ['buy' => [], 'sell' => []],
        ];

        foreach ($expectedMatrix as $role => $permissions) {
            $actualBuy = MarketplacePermissionService::getAllowedBuyTypes($role);
            $actualSell = MarketplacePermissionService::getAllowedSellTypes($role);

            $this->assertEquals($permissions['buy'], $actualBuy, "Buy permissions for {$role} don't match");
            $this->assertEquals($permissions['sell'], $actualSell, "Sell permissions for {$role} don't match");
        }
    }
}
