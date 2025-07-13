<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\MenuService;
use Mockery;

class MenuServiceUnitTest extends TestCase
{
    /** @test */
    public function it_returns_guest_menu_for_null_user()
    {
        $component = MenuService::getMenuComponent(null);
        
        $this->assertEquals('menu.guest-menu', $component);
    }

    /** @test */
    public function it_returns_admin_menu_for_admin_roles()
    {
        $adminRoles = ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'];
        
        foreach ($adminRoles as $role) {
            $user = Mockery::mock(User::class);
            $user->role = $role;
            
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.admin-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_business_menu_for_business_roles()
    {
        $businessRoles = ['verified_partner', 'manufacturer', 'supplier', 'brand'];
        
        foreach ($businessRoles as $role) {
            $user = Mockery::mock(User::class);
            $user->role = $role;
            
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.business-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_member_menu_for_member_roles()
    {
        $memberRoles = ['senior_member', 'member', 'guest'];
        
        foreach ($memberRoles as $role) {
            $user = Mockery::mock(User::class);
            $user->role = $role;
            
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.member-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_guest_menu_for_unknown_role()
    {
        $user = Mockery::mock(User::class);
        $user->role = 'unknown_role';
        
        $component = MenuService::getMenuComponent($user);
        
        $this->assertEquals('menu.guest-menu', $component);
    }

    /** @test */
    public function it_returns_guest_configuration_for_null_user()
    {
        $config = MenuService::getMenuConfiguration(null);

        $this->assertIsArray($config);
        $this->assertEquals('menu.guest-menu', $config['component']);
        $this->assertNull($config['user_info']);
        $this->assertEmpty($config['permissions']);
        $this->assertTrue($config['features']['can_register']);
        $this->assertTrue($config['features']['can_login']);
        $this->assertTrue($config['features']['show_guest_notice']);
    }

    /** @test */
    public function it_validates_route_access_correctly()
    {
        // Test public route access without user
        $canAccess = MenuService::canAccessMenuItem(null, 'home', null);
        $this->assertTrue($canAccess);

        // Test route that doesn't exist
        $canAccess = MenuService::canAccessMenuItem(null, 'non.existent.route', null);
        $this->assertFalse($canAccess);

        // Test with user but no permission required
        $user = Mockery::mock(User::class);
        $canAccess = MenuService::canAccessMenuItem($user, 'home', null);
        $this->assertTrue($canAccess);

        // Test with permission required but user has permission
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasPermission')->with('test-permission')->andReturn(true);
        $canAccess = MenuService::canAccessMenuItem($user, 'home', 'test-permission');
        $this->assertTrue($canAccess);

        // Test with permission required but user doesn't have permission
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasPermission')->with('test-permission')->andReturn(false);
        $canAccess = MenuService::canAccessMenuItem($user, 'home', 'test-permission');
        $this->assertFalse($canAccess);
    }

    /** @test */
    public function it_handles_menu_configuration_structure()
    {
        $user = Mockery::mock(User::class);
        $user->role = 'member';
        $user->id = 1;
        $user->name = 'Test User';
        $user->role_display_name = 'Member';
        $user->role_color = 'primary';
        $user->avatar_url = '/images/avatar.jpg';
        $user->business_verified = false;
        
        $user->shouldReceive('canAccessAdmin')->andReturn(false);

        $config = MenuService::getMenuConfiguration($user);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('component', $config);
        $this->assertArrayHasKey('user_info', $config);
        $this->assertArrayHasKey('permissions', $config);
        $this->assertArrayHasKey('menu_items', $config);
        $this->assertArrayHasKey('features', $config);

        // Check user info structure
        $userInfo = $config['user_info'];
        $this->assertEquals('Test User', $userInfo['name']);
        $this->assertEquals('member', $userInfo['role']);
        $this->assertEquals('Member', $userInfo['role_display_name']);
        $this->assertEquals('primary', $userInfo['role_color']);
        $this->assertEquals('/images/avatar.jpg', $userInfo['avatar_url']);
        $this->assertFalse($userInfo['is_verified']);

        // Check permissions structure
        $permissions = $config['permissions'];
        $this->assertArrayHasKey('can_access_admin', $permissions);
        $this->assertArrayHasKey('can_create_content', $permissions);
        $this->assertArrayHasKey('can_buy_products', $permissions);
        $this->assertArrayHasKey('can_sell_products', $permissions);
        $this->assertArrayHasKey('can_view_cart', $permissions);

        // Check features structure
        $features = $config['features'];
        $this->assertArrayHasKey('show_admin_status_bar', $features);
        $this->assertArrayHasKey('show_business_status_bar', $features);
        $this->assertArrayHasKey('show_member_status_bar', $features);
        $this->assertArrayHasKey('show_shopping_cart', $features);
        $this->assertArrayHasKey('show_create_dropdown', $features);
        $this->assertArrayHasKey('show_notifications', $features);
        $this->assertArrayHasKey('enable_search', $features);
        $this->assertArrayHasKey('show_language_switcher', $features);
    }

    /** @test */
    public function it_sets_correct_permissions_for_admin_user()
    {
        $user = Mockery::mock(User::class);
        $user->role = 'super_admin';
        $user->id = 1;
        $user->name = 'Admin User';
        $user->role_display_name = 'Super Admin';
        $user->role_color = 'danger';
        $user->avatar_url = '/images/admin-avatar.jpg';
        $user->business_verified = false;
        
        $user->shouldReceive('canAccessAdmin')->andReturn(true);

        $config = MenuService::getMenuConfiguration($user);
        $permissions = $config['permissions'];

        $this->assertTrue($permissions['can_access_admin']);
        $this->assertTrue($permissions['can_create_content']);
        $this->assertFalse($permissions['can_buy_products']); // Admin không phải business role
        $this->assertFalse($permissions['can_sell_products']);
        $this->assertFalse($permissions['can_view_cart']);
    }

    /** @test */
    public function it_sets_correct_permissions_for_business_user()
    {
        $user = Mockery::mock(User::class);
        $user->role = 'manufacturer';
        $user->id = 1;
        $user->name = 'Business User';
        $user->role_display_name = 'Manufacturer';
        $user->role_color = 'success';
        $user->avatar_url = '/images/business-avatar.jpg';
        $user->business_verified = true;
        
        $user->shouldReceive('canAccessAdmin')->andReturn(false);

        $config = MenuService::getMenuConfiguration($user);
        $permissions = $config['permissions'];

        $this->assertFalse($permissions['can_access_admin']);
        $this->assertTrue($permissions['can_create_content']);
        $this->assertTrue($permissions['can_buy_products']);
        $this->assertTrue($permissions['can_sell_products']);
        $this->assertTrue($permissions['can_view_cart']); // Verified business user
    }

    /** @test */
    public function it_sets_correct_permissions_for_guest_user()
    {
        $user = Mockery::mock(User::class);
        $user->role = 'guest';
        $user->id = 1;
        $user->name = 'Guest User';
        $user->role_display_name = 'Guest';
        $user->role_color = 'secondary';
        $user->avatar_url = '/images/guest-avatar.jpg';
        $user->business_verified = false;
        
        $user->shouldReceive('canAccessAdmin')->andReturn(false);

        $config = MenuService::getMenuConfiguration($user);
        $permissions = $config['permissions'];

        $this->assertFalse($permissions['can_access_admin']);
        $this->assertFalse($permissions['can_create_content']); // Guest không thể tạo content
        $this->assertFalse($permissions['can_buy_products']);
        $this->assertFalse($permissions['can_sell_products']);
        $this->assertFalse($permissions['can_view_cart']);
    }

    /** @test */
    public function it_handles_business_verification_status()
    {
        // Unverified business user
        $unverifiedUser = Mockery::mock(User::class);
        $unverifiedUser->role = 'supplier';
        $unverifiedUser->id = 1;
        $unverifiedUser->name = 'Unverified User';
        $unverifiedUser->role_display_name = 'Supplier';
        $unverifiedUser->role_color = 'warning';
        $unverifiedUser->avatar_url = '/images/supplier-avatar.jpg';
        $unverifiedUser->business_verified = false;
        
        $unverifiedUser->shouldReceive('canAccessAdmin')->andReturn(false);

        $config = MenuService::getMenuConfiguration($unverifiedUser);
        $this->assertFalse($config['permissions']['can_view_cart']);
        $this->assertFalse($config['user_info']['is_verified']);

        // Verified business user
        $verifiedUser = Mockery::mock(User::class);
        $verifiedUser->role = 'supplier';
        $verifiedUser->id = 2;
        $verifiedUser->name = 'Verified User';
        $verifiedUser->role_display_name = 'Supplier';
        $verifiedUser->role_color = 'success';
        $verifiedUser->avatar_url = '/images/verified-supplier-avatar.jpg';
        $verifiedUser->business_verified = true;
        
        $verifiedUser->shouldReceive('canAccessAdmin')->andReturn(false);

        $config = MenuService::getMenuConfiguration($verifiedUser);
        $this->assertTrue($config['permissions']['can_view_cart']);
        $this->assertTrue($config['user_info']['is_verified']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
