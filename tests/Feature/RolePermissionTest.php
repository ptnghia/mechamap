<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MarketplaceSeller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test admin can access admin dashboard
     */
    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin');
        
        $response->assertStatus(200);
    }

    /**
     * Test moderator can access admin dashboard
     */
    public function test_moderator_can_access_admin_dashboard()
    {
        $moderator = User::factory()->create(['role' => 'moderator']);
        
        $response = $this->actingAs($moderator)->get('/admin');
        
        $response->assertStatus(200);
    }

    /**
     * Test supplier cannot access admin dashboard
     */
    public function test_supplier_cannot_access_admin_dashboard()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        $response = $this->actingAs($supplier)->get('/admin');
        
        $response->assertRedirect('/supplier/dashboard');
    }

    /**
     * Test manufacturer cannot access admin dashboard
     */
    public function test_manufacturer_cannot_access_admin_dashboard()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        $response = $this->actingAs($manufacturer)->get('/admin');
        
        $response->assertRedirect('/manufacturer/dashboard');
    }

    /**
     * Test brand cannot access admin dashboard
     */
    public function test_brand_cannot_access_admin_dashboard()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        $response = $this->actingAs($brand)->get('/admin');
        
        $response->assertRedirect('/brand/dashboard');
    }

    /**
     * Test regular member cannot access admin dashboard
     */
    public function test_member_cannot_access_admin_dashboard()
    {
        $member = User::factory()->create(['role' => 'member']);
        
        $response = $this->actingAs($member)->get('/admin');
        
        $response->assertRedirect('/');
    }

    /**
     * Test supplier can access supplier dashboard
     */
    public function test_supplier_can_access_supplier_dashboard()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        // Create seller account for supplier
        MarketplaceSeller::factory()->create([
            'user_id' => $supplier->id,
            'seller_type' => 'supplier'
        ]);
        
        $response = $this->actingAs($supplier)->get('/supplier/dashboard');
        
        $response->assertStatus(200);
    }

    /**
     * Test manufacturer can access manufacturer dashboard
     */
    public function test_manufacturer_can_access_manufacturer_dashboard()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        // Create seller account for manufacturer
        MarketplaceSeller::factory()->create([
            'user_id' => $manufacturer->id,
            'seller_type' => 'manufacturer'
        ]);
        
        $response = $this->actingAs($manufacturer)->get('/manufacturer/dashboard');
        
        $response->assertStatus(200);
    }

    /**
     * Test brand can access brand dashboard
     */
    public function test_brand_can_access_brand_dashboard()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        $response = $this->actingAs($brand)->get('/brand/dashboard');
        
        $response->assertStatus(200);
    }

    /**
     * Test supplier cannot access manufacturer dashboard
     */
    public function test_supplier_cannot_access_manufacturer_dashboard()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        $response = $this->actingAs($supplier)->get('/manufacturer/dashboard');
        
        $response->assertRedirect('/supplier/dashboard');
    }

    /**
     * Test manufacturer cannot access supplier dashboard
     */
    public function test_manufacturer_cannot_access_supplier_dashboard()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        $response = $this->actingAs($manufacturer)->get('/supplier/dashboard');
        
        $response->assertRedirect('/manufacturer/dashboard');
    }

    /**
     * Test brand cannot access supplier dashboard
     */
    public function test_brand_cannot_access_supplier_dashboard()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        $response = $this->actingAs($brand)->get('/supplier/dashboard');
        
        $response->assertRedirect('/brand/dashboard');
    }

    /**
     * Test unauthenticated user cannot access any dashboard
     */
    public function test_unauthenticated_user_cannot_access_dashboards()
    {
        $dashboards = [
            '/admin',
            '/supplier/dashboard',
            '/manufacturer/dashboard',
            '/brand/dashboard'
        ];

        foreach ($dashboards as $dashboard) {
            $response = $this->get($dashboard);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Test marketplace seller setup access
     */
    public function test_business_users_can_access_seller_setup()
    {
        $businessRoles = ['supplier', 'manufacturer', 'brand'];

        foreach ($businessRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            
            $response = $this->actingAs($user)->get('/marketplace/seller/setup');
            
            $response->assertStatus(200);
        }
    }

    /**
     * Test non-business users cannot access seller setup
     */
    public function test_non_business_users_cannot_access_seller_setup()
    {
        $nonBusinessRoles = ['admin', 'moderator', 'member', 'guest'];

        foreach ($nonBusinessRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            
            $response = $this->actingAs($user)->get('/marketplace/seller/setup');
            
            $response->assertStatus(403);
        }
    }

    /**
     * Test user role methods
     */
    public function test_user_role_methods()
    {
        $roles = ['admin', 'moderator', 'supplier', 'manufacturer', 'brand', 'member', 'guest'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            
            // Test hasRole method
            $this->assertTrue($user->hasRole($role));
            $this->assertFalse($user->hasRole('invalid_role'));
            
            // Test hasAnyRole method
            $this->assertTrue($user->hasAnyRole([$role, 'other_role']));
            $this->assertFalse($user->hasAnyRole(['invalid_role1', 'invalid_role2']));
        }
    }

    /**
     * Test admin access middleware
     */
    public function test_admin_access_middleware()
    {
        // Admin should pass
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        // Moderator should pass
        $moderator = User::factory()->create(['role' => 'moderator']);
        $response = $this->actingAs($moderator)->get('/admin');
        $response->assertStatus(200);

        // Others should be redirected
        $supplier = User::factory()->create(['role' => 'supplier']);
        $response = $this->actingAs($supplier)->get('/admin');
        $response->assertRedirect();
    }

    /**
     * Test role middleware
     */
    public function test_role_middleware()
    {
        // Test supplier role middleware
        $supplier = User::factory()->create(['role' => 'supplier']);
        MarketplaceSeller::factory()->create([
            'user_id' => $supplier->id,
            'seller_type' => 'supplier'
        ]);
        
        $response = $this->actingAs($supplier)->get('/supplier/dashboard');
        $response->assertStatus(200);

        // Test wrong role access
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        $response = $this->actingAs($manufacturer)->get('/supplier/dashboard');
        $response->assertRedirect();
    }
}
