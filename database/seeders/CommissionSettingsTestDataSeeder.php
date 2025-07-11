<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommissionSetting;
use Carbon\Carbon;

class CommissionSettingsTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating commission settings test data...');

        $settings = [
            // Manufacturer settings
            [
                'seller_role' => 'manufacturer',
                'product_type' => null, // All products
                'commission_rate' => 5.0,
                'fixed_fee' => 0,
                'min_commission' => 10000,
                'max_commission' => 0,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Default commission rate for manufacturers on all products',
                'created_by' => 1,
            ],
            [
                'seller_role' => 'manufacturer',
                'product_type' => 'digital',
                'commission_rate' => 3.0,
                'fixed_fee' => 5000,
                'min_commission' => 5000,
                'max_commission' => 100000,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Special rate for manufacturers selling digital products',
                'created_by' => 1,
            ],

            // Supplier settings
            [
                'seller_role' => 'supplier',
                'product_type' => null,
                'commission_rate' => 3.0,
                'fixed_fee' => 0,
                'min_commission' => 5000,
                'max_commission' => 0,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Default commission rate for suppliers',
                'created_by' => 1,
            ],
            [
                'seller_role' => 'supplier',
                'product_type' => 'new_product',
                'commission_rate' => 4.0,
                'fixed_fee' => 0,
                'min_commission' => 8000,
                'max_commission' => 500000,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Higher rate for suppliers selling new products',
                'created_by' => 1,
            ],

            // Brand settings
            [
                'seller_role' => 'brand',
                'product_type' => null,
                'commission_rate' => 0.0,
                'fixed_fee' => 0,
                'min_commission' => 0,
                'max_commission' => 0,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'No commission for brand partners (promotional)',
                'created_by' => 1,
            ],

            // Verified Partner settings
            [
                'seller_role' => 'verified_partner',
                'product_type' => null,
                'commission_rate' => 2.0,
                'fixed_fee' => 0,
                'min_commission' => 3000,
                'max_commission' => 0,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Preferential rate for verified partners',
                'created_by' => 1,
            ],
            [
                'seller_role' => 'verified_partner',
                'product_type' => 'service',
                'commission_rate' => 1.5,
                'fixed_fee' => 2000,
                'min_commission' => 2000,
                'max_commission' => 200000,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Special rate for verified partners offering services',
                'created_by' => 1,
            ],

            // Seasonal/Promotional settings (inactive examples)
            [
                'seller_role' => 'manufacturer',
                'product_type' => 'used_product',
                'commission_rate' => 7.0,
                'fixed_fee' => 0,
                'min_commission' => 15000,
                'max_commission' => 0,
                'effective_from' => Carbon::now()->subMonths(6),
                'effective_until' => Carbon::now()->subMonths(3),
                'is_active' => false,
                'description' => 'Expired promotional rate for used products',
                'created_by' => 1,
            ],
            [
                'seller_role' => 'supplier',
                'product_type' => 'digital',
                'commission_rate' => 2.5,
                'fixed_fee' => 3000,
                'min_commission' => 3000,
                'max_commission' => 50000,
                'effective_from' => Carbon::now()->addMonth(),
                'effective_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'description' => 'Future promotional rate for digital products',
                'created_by' => 1,
            ],

            // High-value order settings
            [
                'seller_role' => 'manufacturer',
                'product_type' => 'new_product',
                'commission_rate' => 4.5,
                'fixed_fee' => 0,
                'min_commission' => 20000,
                'max_commission' => 1000000,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_until' => null,
                'is_active' => true,
                'description' => 'Rate for manufacturers selling new products with commission cap',
                'created_by' => 1,
            ],
        ];

        foreach ($settings as $setting) {
            CommissionSetting::create($setting);
            $this->command->info("Created commission setting: {$setting['seller_role']} - " . ($setting['product_type'] ?? 'all products') . " - {$setting['commission_rate']}%");
        }

        $this->command->info('Commission settings test data created successfully!');
    }
}
