<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 🧪 Test Environment Verification
 * 
 * Kiểm tra testing environment hoạt động đúng
 */
class TestEnvironmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test database connection và seeding
     */
    public function test_database_connection_and_seeding(): void
    {
        // Kiểm tra database connection
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@test.com'
        ]);

        // Kiểm tra test users được tạo đúng
        $superAdmin = User::where('email', 'superadmin@test.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertEquals('super_admin', $superAdmin->role);
        $this->assertEquals('system_management', $superAdmin->role_group);
    }

    /**
     * Test user factory methods
     */
    public function test_user_factory_methods(): void
    {
        // Test basic user creation
        $user = User::factory()->create();
        $this->assertNotNull($user);
        $this->assertEquals('member', $user->role);

        // Test business user creation
        $businessUser = User::factory()->business('manufacturer')->create();
        $this->assertEquals('manufacturer', $businessUser->role);
        $this->assertEquals('business_partners', $businessUser->role_group);
        $this->assertNotNull($businessUser->company_name);
        $this->assertFalse($businessUser->is_verified_business);

        // Test verified business user creation
        $verifiedUser = User::factory()->verifiedBusiness('supplier')->create();
        $this->assertEquals('supplier', $verifiedUser->role);
        $this->assertTrue($verifiedUser->is_verified_business);
        $this->assertNotNull($verifiedUser->verified_at);

        // Test admin user creation
        $admin = User::factory()->admin('system_admin')->create();
        $this->assertEquals('system_admin', $admin->role);
        $this->assertEquals('system_management', $admin->role_group);
    }

    /**
     * Test helper methods trong TestCase
     */
    public function test_testcase_helper_methods(): void
    {
        // Test createUser helper
        $user = $this->createUser('student');
        $this->assertEquals('student', $user->role);
        $this->assertNotNull($user->email_verified_at);

        // Test createBusinessUser helper
        $businessUser = $this->createBusinessUser('manufacturer');
        $this->assertEquals('manufacturer', $businessUser->role);
        $this->assertNotNull($businessUser->company_name);
        $this->assertFalse($businessUser->is_verified_business);

        // Test createVerifiedBusinessUser helper
        $verifiedUser = $this->createVerifiedBusinessUser('supplier');
        $this->assertEquals('supplier', $verifiedUser->role);
        $this->assertTrue($verifiedUser->is_verified_business);

        // Test createAdmin helper
        $admin = $this->createAdmin('content_admin');
        $this->assertEquals('content_admin', $admin->role);
    }

    /**
     * Test assertion methods
     */
    public function test_custom_assertion_methods(): void
    {
        $user = $this->createUser('member');
        $this->assertUserHasRole($user, 'member');

        $businessUser = $this->createVerifiedBusinessUser('manufacturer');
        $this->assertBusinessUserIsVerified($businessUser);
    }

    /**
     * Test all user roles được tạo trong seeder
     */
    public function test_all_user_roles_seeded(): void
    {
        $expectedRoles = [
            'super_admin',
            'system_admin', 
            'content_moderator',
            'marketplace_moderator',
            'senior_member',
            'member',
            'student',
            'guest',
            'manufacturer',
            'supplier',
            'brand'
        ];

        foreach ($expectedRoles as $role) {
            $this->assertDatabaseHas('users', ['role' => $role]);
        }
    }

    /**
     * Test business users có business information
     */
    public function test_business_users_have_business_info(): void
    {
        $manufacturer = User::where('role', 'manufacturer')->first();
        $this->assertNotNull($manufacturer->company_name);
        $this->assertNotNull($manufacturer->business_license);
        $this->assertNotNull($manufacturer->tax_code);

        $supplier = User::where('role', 'supplier')->first();
        $this->assertNotNull($supplier->company_name);
        $this->assertNotNull($supplier->business_license);
        $this->assertNotNull($supplier->tax_code);
    }

    /**
     * Test verified business users
     */
    public function test_verified_business_users(): void
    {
        $verifiedUsers = User::where('is_verified_business', true)->get();
        $this->assertGreaterThan(0, $verifiedUsers->count());

        foreach ($verifiedUsers as $user) {
            $this->assertNotNull($user->verified_at);
            $this->assertNotNull($user->verified_by);
            $this->assertNotNull($user->verification_notes);
        }
    }

    /**
     * Test categories và forums được tạo
     */
    public function test_categories_and_forums_seeded(): void
    {
        $this->assertDatabaseHas('categories', ['name' => 'Test Category 1']);
        $this->assertDatabaseHas('categories', ['name' => 'Test Category 2']);
        
        $this->assertDatabaseHas('forums', ['name' => 'Test Forum 1']);
        $this->assertDatabaseHas('forums', ['name' => 'Test Forum 2']);
    }

    /**
     * Test threads và showcases được tạo
     */
    public function test_threads_and_showcases_seeded(): void
    {
        $this->assertDatabaseHas('threads', ['title' => 'Test Thread 1']);
        $this->assertDatabaseHas('threads', ['title' => 'Test Thread 2']);
        
        $this->assertDatabaseHas('showcases', ['title' => 'Test Showcase 1']);
        $this->assertDatabaseHas('showcases', ['title' => 'Test Showcase 2']);
    }

    /**
     * Test environment variables
     */
    public function test_environment_variables(): void
    {
        $this->assertEquals('testing', app()->environment());
        $this->assertEquals('mechamap_backend_test', config('database.connections.mysql.database'));
        $this->assertEquals('array', config('cache.default'));
        $this->assertEquals('array', config('mail.default'));
    }
}
