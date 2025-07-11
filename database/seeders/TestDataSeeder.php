<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Showcase;
use Illuminate\Support\Facades\Hash;

/**
 * 🧪 Test Data Seeder for MechaMap Testing Environment
 * 
 * Tạo test data cho User Registration & Permission System testing
 */
class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ chạy trong testing environment
        if (app()->environment() !== 'testing') {
            return;
        }

        $this->createTestUsers();
        $this->createTestCategories();
        $this->createTestForums();
        $this->createTestThreads();
        $this->createTestShowcases();
    }

    /**
     * Tạo test users cho tất cả roles
     */
    private function createTestUsers(): void
    {
        // System Management Users
        User::create([
            'name' => 'Super Admin Test',
            'username' => 'superadmin_test',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'role_group' => 'system_management',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'System Admin Test',
            'username' => 'sysadmin_test',
            'email' => 'sysadmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'system_admin',
            'role_group' => 'system_management',
            'email_verified_at' => now(),
        ]);

        // Community Management Users
        User::create([
            'name' => 'Content Moderator Test',
            'username' => 'contentmod_test',
            'email' => 'contentmod@test.com',
            'password' => Hash::make('password'),
            'role' => 'content_moderator',
            'role_group' => 'community_management',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Marketplace Moderator Test',
            'username' => 'marketmod_test',
            'email' => 'marketmod@test.com',
            'password' => Hash::make('password'),
            'role' => 'marketplace_moderator',
            'role_group' => 'community_management',
            'email_verified_at' => now(),
        ]);

        // Community Members
        User::create([
            'name' => 'Senior Member Test',
            'username' => 'senior_test',
            'email' => 'senior@test.com',
            'password' => Hash::make('password'),
            'role' => 'senior_member',
            'role_group' => 'community_members',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Member Test',
            'username' => 'member_test',
            'email' => 'member@test.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'role_group' => 'community_members',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Test',
            'username' => 'student_test',
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'role_group' => 'community_members',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guest Test',
            'username' => 'guest_test',
            'email' => 'guest@test.com',
            'password' => Hash::make('password'),
            'role' => 'guest',
            'role_group' => 'community_members',
            'email_verified_at' => now(),
        ]);

        // Business Partners - Unverified
        User::create([
            'name' => 'Manufacturer Test',
            'username' => 'manufacturer_test',
            'email' => 'manufacturer@test.com',
            'password' => Hash::make('password'),
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Test Manufacturing Co.',
            'business_license' => 'BL-TEST-001',
            'tax_code' => '1234567890',
            'business_description' => 'Test manufacturing company for testing purposes',
            'business_categories' => json_encode(['automotive', 'aerospace']),
            'business_phone' => '+1-555-0001',
            'business_email' => 'business@manufacturer-test.com',
            'business_address' => '123 Manufacturing St, Test City, TC 12345',
            'is_verified_business' => false,
        ]);

        User::create([
            'name' => 'Supplier Test',
            'username' => 'supplier_test',
            'email' => 'supplier@test.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Test Supply Chain Ltd.',
            'business_license' => 'BL-TEST-002',
            'tax_code' => '2345678901',
            'business_description' => 'Test supplier company for testing purposes',
            'business_categories' => json_encode(['materials', 'components']),
            'business_phone' => '+1-555-0002',
            'business_email' => 'business@supplier-test.com',
            'business_address' => '456 Supply Ave, Test City, TC 12345',
            'is_verified_business' => false,
        ]);

        User::create([
            'name' => 'Brand Test',
            'username' => 'brand_test',
            'email' => 'brand@test.com',
            'password' => Hash::make('password'),
            'role' => 'brand',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Test Brand Corp.',
            'business_license' => 'BL-TEST-003',
            'tax_code' => '3456789012',
            'business_description' => 'Test brand company for testing purposes',
            'business_categories' => json_encode(['branding', 'marketing']),
            'business_phone' => '+1-555-0003',
            'business_email' => 'business@brand-test.com',
            'business_address' => '789 Brand Blvd, Test City, TC 12345',
            'is_verified_business' => false,
        ]);

        // Business Partners - Verified
        User::create([
            'name' => 'Verified Manufacturer Test',
            'username' => 'verified_manufacturer_test',
            'email' => 'verified.manufacturer@test.com',
            'password' => Hash::make('password'),
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Verified Manufacturing Co.',
            'business_license' => 'BL-VERIFIED-001',
            'tax_code' => '4567890123',
            'business_description' => 'Verified manufacturing company for testing purposes',
            'business_categories' => json_encode(['automotive', 'industrial']),
            'business_phone' => '+1-555-0004',
            'business_email' => 'business@verified-manufacturer.com',
            'business_address' => '321 Verified St, Test City, TC 12345',
            'is_verified_business' => true,
            'verified_at' => now(),
            'verified_by' => 1, // Super Admin
            'verification_notes' => 'Test verification for automated testing',
        ]);

        User::create([
            'name' => 'Verified Supplier Test',
            'username' => 'verified_supplier_test',
            'email' => 'verified.supplier@test.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => 'Verified Supply Chain Ltd.',
            'business_license' => 'BL-VERIFIED-002',
            'tax_code' => '5678901234',
            'business_description' => 'Verified supplier company for testing purposes',
            'business_categories' => json_encode(['materials', 'logistics']),
            'business_phone' => '+1-555-0005',
            'business_email' => 'business@verified-supplier.com',
            'business_address' => '654 Verified Ave, Test City, TC 12345',
            'is_verified_business' => true,
            'verified_at' => now(),
            'verified_by' => 1, // Super Admin
            'verification_notes' => 'Test verification for automated testing',
        ]);
    }

    /**
     * Tạo test categories
     */
    private function createTestCategories(): void
    {
        Category::create([
            'name' => 'Test Category 1',
            'slug' => 'test-category-1',
            'description' => 'Test category for automated testing',
            'color' => '#007bff',
            'icon' => 'fas fa-cog',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Test Category 2',
            'slug' => 'test-category-2',
            'description' => 'Another test category for automated testing',
            'color' => '#28a745',
            'icon' => 'fas fa-wrench',
            'is_active' => true,
        ]);
    }

    /**
     * Tạo test forums
     */
    private function createTestForums(): void
    {
        Forum::create([
            'category_id' => 1,
            'name' => 'Test Forum 1',
            'slug' => 'test-forum-1',
            'description' => 'Test forum for automated testing',
            'is_active' => true,
        ]);

        Forum::create([
            'category_id' => 2,
            'name' => 'Test Forum 2',
            'slug' => 'test-forum-2',
            'description' => 'Another test forum for automated testing',
            'is_active' => true,
        ]);
    }

    /**
     * Tạo test threads
     */
    private function createTestThreads(): void
    {
        Thread::create([
            'forum_id' => 1,
            'user_id' => 6, // Member Test
            'title' => 'Test Thread 1',
            'slug' => 'test-thread-1',
            'content' => 'This is a test thread for automated testing purposes.',
            'is_published' => true,
        ]);

        Thread::create([
            'forum_id' => 2,
            'user_id' => 7, // Student Test
            'title' => 'Test Thread 2',
            'slug' => 'test-thread-2',
            'content' => 'This is another test thread for automated testing purposes.',
            'is_published' => true,
        ]);
    }

    /**
     * Tạo test showcases
     */
    private function createTestShowcases(): void
    {
        Showcase::create([
            'user_id' => 9, // Manufacturer Test
            'title' => 'Test Showcase 1',
            'slug' => 'test-showcase-1',
            'description' => 'Test showcase for automated testing',
            'content' => 'This is a test showcase for automated testing purposes.',
            'software_used' => json_encode(['SolidWorks', 'AutoCAD']),
            'complexity_level' => 'intermediate',
            'is_published' => true,
        ]);

        Showcase::create([
            'user_id' => 10, // Supplier Test
            'title' => 'Test Showcase 2',
            'slug' => 'test-showcase-2',
            'description' => 'Another test showcase for automated testing',
            'content' => 'This is another test showcase for automated testing purposes.',
            'software_used' => json_encode(['Fusion 360', 'Inventor']),
            'complexity_level' => 'advanced',
            'is_published' => true,
        ]);
    }
}
