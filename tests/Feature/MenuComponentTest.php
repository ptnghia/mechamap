<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

class MenuComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_menu_component_renders_correctly()
    {
        $view = View::make('components.menu.guest-menu');
        $html = $view->render();

        // Check for essential guest menu elements
        $this->assertStringContainsString('Trang chủ', $html);
        $this->assertStringContainsString('Forums', $html);
        $this->assertStringContainsString('Showcases', $html);
        $this->assertStringContainsString('Marketplace', $html);
        $this->assertStringContainsString('Đăng nhập', $html);
        $this->assertStringContainsString('Đăng ký', $html);
        $this->assertStringContainsString('guest-notice', $html);
    }

    /** @test */
    public function admin_menu_component_renders_correctly()
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Admin User',
            'business_verified' => false
        ]);

        $this->actingAs($user);

        $view = View::make('components.menu.admin-menu');
        $html = $view->render();

        // Check for essential admin menu elements
        $this->assertStringContainsString('Quản trị', $html);
        $this->assertStringContainsString('admin-status-bar', $html);
        $this->assertStringContainsString('Admin User', $html);
        $this->assertStringContainsString('fas fa-shield-alt', $html);
    }

    /** @test */
    public function member_menu_component_renders_correctly()
    {
        $user = User::factory()->create([
            'role' => 'member',
            'name' => 'Member User'
        ]);

        $this->actingAs($user);

        $view = View::make('components.menu.member-menu');
        $html = $view->render();

        // Check for essential member menu elements
        $this->assertStringContainsString('Member User', $html);
        $this->assertStringContainsString('Tạo mới', $html);
        $this->assertStringContainsString('fas fa-plus-circle', $html);
        $this->assertStringContainsString('user-dropdown', $html);
    }

    /** @test */
    public function business_menu_component_renders_correctly()
    {
        $user = User::factory()->create([
            'role' => 'manufacturer',
            'name' => 'Business User',
            'business_verified' => true
        ]);

        $this->actingAs($user);

        $view = View::make('components.menu.business-menu');
        $html = $view->render();

        // Check for essential business menu elements
        $this->assertStringContainsString('Business User', $html);
        $this->assertStringContainsString('business-status-bar', $html);
        $this->assertStringContainsString('fas fa-briefcase', $html);
        $this->assertStringContainsString('Đã xác thực', $html);
    }

    /** @test */
    public function business_menu_shows_verification_status()
    {
        // Test unverified business user
        $unverifiedUser = User::factory()->create([
            'role' => 'supplier',
            'business_verified' => false
        ]);

        $this->actingAs($unverifiedUser);

        $view = View::make('components.menu.business-menu');
        $html = $view->render();

        $this->assertStringContainsString('Chờ xác thực', $html);
        $this->assertStringContainsString('Xác thực ngay', $html);
        $this->assertStringContainsString('bg-warning', $html);

        // Test verified business user
        $verifiedUser = User::factory()->create([
            'role' => 'supplier',
            'business_verified' => true
        ]);

        $this->actingAs($verifiedUser);

        $view = View::make('components.menu.business-menu');
        $html = $view->render();

        $this->assertStringContainsString('Đã xác thực', $html);
        $this->assertStringContainsString('bg-success', $html);
    }

    /** @test */
    public function guest_role_member_menu_shows_restrictions()
    {
        $guestUser = User::factory()->create([
            'role' => 'guest',
            'name' => 'Guest User'
        ]);

        $this->actingAs($guestUser);

        $view = View::make('components.menu.member-menu');
        $html = $view->render();

        $this->assertStringContainsString('member-status-bar', $html);
        $this->assertStringContainsString('Tài khoản Guest', $html);
        $this->assertStringContainsString('Nâng cấp tài khoản', $html);
    }

    /** @test */
    public function shopping_cart_only_shows_for_business_users()
    {
        // Test member user (should not see cart)
        $memberUser = User::factory()->create(['role' => 'member']);
        $this->actingAs($memberUser);

        $memberView = View::make('components.menu.member-menu');
        $memberHtml = $memberView->render();
        $this->assertStringNotContainsString('fas fa-shopping-cart', $memberHtml);

        // Test business user (should see cart if verified)
        $businessUser = User::factory()->create([
            'role' => 'manufacturer',
            'business_verified' => true
        ]);
        $this->actingAs($businessUser);

        $businessView = View::make('components.menu.business-menu');
        $businessHtml = $businessView->render();
        $this->assertStringContainsString('fas fa-shopping-cart', $businessHtml);
    }

    /** @test */
    public function dynamic_header_component_renders_correctly()
    {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user);

        $view = View::make('components.dynamic-header');
        $html = $view->render();

        // Check for essential dynamic header elements
        $this->assertStringContainsString('site-header', $html);
        $this->assertStringContainsString('header-content', $html);
        $this->assertStringContainsString('searchModal', $html);
        $this->assertStringContainsString('x-dynamic-component', $html);
    }

    /** @test */
    public function menu_components_handle_missing_user_gracefully()
    {
        // Test guest menu without authentication
        $guestView = View::make('components.menu.guest-menu');
        $guestHtml = $guestView->render();
        $this->assertStringContainsString('Đăng nhập', $guestHtml);

        // Test dynamic header without authentication
        $headerView = View::make('components.dynamic-header');
        $headerHtml = $headerView->render();
        $this->assertStringContainsString('menu.guest-menu', $headerHtml);
    }

    /** @test */
    public function menu_components_include_proper_icons()
    {
        $iconTests = [
            'guest-menu' => ['fas fa-home', 'fas fa-comments', 'fas fa-star', 'fas fa-store'],
            'admin-menu' => ['fas fa-shield-alt', 'fas fa-tachometer-alt', 'fas fa-users'],
            'member-menu' => ['fas fa-plus-circle', 'fas fa-bell', 'fas fa-search'],
            'business-menu' => ['fas fa-briefcase', 'fas fa-shopping-cart', 'fas fa-chart-line'],
        ];

        foreach ($iconTests as $component => $expectedIcons) {
            $view = View::make("components.menu.{$component}");
            $html = $view->render();

            foreach ($expectedIcons as $icon) {
                $this->assertStringContainsString($icon, $html, "Icon {$icon} not found in {$component}");
            }
        }
    }

    /** @test */
    public function menu_components_include_responsive_classes()
    {
        $components = ['guest-menu', 'admin-menu', 'member-menu', 'business-menu'];

        foreach ($components as $component) {
            $view = View::make("components.menu.{$component}");
            $html = $view->render();

            // Check for Bootstrap responsive classes
            $this->assertStringContainsString('navbar-expand-lg', $html);
            $this->assertStringContainsString('navbar-toggler', $html);
            $this->assertStringContainsString('collapse navbar-collapse', $html);
        }
    }

    /** @test */
    public function menu_components_include_accessibility_attributes()
    {
        $components = ['guest-menu', 'admin-menu', 'member-menu', 'business-menu'];

        foreach ($components as $component) {
            $view = View::make("components.menu.{$component}");
            $html = $view->render();

            // Check for accessibility attributes
            $this->assertStringContainsString('aria-expanded', $html);
            $this->assertStringContainsString('aria-label', $html);
            $this->assertStringContainsString('role="button"', $html);
        }
    }

    /** @test */
    public function business_menu_shows_role_specific_dashboards()
    {
        $roleTests = [
            'verified_partner' => 'partner.dashboard',
            'manufacturer' => 'manufacturer.dashboard',
            'supplier' => 'supplier.dashboard',
            'brand' => 'brand.dashboard',
        ];

        foreach ($roleTests as $role => $expectedRoute) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user);

            $view = View::make('components.menu.business-menu');
            $html = $view->render();

            $this->assertStringContainsString($expectedRoute, $html, "Route {$expectedRoute} not found for role {$role}");
        }
    }

    /** @test */
    public function menu_components_handle_empty_notifications()
    {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user);

        $view = View::make('components.menu.member-menu');
        $html = $view->render();

        // Should handle empty notifications gracefully
        $this->assertStringContainsString('Không có thông báo mới', $html);
    }
}
