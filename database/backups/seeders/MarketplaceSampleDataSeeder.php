<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Support\Str;

class MarketplaceSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create marketplace sellers first
        $this->createMarketplaceSellers();

        // Create marketplace products
        $this->createMarketplaceProducts();
    }

    private function createMarketplaceSellers(): void
    {
        // Get users with business roles
        $suppliers = User::where('role', 'supplier')->limit(5)->get();
        $manufacturers = User::where('role', 'manufacturer')->limit(4)->get();
        $brands = User::where('role', 'brand')->limit(3)->get();

        // Create sellers for suppliers
        foreach ($suppliers as $user) {
            MarketplaceSeller::create([
                'user_id' => $user->id,
                'seller_type' => 'supplier',
                'business_type' => 'company',
                'business_name' => $user->name . ' Supply Co.',
                'business_registration_number' => 'REG' . rand(1000000, 9999999),
                'tax_identification_number' => '030' . rand(1000000, 9999999),
                'business_description' => 'Professional mechanical parts supplier with over 10 years experience.',
                'contact_person_name' => $user->name,
                'contact_email' => strtolower(str_replace(' ', '', $user->name)) . '@supply.com',
                'contact_phone' => '+84 28 ' . rand(1000000, 9999999),
                'business_address' => 'Industrial Zone, Ho Chi Minh City, Vietnam',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'status' => 'active',
                'commission_rate' => 5.0,
                'total_sales' => rand(100, 1000),
                'total_revenue' => rand(50000, 500000),
                'total_products' => rand(10, 50),
                'active_products' => rand(5, 30),
                'rating_average' => rand(40, 50) / 10,
                'rating_count' => rand(50, 200),
                'store_name' => $user->name . ' Supply Store',
                'store_slug' => Str::slug($user->name . '-supply-' . rand(1000, 9999)),
                'store_description' => 'Your trusted partner for mechanical engineering supplies.',
                'auto_approve_orders' => true,
                'processing_time_days' => rand(1, 5),
                'is_featured' => rand(1, 100) <= 20,
                'last_active_at' => now(),
            ]);
        }

        // Create sellers for manufacturers
        foreach ($manufacturers as $user) {
            MarketplaceSeller::create([
                'user_id' => $user->id,
                'seller_type' => 'manufacturer',
                'business_type' => 'corporation',
                'business_name' => $user->name . ' Manufacturing',
                'business_registration_number' => 'MFG' . rand(1000000, 9999999),
                'tax_identification_number' => '031' . rand(1000000, 9999999),
                'business_description' => 'Advanced manufacturing solutions for mechanical engineering industry.',
                'contact_person_name' => $user->name,
                'contact_email' => strtolower(str_replace(' ', '', $user->name)) . '@manufacturing.com',
                'contact_phone' => '+84 274 ' . rand(1000000, 9999999),
                'business_address' => 'Manufacturing District, Binh Duong, Vietnam',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'status' => 'active',
                'commission_rate' => 7.0,
                'total_sales' => rand(50, 500),
                'total_revenue' => rand(100000, 1000000),
                'total_products' => rand(15, 60),
                'active_products' => rand(10, 40),
                'rating_average' => rand(42, 50) / 10,
                'rating_count' => rand(30, 150),
                'store_name' => $user->name . ' Manufacturing',
                'store_slug' => Str::slug($user->name . '-manufacturing-' . rand(1000, 9999)),
                'store_description' => 'Professional manufacturing services and products.',
                'auto_approve_orders' => false,
                'processing_time_days' => rand(3, 10),
                'is_featured' => rand(1, 100) <= 30,
                'last_active_at' => now(),
            ]);
        }

        // Create sellers for brands
        foreach ($brands as $user) {
            MarketplaceSeller::create([
                'user_id' => $user->id,
                'seller_type' => 'brand',
                'business_type' => 'corporation',
                'business_name' => $user->name . ' Brand',
                'business_registration_number' => 'BRD' . rand(1000000, 9999999),
                'tax_identification_number' => '032' . rand(1000000, 9999999),
                'business_description' => 'Premium mechanical engineering brand with global presence.',
                'contact_person_name' => $user->name,
                'contact_email' => strtolower(str_replace(' ', '', $user->name)) . '@brand.com',
                'contact_phone' => '+84 24 ' . rand(1000000, 9999999),
                'business_address' => 'Business Center, Hanoi, Vietnam',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'status' => 'active',
                'commission_rate' => 10.0,
                'total_sales' => rand(20, 200),
                'total_revenue' => rand(200000, 2000000),
                'total_products' => rand(5, 25),
                'active_products' => rand(3, 15),
                'rating_average' => rand(45, 50) / 10,
                'rating_count' => rand(20, 100),
                'store_name' => $user->name . ' Brand Store',
                'store_slug' => Str::slug($user->name . '-brand-' . rand(1000, 9999)),
                'store_description' => 'Premium quality mechanical engineering products.',
                'auto_approve_orders' => true,
                'processing_time_days' => rand(1, 3),
                'is_featured' => rand(1, 100) <= 50,
                'last_active_at' => now(),
            ]);
        }

        $this->command->info('Created ' . MarketplaceSeller::count() . ' marketplace sellers');
    }

    private function createMarketplaceProducts(): void
    {
        $sellers = MarketplaceSeller::all();
        $categories = ProductCategory::all();

        $productTemplates = [
            // Mechanical Parts
            ['name' => 'Precision Ball Bearing Set', 'type' => 'physical', 'price_range' => [25, 150]],
            ['name' => 'Industrial Gear Assembly', 'type' => 'physical', 'price_range' => [100, 500]],
            ['name' => 'Hydraulic Cylinder Kit', 'type' => 'physical', 'price_range' => [200, 800]],
            ['name' => 'Pneumatic Valve System', 'type' => 'physical', 'price_range' => [150, 600]],
            ['name' => 'Motor Coupling Set', 'type' => 'physical', 'price_range' => [50, 250]],

            // CAD Files & Digital Products
            ['name' => 'CAD Model - Gear Assembly', 'type' => 'digital', 'price_range' => [15, 100]],
            ['name' => 'Technical Drawing - Bearing Housing', 'type' => 'digital', 'price_range' => [20, 80]],
            ['name' => 'SolidWorks Model - Engine Block', 'type' => 'digital', 'price_range' => [50, 200]],
            ['name' => 'AutoCAD Drawing - Mechanical Frame', 'type' => 'digital', 'price_range' => [25, 120]],
            ['name' => 'FEA Analysis Report - Stress Testing', 'type' => 'digital', 'price_range' => [100, 400]],

            // Materials
            ['name' => 'Aluminum Sheet 6061-T6', 'type' => 'physical', 'price_range' => [30, 200]],
            ['name' => 'Stainless Steel Rod 316L', 'type' => 'physical', 'price_range' => [40, 300]],
            ['name' => 'Carbon Fiber Plate', 'type' => 'physical', 'price_range' => [80, 500]],
            ['name' => 'Titanium Alloy Bar', 'type' => 'physical', 'price_range' => [200, 1000]],

            // Tools & Equipment
            ['name' => 'Digital Caliper Set', 'type' => 'physical', 'price_range' => [50, 300]],
            ['name' => 'Precision Micrometer', 'type' => 'physical', 'price_range' => [80, 400]],
            ['name' => 'Torque Wrench Kit', 'type' => 'physical', 'price_range' => [100, 600]],
            ['name' => 'Surface Roughness Tester', 'type' => 'physical', 'price_range' => [500, 2000]],

            // Services
            ['name' => 'CNC Machining Service', 'type' => 'service', 'price_range' => [100, 1000]],
            ['name' => '3D Printing Service', 'type' => 'service', 'price_range' => [50, 500]],
            ['name' => 'Engineering Consultation', 'type' => 'service', 'price_range' => [200, 800]],
            ['name' => 'Quality Inspection Service', 'type' => 'service', 'price_range' => [150, 600]],
        ];

        foreach ($productTemplates as $template) {
            foreach ($sellers->random(rand(2, 4)) as $seller) {
                $category = $categories->random();
                $price = rand($template['price_range'][0], $template['price_range'][1]);
                $isOnSale = rand(1, 100) <= 20; // 20% chance of being on sale
                $salePrice = $isOnSale ? $price * (rand(70, 90) / 100) : null;

                MarketplaceProduct::create([
                    'seller_id' => $seller->id,
                    'product_category_id' => $category->id,
                    'name' => $template['name'] . ' - ' . $seller->business_name,
                    'slug' => Str::slug($template['name'] . '-' . $seller->business_name . '-' . rand(1000, 9999)),
                    'short_description' => 'High-quality ' . strtolower($template['name']) . ' for professional mechanical engineering applications.',
                    'description' => $this->generateProductDescription($template['name'], $template['type']),
                    'product_type' => $template['type'],
                    'seller_type' => $seller->seller_type,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'is_on_sale' => $isOnSale,
                    'sku' => strtoupper(substr($template['type'], 0, 3)) . rand(10000, 99999),
                    'stock_quantity' => $template['type'] === 'digital' ? 999 : rand(5, 100),
                    'manage_stock' => $template['type'] !== 'digital',
                    'in_stock' => true,
                    'low_stock_threshold' => $template['type'] === 'digital' ? 0 : 5,
                    'technical_specs' => json_encode($this->generateTechnicalSpecs($template['type'])),
                    'material' => $template['type'] === 'physical' ? ['Steel', 'Aluminum', 'Stainless Steel', 'Titanium'][rand(0, 3)] : null,
                    'manufacturing_process' => $template['type'] === 'physical' ? ['CNC Machining', 'Casting', 'Forging', 'Welding'][rand(0, 3)] : null,
                    'standards_compliance' => json_encode(['ISO 9001:2015', 'ANSI/ASME', 'DIN Standards']),
                    'file_formats' => $template['type'] === 'digital' ? json_encode(['STEP', 'IGES', 'DWG', 'PDF']) : null,
                    'software_compatibility' => $template['type'] === 'digital' ? json_encode(['SolidWorks', 'AutoCAD', 'Fusion 360']) : null,
                    'file_size_mb' => $template['type'] === 'digital' ? rand(1, 50) : null,
                    'download_limit' => $template['type'] === 'digital' ? 5 : null,
                    'status' => 'approved',
                    'is_active' => true,
                    'is_featured' => rand(1, 100) <= 15, // 15% chance of being featured
                    'featured_at' => rand(1, 100) <= 15 ? now() : null,
                    'approved_at' => now(),
                    'view_count' => rand(10, 1000),
                    'like_count' => rand(0, 50),
                    'download_count' => $template['type'] === 'digital' ? rand(0, 200) : 0,
                    'purchase_count' => rand(0, 100),
                    'rating_average' => rand(35, 50) / 10,
                    'rating_count' => rand(5, 100),
                    'meta_title' => $template['name'] . ' - Professional Quality',
                    'meta_description' => 'Buy high-quality ' . strtolower($template['name']) . ' from verified sellers. Fast shipping and professional support.',
                    'tags' => json_encode([
                        strtolower($template['name']),
                        'mechanical engineering',
                        $template['type'],
                        'professional',
                        'quality'
                    ]),
                ]);
            }
        }

        $this->command->info('Created ' . MarketplaceProduct::count() . ' marketplace products');
    }

    private function generateProductDescription(string $productName, string $type): string
    {
        $descriptions = [
            'physical' => "Professional grade {$productName} designed for demanding mechanical engineering applications. Manufactured using premium materials and precision engineering processes. Suitable for industrial, automotive, and aerospace applications. Comes with quality certification and warranty.",
            'digital' => "High-quality digital {$productName} created by experienced mechanical engineers. Compatible with major CAD software including SolidWorks, AutoCAD, and Fusion 360. Includes detailed specifications, material properties, and manufacturing notes. Instant download after purchase.",
            'service' => "Professional {$productName} provided by certified mechanical engineers with extensive industry experience. We use state-of-the-art equipment and follow international quality standards. Custom solutions available for specific requirements."
        ];

        return $descriptions[$type] ?? $descriptions['physical'];
    }

    private function generateTechnicalSpecs(string $type): array
    {
        $specs = [
            'physical' => [
                'Material' => ['Steel', 'Aluminum', 'Stainless Steel', 'Titanium'][rand(0, 3)],
                'Tolerance' => '±0.01mm',
                'Surface Finish' => 'Ra 0.8μm',
                'Hardness' => rand(40, 60) . ' HRC',
                'Operating Temperature' => '-20°C to +150°C',
                'Certification' => 'ISO 9001:2015'
            ],
            'digital' => [
                'File Format' => ['STEP', 'IGES', 'DWG', 'PDF'][rand(0, 3)],
                'CAD Software' => ['SolidWorks', 'AutoCAD', 'Fusion 360'][rand(0, 2)],
                'File Size' => rand(1, 50) . ' MB',
                'Drawing Scale' => '1:1',
                'Units' => 'Metric (mm)',
                'Layers' => rand(5, 20) . ' layers'
            ],
            'service' => [
                'Lead Time' => rand(3, 14) . ' days',
                'Minimum Order' => '1 piece',
                'Precision' => '±0.05mm',
                'Quality Standard' => 'ISO 9001:2015',
                'Material Options' => 'Multiple available',
                'Delivery' => 'Worldwide shipping'
            ]
        ];

        return $specs[$type] ?? $specs['physical'];
    }
}
