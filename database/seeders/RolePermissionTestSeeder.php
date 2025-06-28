<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MarketplaceSeller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for each role
        $testUsers = [
            [
                'name' => 'Admin Test User',
                'email' => 'admin@mechamap.test',
                'username' => 'admin_test',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Moderator Test User',
                'email' => 'moderator@mechamap.test',
                'username' => 'moderator_test',
                'role' => 'moderator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Supplier Test User',
                'email' => 'supplier@mechamap.test',
                'username' => 'supplier_test',
                'role' => 'supplier',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manufacturer Test User',
                'email' => 'manufacturer@mechamap.test',
                'username' => 'manufacturer_test',
                'role' => 'manufacturer',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Brand Test User',
                'email' => 'brand@mechamap.test',
                'username' => 'brand_test',
                'role' => 'brand',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Member Test User',
                'email' => 'member@mechamap.test',
                'username' => 'member_test',
                'role' => 'member',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Guest Test User',
                'email' => 'guest@mechamap.test',
                'username' => 'guest_test',
                'role' => 'guest',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::create($userData);
            
            // Create marketplace seller for business roles
            if (in_array($user->role, ['supplier', 'manufacturer', 'brand'])) {
                MarketplaceSeller::create([
                    'user_id' => $user->id,
                    'seller_type' => $user->role,
                    'business_type' => 'company',
                    'business_name' => $user->name . ' Business',
                    'contact_person_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => '+84 123 456 789',
                    'business_address' => [
                        'street' => '123 Test Street',
                        'city' => 'Ho Chi Minh City',
                        'state' => 'Ho Chi Minh',
                        'postal_code' => '70000',
                        'country' => 'Vietnam',
                    ],
                    'store_name' => $user->name . ' Store',
                    'store_slug' => strtolower(str_replace(' ', '-', $user->name)) . '-store',
                    'store_description' => 'Test store for ' . $user->getRoleDisplayName(),
                    'industry_categories' => ['mechanical_parts', 'tools'],
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                    'status' => 'active',
                    'commission_rate' => match($user->role) {
                        'supplier' => 5.0,
                        'manufacturer' => 3.0,
                        'brand' => 0.0,
                        default => 5.0
                    },
                ]);
            }
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@mechamap.test / password123');
        $this->command->info('Moderator: moderator@mechamap.test / password123');
        $this->command->info('Supplier: supplier@mechamap.test / password123');
        $this->command->info('Manufacturer: manufacturer@mechamap.test / password123');
        $this->command->info('Brand: brand@mechamap.test / password123');
        $this->command->info('Member: member@mechamap.test / password123');
        $this->command->info('Guest: guest@mechamap.test / password123');
    }
}
