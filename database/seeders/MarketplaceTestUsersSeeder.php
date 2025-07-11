<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceSeller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds for testing marketplace permissions
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating test users for marketplace permission testing...');

        // Test users data with different roles
        $testUsers = [
            [
                'name' => 'Test Guest User',
                'username' => 'test_guest_2025',
                'email' => 'guest@mechamap.test',
                'role' => 'guest',
                'description' => 'Guest user - chỉ được mua/bán digital products'
            ],
            [
                'name' => 'Test Member User',
                'username' => 'test_member_2025',
                'email' => 'member@mechamap.test',
                'role' => 'member',
                'description' => 'Member user - chỉ được mua/bán digital products'
            ],
            [
                'name' => 'Test Senior Member',
                'username' => 'test_senior_2025',
                'email' => 'senior@mechamap.test',
                'role' => 'senior_member',
                'description' => 'Senior Member - chỉ được mua/bán digital products'
            ],
            [
                'name' => 'Test Supplier',
                'username' => 'test_supplier_2025',
                'email' => 'supplier@mechamap.test',
                'role' => 'supplier',
                'description' => 'Supplier - mua digital, bán digital + new_product',
                'business_info' => [
                    'company_name' => 'MechaSupply Co., Ltd',
                    'business_license' => 'SUP-2025-001',
                    'tax_code' => '0123456789',
                    'business_description' => 'Nhà cung cấp thiết bị cơ khí chuyên nghiệp',
                    'business_phone' => '+84-28-1234-5678',
                    'business_email' => 'business@mechasupply.com',
                    'business_address' => '123 Nguyễn Văn Cừ, Q.5, TP.HCM'
                ]
            ],
            [
                'name' => 'Test Manufacturer',
                'username' => 'test_manufacturer_2025',
                'email' => 'manufacturer@mechamap.test',
                'role' => 'manufacturer',
                'description' => 'Manufacturer - mua digital + new_product, bán digital',
                'business_info' => [
                    'company_name' => 'MechaManu Industries',
                    'business_license' => 'MFG-2025-001',
                    'tax_code' => '9876543210',
                    'business_description' => 'Nhà sản xuất thiết bị cơ khí công nghiệp',
                    'business_phone' => '+84-28-9876-5432',
                    'business_email' => 'contact@mechamanu.com',
                    'business_address' => '456 Lê Văn Việt, Q.9, TP.HCM'
                ]
            ],
            [
                'name' => 'Test Brand',
                'username' => 'test_brand_2025',
                'email' => 'brand@mechamap.test',
                'role' => 'brand',
                'description' => 'Brand - không được mua/bán, chỉ xem và liên hệ',
                'business_info' => [
                    'company_name' => 'MechaBrand Corp',
                    'business_license' => 'BRD-2025-001',
                    'tax_code' => '1122334455',
                    'business_description' => 'Thương hiệu thiết bị cơ khí cao cấp',
                    'business_phone' => '+84-28-1122-3344',
                    'business_email' => 'info@mechabrand.com',
                    'business_address' => '789 Võ Văn Tần, Q.3, TP.HCM'
                ]
            ]
        ];

        foreach ($testUsers as $userData) {
            $this->command->info("Processing user: {$userData['name']} ({$userData['role']})");

            // Check if user exists, update or create
            $user = User::where('email', $userData['email'])->first();

            if ($user) {
                $this->command->info("  User exists, updating...");
                $user->update([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'role' => $userData['role'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'avatar' => '/images/users/avatars/default-avatar.png',
                    'about_me' => $userData['description'],
                    'setup_progress' => 100,

                    // Business fields if applicable
                    'company_name' => $userData['business_info']['company_name'] ?? null,
                    'business_license' => $userData['business_info']['business_license'] ?? null,
                    'tax_code' => $userData['business_info']['tax_code'] ?? null,
                    'business_description' => $userData['business_info']['business_description'] ?? null,
                    'business_phone' => $userData['business_info']['business_phone'] ?? null,
                    'business_email' => $userData['business_info']['business_email'] ?? null,
                    'business_address' => $userData['business_info']['business_address'] ?? null,
                    'is_verified_business' => in_array($userData['role'], ['supplier', 'manufacturer', 'brand']),
                    'business_verified_at' => in_array($userData['role'], ['supplier', 'manufacturer', 'brand']) ? now() : null,
                ]);
            } else {
                $this->command->info("  Creating new user...");
                $user = User::create([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'avatar' => '/images/users/avatars/default-avatar.png',
                    'about_me' => $userData['description'],
                    'points' => rand(100, 1000),
                    'reaction_score' => rand(50, 500),
                    'last_seen_at' => now(),
                    'setup_progress' => 100,

                    // Business fields if applicable
                    'company_name' => $userData['business_info']['company_name'] ?? null,
                    'business_license' => $userData['business_info']['business_license'] ?? null,
                    'tax_code' => $userData['business_info']['tax_code'] ?? null,
                    'business_description' => $userData['business_info']['business_description'] ?? null,
                    'business_phone' => $userData['business_info']['business_phone'] ?? null,
                    'business_email' => $userData['business_info']['business_email'] ?? null,
                    'business_address' => $userData['business_info']['business_address'] ?? null,
                    'is_verified_business' => in_array($userData['role'], ['supplier', 'manufacturer', 'brand']),
                    'business_verified_at' => in_array($userData['role'], ['supplier', 'manufacturer', 'brand']) ? now() : null,
                ]);
            }

            // Create or update marketplace seller for business roles
            if (in_array($userData['role'], ['supplier', 'manufacturer', 'brand'])) {
                $seller = MarketplaceSeller::where('user_id', $user->id)->first();

                if ($seller) {
                    $this->command->info("  Updating marketplace seller...");
                    $seller->update([
                        'business_name' => $userData['business_info']['company_name'],
                        'seller_type' => $userData['role'],
                        'business_description' => $userData['business_info']['business_description'],
                        'contact_email' => $userData['business_info']['business_email'],
                        'contact_phone' => $userData['business_info']['business_phone'],
                        'business_address' => $userData['business_info']['business_address'],
                        'tax_identification_number' => $userData['business_info']['tax_code'],
                        'business_registration_number' => $userData['business_info']['business_license'],
                        'status' => 'active',
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'commission_rate' => $this->getCommissionRate($userData['role']),
                    ]);
                } else {
                    $this->command->info("  Creating marketplace seller...");
                    $seller = MarketplaceSeller::create([
                        'uuid' => Str::uuid(),
                        'user_id' => $user->id,
                        'business_name' => $userData['business_info']['company_name'],
                        'seller_type' => $userData['role'],
                        'business_description' => $userData['business_info']['business_description'],
                        'contact_email' => $userData['business_info']['business_email'],
                        'contact_phone' => $userData['business_info']['business_phone'],
                        'business_address' => $userData['business_info']['business_address'],
                        'tax_identification_number' => $userData['business_info']['tax_code'],
                        'business_registration_number' => $userData['business_info']['business_license'],
                        'status' => 'active',
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'commission_rate' => $this->getCommissionRate($userData['role']),
                        'total_sales' => 0,
                        'total_products' => 0,
                        'rating_average' => 5.0,
                        'rating_count' => 0,
                    ]);
                }

                $this->command->info("  ✅ Marketplace seller ready for {$userData['name']}");
            }

            $this->command->info("  ✅ User {$userData['name']} created successfully");
        }

        $this->command->info('');
        $this->command->info('🎉 Test users created successfully!');
        $this->command->info('');
        $this->command->info('📋 Login credentials (all passwords: password123):');
        foreach ($testUsers as $userData) {
            $this->command->info("  {$userData['role']}: {$userData['email']}");
        }
        $this->command->info('');
        $this->command->info('🔐 Permission Matrix Testing:');
        $this->command->info('  Guest/Member/Senior: Digital only');
        $this->command->info('  Supplier: Buy digital, Sell digital + new_product');
        $this->command->info('  Manufacturer: Buy digital + new_product, Sell digital');
        $this->command->info('  Brand: No buy/sell permissions');
    }

    /**
     * Get commission rate based on role
     */
    private function getCommissionRate(string $role): float
    {
        return match ($role) {
            'supplier' => 3.0,
            'manufacturer' => 5.0,
            'brand' => 0.0,
            default => 5.0
        };
    }
}
