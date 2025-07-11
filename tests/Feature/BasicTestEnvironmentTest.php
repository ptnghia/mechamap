<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 🧪 Basic Test Environment Verification
 * 
 * Kiểm tra basic testing environment hoạt động đúng
 */
class BasicTestEnvironmentTest extends TestCase
{
    /**
     * Test basic database connection
     */
    public function test_basic_database_connection(): void
    {
        // Test database connection
        $this->assertTrue(true);
        
        // Test environment
        $this->assertEquals('testing', app()->environment());
        $this->assertEquals('mechamap_backend_test', config('database.connections.mysql.database'));
    }

    /**
     * Test user creation without seeder
     */
    public function test_user_creation_without_seeder(): void
    {
        // Create user directly without seeder
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('member', $user->role);
    }

    /**
     * Test user factory basic functionality
     */
    public function test_user_factory_basic(): void
    {
        $user = User::factory()->create([
            'name' => 'Factory User',
            'role' => 'student'
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('Factory User', $user->name);
        $this->assertEquals('student', $user->role);
    }

    /**
     * Test business user creation
     */
    public function test_business_user_creation(): void
    {
        $businessUser = User::create([
            'name' => 'Business User',
            'username' => 'businessuser',
            'email' => 'business@example.com',
            'password' => bcrypt('password'),
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Test Company',
            'business_license' => 'BL-TEST-001',
            'tax_code' => '1234567890',
            'is_verified_business' => false,
        ]);

        $this->assertNotNull($businessUser);
        $this->assertEquals('manufacturer', $businessUser->role);
        $this->assertEquals('business_partners', $businessUser->role_group);
        $this->assertEquals('Test Company', $businessUser->company_name);
        $this->assertFalse($businessUser->is_verified_business);
    }

    /**
     * Test verified business user
     */
    public function test_verified_business_user(): void
    {
        $verifiedUser = User::create([
            'name' => 'Verified Business User',
            'username' => 'verifieduser',
            'email' => 'verified@example.com',
            'password' => bcrypt('password'),
            'role' => 'supplier',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Verified Company',
            'business_license' => 'BL-VERIFIED-001',
            'tax_code' => '9876543210',
            'is_verified_business' => true,
            'verified_at' => now(),
            'verified_by' => 1,
            'verification_notes' => 'Test verification',
        ]);

        $this->assertNotNull($verifiedUser);
        $this->assertEquals('supplier', $verifiedUser->role);
        $this->assertTrue($verifiedUser->is_verified_business);
        $this->assertNotNull($verifiedUser->verified_at);
        $this->assertEquals(1, $verifiedUser->verified_by);
    }

    /**
     * Test admin user creation
     */
    public function test_admin_user_creation(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'role_group' => 'system_management',
            'email_verified_at' => now(),
        ]);

        $this->assertNotNull($admin);
        $this->assertEquals('super_admin', $admin->role);
        $this->assertEquals('system_management', $admin->role_group);
    }

    /**
     * Test multiple user roles
     */
    public function test_multiple_user_roles(): void
    {
        $roles = [
            ['role' => 'member', 'group' => 'community_members'],
            ['role' => 'student', 'group' => 'community_members'],
            ['role' => 'manufacturer', 'group' => 'business_partners'],
            ['role' => 'supplier', 'group' => 'business_partners'],
            ['role' => 'brand', 'group' => 'business_partners'],
            ['role' => 'super_admin', 'group' => 'system_management'],
        ];

        foreach ($roles as $index => $roleData) {
            $user = User::create([
                'name' => "Test User {$index}",
                'username' => "testuser{$index}",
                'email' => "test{$index}@example.com",
                'password' => bcrypt('password'),
                'role' => $roleData['role'],
                'role_group' => $roleData['group'],
                'email_verified_at' => now(),
            ]);

            $this->assertEquals($roleData['role'], $user->role);
            $this->assertEquals($roleData['group'], $user->role_group);
        }
    }

    /**
     * Test cache and mail configuration
     */
    public function test_testing_configuration(): void
    {
        $this->assertEquals('array', config('cache.default'));
        $this->assertEquals('array', config('mail.default'));
        $this->assertEquals('sync', config('queue.default'));
        $this->assertEquals('array', config('session.driver'));
    }
}
