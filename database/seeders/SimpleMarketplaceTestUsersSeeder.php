<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceSeller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SimpleMarketplaceTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating simple test users for marketplace permission testing...');

        // Test users data with updated permission matrix
        $testUsers = [
            [
                'name' => 'Test Guest User',
                'username' => 'test_guest_2025',
                'email' => 'guest@mechamap.test',
                'role' => 'guest',
                'create_seller' => true,
                'permissions' => 'Buy/Sell digital products only'
            ],
            [
                'name' => 'Test Member User',
                'username' => 'test_member_2025',
                'email' => 'member@mechamap.test',
                'role' => 'member',
                'create_seller' => false, // Members have NO marketplace access
                'permissions' => 'Community features only (NO marketplace access)'
            ],
            [
                'name' => 'Test Senior Member',
                'username' => 'test_senior_2025',
                'email' => 'senior@mechamap.test',
                'role' => 'senior_member',
                'create_seller' => false, // Senior members have NO marketplace access
                'permissions' => 'Community features only (NO marketplace access)'
            ],
            [
                'name' => 'Test Supplier',
                'username' => 'test_supplier_2025',
                'email' => 'supplier@mechamap.test',
                'role' => 'supplier',
                'create_seller' => true,
                'permissions' => 'Buy digital, Sell digital + new_product'
            ],
            [
                'name' => 'Test Manufacturer',
                'username' => 'test_manufacturer_2025',
                'email' => 'manufacturer@mechamap.test',
                'role' => 'manufacturer',
                'create_seller' => true,
                'permissions' => 'Buy/Sell digital + new_product'
            ],
            [
                'name' => 'Test Brand',
                'username' => 'test_brand_2025',
                'email' => 'brand@mechamap.test',
                'role' => 'brand',
                'create_seller' => true, // Brand has seller account for showcase only
                'permissions' => 'Showcase only (NO buy/sell transactions)'
            ],
        ];

        foreach ($testUsers as $userData) {
            $this->createTestUser($userData);
        }

        $this->command->info('✅ Simple marketplace test users created successfully!');
        $this->displayLoginCredentials();
    }

    /**
     * Create a test user
     */
    private function createTestUser(array $userData): void
    {
        // Check if user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        if ($existingUser) {
            $this->command->warn("User {$userData['email']} already exists, skipping...");
            return;
        }

        // Create user with only existing columns
        $user = User::create([
            'name' => $userData['name'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password' => Hash::make('password123'),
            'role' => $userData['role'],
            'status' => 'active',
            'email_verified_at' => now(),
            'avatar' => '/images/users/avatars/default-avatar.png',
            'about_me' => $userData['permissions'],
            'points' => rand(100, 500),
            'reaction_score' => rand(200, 800),
            'last_seen_at' => now(),
            'setup_progress' => 100,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ]);

        $this->command->info("✅ Created {$userData['role']} user: {$userData['email']}");

        // Create seller account if needed
        if ($userData['create_seller']) {
            $this->createSellerAccount($user, $userData);
        }
    }

    /**
     * Create seller account for user
     */
    private function createSellerAccount(User $user, array $userData): void
    {
        // Check if seller already exists
        $existingSeller = MarketplaceSeller::where('user_id', $user->id)->first();
        if ($existingSeller) {
            $this->command->warn("Seller account for {$user->email} already exists, skipping...");
            return;
        }

        $businessNames = [
            'guest' => 'Guest Digital Store',
            'supplier' => 'MechaParts Supplier Co.',
            'manufacturer' => 'VietMech Manufacturing Ltd.',
            'brand' => 'TechBrand Showcase',
        ];

        $seller = MarketplaceSeller::create([
            'user_id' => $user->id,
            'seller_type' => $user->role === 'guest' ? 'supplier' : $user->role, // Map guest to supplier type
            'business_name' => $businessNames[$user->role] ?? 'Test Business',
            'business_type' => $user->role === 'guest' ? 'individual' : 'company',
            'business_description' => "Test {$user->role} seller account - {$userData['permissions']}",
            'contact_person_name' => $user->name,
            'contact_email' => $user->email,
            'contact_phone' => '+84 901 234 567',
            'business_address' => 'Test Address, Ho Chi Minh City, Vietnam',
            'verification_status' => 'verified', // Pre-verify test accounts
            'verified_at' => now(),
            'status' => 'active',
            'total_products' => 0,
            'total_sales' => 0,
            'rating_average' => 0,
            'rating_count' => 0,
            'commission_rate' => 5.0,
            'is_featured' => false,
            'auto_approve_orders' => true,
            'processing_time_days' => 3,
        ]);

        $this->command->info("✅ Created seller account: {$seller->business_name}");
    }

    /**
     * Display login credentials for test users
     */
    private function displayLoginCredentials(): void
    {
        $this->command->info('');
        $this->command->info('=== 🧪 MARKETPLACE TEST ACCOUNTS (Updated Permission Matrix) ===');
        $this->command->info('🔑 Password for all accounts: password123');
        $this->command->info('🌐 Login URL: https://mechamap.test/login');
        $this->command->info('');

        $testAccounts = [
            ['role' => '👤 Guest', 'email' => 'guest@mechamap.test', 'permissions' => '✅ Buy/Sell digital products'],
            ['role' => '👥 Member', 'email' => 'member@mechamap.test', 'permissions' => '❌ NO marketplace access (community only)'],
            ['role' => '⭐ Senior Member', 'email' => 'senior.member@mechamap.test', 'permissions' => '❌ NO marketplace access (community only)'],
            ['role' => '🏪 Supplier', 'email' => 'supplier@mechamap.test', 'permissions' => '✅ Buy digital, Sell digital+new_product'],
            ['role' => '🏭 Manufacturer', 'email' => 'manufacturer@mechamap.test', 'permissions' => '✅ Buy/Sell digital+new_product'],
            ['role' => '🏷️ Brand', 'email' => 'brand@mechamap.test', 'permissions' => '❌ Showcase only (no transactions)'],
        ];

        foreach ($testAccounts as $account) {
            $this->command->info("{$account['role']}: {$account['email']}");
            $this->command->info("   {$account['permissions']}");
            $this->command->info('');
        }

        $this->command->info('=== 🧪 TESTING INSTRUCTIONS ===');
        $this->command->info('1. Login with each account to test role-based permissions');
        $this->command->info('2. Try accessing marketplace features:');
        $this->command->info('   - Guest: Should see digital product options');
        $this->command->info('   - Member/Senior: Should NOT see marketplace features');
        $this->command->info('   - Supplier: Should see digital + new_product options');
        $this->command->info('   - Manufacturer: Should see digital + new_product options');
        $this->command->info('   - Brand: Should see showcase features only');
        $this->command->info('3. Test product creation with different roles');
        $this->command->info('4. Verify permission restrictions are enforced');
        $this->command->info('');
        $this->command->info('🎯 Expected Results:');
        $this->command->info('- Member/Senior Member: No marketplace access');
        $this->command->info('- Guest: Digital products only');
        $this->command->info('- Supplier/Manufacturer: Full marketplace access');
        $this->command->info('- Brand: Read-only showcase');
        $this->command->info('');
    }
}
