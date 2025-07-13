<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MenuServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear menu cache before each test
        Cache::flush();
    }

    /** @test */
    public function it_returns_guest_menu_for_unauthenticated_user()
    {
        $component = MenuService::getMenuComponent(null);
        
        $this->assertEquals('menu.guest-menu', $component);
    }

    /** @test */
    public function it_returns_correct_menu_component_for_admin_roles()
    {
        $adminRoles = ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'];
        
        foreach ($adminRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.admin-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_correct_menu_component_for_business_roles()
    {
        $businessRoles = ['verified_partner', 'manufacturer', 'supplier', 'brand'];
        
        foreach ($businessRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.business-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_correct_menu_component_for_member_roles()
    {
        $memberRoles = ['senior_member', 'member', 'guest'];
        
        foreach ($memberRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $component = MenuService::getMenuComponent($user);
            
            $this->assertEquals('menu.member-menu', $component, "Failed for role: {$role}");
        }
    }

    /** @test */
    public function it_returns_guest_menu_for_unknown_role()
    {
        $user = User::factory()->create(['role' => 'unknown_role']);
        $component = MenuService::getMenuComponent($user);
        
        $this->assertEquals('menu.guest-menu', $component);
    }

    /** @test */
    public function it_returns_menu_configuration_for_authenticated_user()
    {
        $user = User::factory()->create([
            'role' => 'member',
            'name' => 'Test User',
        ]);

        $config = MenuService::getMenuConfiguration($user);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('component', $config);
        $this->assertArrayHasKey('user_info', $config);
        $this->assertArrayHasKey('permissions', $config);
        $this->assertArrayHasKey('menu_items', $config);
        $this->assertArrayHasKey('features', $config);

        $this->assertEquals('menu.member-menu', $config['component']);
        $this->assertEquals('Test User', $config['user_info']['name']);
        $this->assertEquals('member', $config['user_info']['role']);
    }

    /** @test */
    public function it_returns_guest_configuration_for_unauthenticated_user()
    {
        $config = MenuService::getMenuConfiguration(null);

        $this->assertIsArray($config);
        $this->assertEquals('menu.guest-menu', $config['component']);
        $this->assertNull($config['user_info']);
        $this->assertEmpty($config['permissions']);
        $this->assertTrue($config['features']['can_register']);
        $this->assertTrue($config['features']['can_login']);
    }

    /** @test */
    public function it_caches_menu_configuration()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        // First call should cache the result
        $config1 = MenuService::getMenuConfiguration($user);
        
        // Second call should return cached result
        $config2 = MenuService::getMenuConfiguration($user);
        
        $this->assertEquals($config1, $config2);
        
        // Verify cache key exists
        $cacheKey = 'menu_config_' . $user->role . '_' . $user->id;
        $this->assertTrue(Cache::has($cacheKey));
    }

    /** @test */
    public function it_invalidates_user_menu_cache()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        // Cache the menu configuration
        MenuService::getMenuConfiguration($user);
        
        $cacheKey = 'menu_config_' . $user->role . '_' . $user->id;
        $this->assertTrue(Cache::has($cacheKey));
        
        // Invalidate cache
        MenuService::invalidateUserMenuCache($user);
        
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function it_validates_menu_routes_correctly()
    {
        // Create some test routes
        Route::get('/test-route', function () {
            return 'test';
        })->name('test.route');

        $user = User::factory()->create(['role' => 'member']);
        $config = MenuService::getMenuConfiguration($user);

        // Check that only valid routes are included
        foreach ($config['menu_items'] as $item) {
            $this->assertTrue(Route::has($item['route']), "Route {$item['route']} should exist");
        }
    }

    /** @test */
    public function it_handles_missing_routes_gracefully()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        // This should not throw an exception even if some routes don't exist
        $config = MenuService::getMenuConfiguration($user);
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('menu_items', $config);
    }

    /** @test */
    public function it_returns_correct_permissions_for_admin_user()
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $config = MenuService::getMenuConfiguration($user);

        $permissions = $config['permissions'];
        
        $this->assertTrue($permissions['can_access_admin']);
        $this->assertTrue($permissions['can_create_content']);
        $this->assertFalse($permissions['can_buy_products']); // Admin không phải business role
    }

    /** @test */
    public function it_returns_correct_permissions_for_business_user()
    {
        $user = User::factory()->create([
            'role' => 'manufacturer',
            'business_verified' => true
        ]);
        $config = MenuService::getMenuConfiguration($user);

        $permissions = $config['permissions'];
        
        $this->assertFalse($permissions['can_access_admin']);
        $this->assertTrue($permissions['can_create_content']);
        $this->assertTrue($permissions['can_buy_products']);
        $this->assertTrue($permissions['can_sell_products']);
        $this->assertTrue($permissions['can_view_cart']);
    }

    /** @test */
    public function it_returns_correct_permissions_for_guest_user()
    {
        $user = User::factory()->create(['role' => 'guest']);
        $config = MenuService::getMenuConfiguration($user);

        $permissions = $config['permissions'];
        
        $this->assertFalse($permissions['can_access_admin']);
        $this->assertFalse($permissions['can_create_content']);
        $this->assertFalse($permissions['can_buy_products']);
        $this->assertFalse($permissions['can_sell_products']);
        $this->assertFalse($permissions['can_view_cart']);
    }

    /** @test */
    public function it_checks_menu_item_access_correctly()
    {
        $user = User::factory()->create(['role' => 'member']);

        // Test public route access
        $canAccess = MenuService::canAccessMenuItem($user, 'home', null);
        $this->assertTrue($canAccess);

        // Test route that doesn't exist
        $canAccess = MenuService::canAccessMenuItem($user, 'non.existent.route', null);
        $this->assertFalse($canAccess);

        // Test unauthenticated user with public route
        $canAccess = MenuService::canAccessMenuItem(null, 'home', null);
        $this->assertTrue($canAccess);
    }

    /** @test */
    public function it_handles_business_verification_status()
    {
        // Unverified business user
        $unverifiedUser = User::factory()->create([
            'role' => 'manufacturer',
            'business_verified' => false
        ]);
        
        $config = MenuService::getMenuConfiguration($unverifiedUser);
        $this->assertFalse($config['permissions']['can_view_cart']);

        // Verified business user
        $verifiedUser = User::factory()->create([
            'role' => 'manufacturer',
            'business_verified' => true
        ]);
        
        $config = MenuService::getMenuConfiguration($verifiedUser);
        $this->assertTrue($config['permissions']['can_view_cart']);
    }

    /** @test */
    public function it_includes_role_specific_menu_items()
    {
        // Test admin user gets admin-specific items
        $adminUser = User::factory()->create(['role' => 'super_admin']);
        $adminConfig = MenuService::getMenuConfiguration($adminUser);
        
        $hasAdminRoute = false;
        foreach ($adminConfig['menu_items'] as $item) {
            if (str_contains($item['route'], 'admin')) {
                $hasAdminRoute = true;
                break;
            }
        }
        $this->assertTrue($hasAdminRoute, 'Admin user should have admin routes');

        // Test business user gets business-specific items
        $businessUser = User::factory()->create(['role' => 'manufacturer']);
        $businessConfig = MenuService::getMenuConfiguration($businessUser);
        
        $hasBusinessRoute = false;
        foreach ($businessConfig['menu_items'] as $item) {
            if (str_contains($item['route'], 'manufacturer')) {
                $hasBusinessRoute = true;
                break;
            }
        }
        $this->assertTrue($hasBusinessRoute, 'Business user should have business routes');
    }

    /** @test */
    public function it_handles_feature_flags_correctly()
    {
        $user = User::factory()->create(['role' => 'member']);
        $config = MenuService::getMenuConfiguration($user);

        $features = $config['features'];
        
        $this->assertFalse($features['show_admin_status_bar']);
        $this->assertFalse($features['show_business_status_bar']);
        $this->assertFalse($features['show_member_status_bar']); // Only for guest role
        $this->assertFalse($features['show_shopping_cart']);
        $this->assertTrue($features['show_create_dropdown']);
        $this->assertTrue($features['show_notifications']);
        $this->assertTrue($features['enable_search']);
        $this->assertTrue($features['show_language_switcher']);
    }
}
