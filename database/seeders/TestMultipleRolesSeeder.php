<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * 🧪 Test Multiple Roles Seeder
 *
 * Tạo user test để demo multiple roles functionality
 */
class TestMultipleRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating test user with multiple roles...');

        // Tạo user test
        $user = User::create([
            'name' => 'Test Multiple Roles User',
            'username' => 'test_multi_roles',
            'email' => 'test.multi.roles@mechamap.test',
            'password' => Hash::make('password123'),
            'role' => 'content_moderator', // Role chính (backward compatibility)
            'role_group' => 'community_management',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Lấy roles để gán
        $contentModerator = Role::where('name', 'content_moderator')->first();
        $marketplaceModerator = Role::where('name', 'marketplace_moderator')->first();
        $communityModerator = Role::where('name', 'community_moderator')->first();

        if ($contentModerator && $marketplaceModerator && $communityModerator) {
            // Gán multiple roles
            $user->roles()->attach([
                $contentModerator->id => [
                    'is_primary' => true, // Role chính
                    'assigned_at' => now(),
                    'assigned_by' => 1, // Assume admin user ID = 1
                    'assignment_reason' => 'Test multiple roles - Content Moderator as primary',
                    'is_active' => true,
                ],
                $marketplaceModerator->id => [
                    'is_primary' => false,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_reason' => 'Test multiple roles - Additional marketplace moderation',
                    'is_active' => true,
                ],
                $communityModerator->id => [
                    'is_primary' => false,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_reason' => 'Test multiple roles - Additional community moderation',
                    'is_active' => true,
                ],
            ]);

            $this->command->info("✅ User '{$user->name}' created with 3 roles:");
            $this->command->info("   🎯 Primary: Content Moderator");
            $this->command->info("   📦 Additional: Marketplace Moderator");
            $this->command->info("   👥 Additional: Community Moderator");
        } else {
            $this->command->error("❌ Could not find required roles. Please run RoleSeeder first.");
        }

        // Tạo thêm user test khác
        $user2 = User::create([
            'name' => 'Business Multi-Role User',
            'username' => 'business_multi',
            'email' => 'business.multi@mechamap.test',
            'password' => Hash::make('password123'),
            'role' => 'manufacturer', // Role chính
            'role_group' => 'business_partners',
            'status' => 'active',
            'email_verified_at' => now(),
            'company_name' => 'Test Manufacturing Co.',
            'is_verified_business' => true,
        ]);

        // Gán roles cho business user
        $manufacturer = Role::where('name', 'manufacturer')->first();
        $supplier = Role::where('name', 'supplier')->first();
        $verifiedPartner = Role::where('name', 'verified_partner')->first();

        if ($manufacturer && $supplier && $verifiedPartner) {
            $user2->roles()->attach([
                $manufacturer->id => [
                    'is_primary' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_reason' => 'Primary business role - Manufacturing',
                    'is_active' => true,
                ],
                $supplier->id => [
                    'is_primary' => false,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_reason' => 'Also acts as supplier',
                    'is_active' => true,
                ],
                $verifiedPartner->id => [
                    'is_primary' => false,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_reason' => 'Verified business partner status',
                    'is_active' => true,
                ],
            ]);

            $this->command->info("✅ Business user '{$user2->name}' created with 3 roles:");
            $this->command->info("   🏭 Primary: Manufacturer");
            $this->command->info("   🚚 Additional: Supplier");
            $this->command->info("   ✅ Additional: Verified Partner");
        }

        $this->command->info('🎉 Multiple roles test data created successfully!');
    }
}
