<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BusinessUserSeeder extends Seeder
{
    /**
     * Tạo dữ liệu mẫu cho business users (Supplier, Manufacturer, Brand)
     */
    public function run(): void
    {
        // ====================================================================
        // SUPPLIERS - Nhà cung cấp
        // ====================================================================
        $suppliers = [
            [
                'name' => 'Công ty TNHH Thép Việt Nam',
                'username' => 'thep_vietnam',
                'email' => 'contact@thepvietnam.com',
                'password' => Hash::make('Supplier2024@'),
                'role' => 'supplier',
                'avatar' => '/images/avatars/supplier1.jpg',
                'company_name' => 'Công ty TNHH Thép Việt Nam',
                'business_license' => '0123456789',
                'tax_code' => '0123456789-001',
                'business_description' => 'Chuyên cung cấp thép xây dựng, thép công nghiệp, vật liệu kim loại chất lượng cao cho các dự án cơ khí.',
                'business_categories' => ['steel', 'metal', 'construction_materials'],
                'business_phone' => '+84-28-1234-5678',
                'business_email' => 'sales@thepvietnam.com',
                'business_address' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'premium',
                'business_rating' => 4.5,
                'total_reviews' => 127,
                'points' => 2500,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
            ],
            [
                'name' => 'Vật Liệu Cơ Khí Hà Nội',
                'username' => 'vlck_hanoi',
                'email' => 'info@vlckhanoi.vn',
                'password' => Hash::make('Supplier2024@'),
                'role' => 'supplier',
                'avatar' => '/images/avatars/supplier2.jpg',
                'company_name' => 'Công ty Cổ phần Vật Liệu Cơ Khí Hà Nội',
                'business_license' => '0987654321',
                'tax_code' => '0987654321-002',
                'business_description' => 'Nhà phân phối ủy quyền các thiết bị cơ khí, dụng cụ công nghiệp, phụ tùng máy móc.',
                'business_categories' => ['tools', 'equipment', 'spare_parts'],
                'business_phone' => '+84-24-9876-5432',
                'business_email' => 'sales@vlckhanoi.vn',
                'business_address' => '456 Phố Huế, Hai Bà Trưng, Hà Nội',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'basic',
                'business_rating' => 4.2,
                'total_reviews' => 89,
                'points' => 1800,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Hà Nội, Việt Nam',
            ],
            [
                'name' => 'Industrial Tools Vietnam',
                'username' => 'industrial_tools_vn',
                'email' => 'contact@industrialtools.vn',
                'password' => Hash::make('Tools2024@'),
                'role' => 'supplier',
                'avatar' => '/images/avatars/supplier3.jpg',
                'company_name' => 'Công ty TNHH Industrial Tools Vietnam',
                'business_license' => '1234567890',
                'tax_code' => '1234567890-003',
                'business_description' => 'Nhà cung cấp dụng cụ công nghiệp chuyên nghiệp, thiết bị đo lường, máy móc chính xác từ các thương hiệu hàng đầu thế giới.',
                'business_categories' => ['precision_tools', 'measuring_equipment', 'industrial_machinery'],
                'business_phone' => '+84-28-2345-6789',
                'business_email' => 'sales@industrialtools.vn',
                'business_address' => '789 Đường Lê Văn Việt, Quận 9, TP.HCM',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.7,
                'total_reviews' => 156,
                'points' => 3200,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
            ],
            [
                'name' => 'Bearing & Fastener Supply Co.',
                'username' => 'bearing_fastener',
                'email' => 'info@bearingfastener.com.vn',
                'password' => Hash::make('Bearing2024@'),
                'role' => 'supplier',
                'avatar' => '/images/avatars/supplier4.jpg',
                'company_name' => 'Công ty Cổ phần Bearing & Fastener Supply',
                'business_license' => '2345678901',
                'tax_code' => '2345678901-004',
                'business_description' => 'Chuyên cung cấp ổ bi, bu lông, ốc vít, và các loại fastener chất lượng cao cho ngành cơ khí, ô tô, và hàng không.',
                'business_categories' => ['bearings', 'fasteners', 'automotive_parts'],
                'business_phone' => '+84-24-3456-7890',
                'business_email' => 'sales@bearingfastener.com.vn',
                'business_address' => '321 Phố Minh Khai, Hai Bà Trưng, Hà Nội',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'premium',
                'business_rating' => 4.3,
                'total_reviews' => 98,
                'points' => 2100,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Hà Nội, Việt Nam',
            ],
            [
                'name' => 'Hydraulic Systems Vietnam',
                'username' => 'hydraulic_vn',
                'email' => 'contact@hydraulicvn.com',
                'password' => Hash::make('Hydraulic2024@'),
                'role' => 'supplier',
                'avatar' => '/images/avatars/supplier5.jpg',
                'company_name' => 'Công ty TNHH Hydraulic Systems Vietnam',
                'business_license' => '3456789012',
                'tax_code' => '3456789012-005',
                'business_description' => 'Nhà cung cấp hệ thống thủy lực, khí nén, và các thiết bị tự động hóa cho ngành công nghiệp nặng.',
                'business_categories' => ['hydraulic_systems', 'pneumatic_systems', 'automation'],
                'business_phone' => '+84-251-456-7890',
                'business_email' => 'sales@hydraulicvn.com',
                'business_address' => 'KCN Biên Hòa 1, Đồng Nai',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'basic',
                'business_rating' => 4.1,
                'total_reviews' => 73,
                'points' => 1900,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Đồng Nai, Việt Nam',
            ]
        ];

        // ====================================================================
        // MANUFACTURERS - Nhà sản xuất
        // ====================================================================
        $manufacturers = [
            [
                'name' => 'Nhà máy Cơ khí Đông Á',
                'username' => 'dongamech',
                'email' => 'contact@dongamech.com',
                'password' => Hash::make('Manufacturer2024@'),
                'role' => 'manufacturer',
                'avatar' => '/images/avatars/manufacturer1.jpg',
                'company_name' => 'Công ty Cổ phần Cơ khí Đông Á',
                'business_license' => '1122334455',
                'tax_code' => '1122334455-003',
                'business_description' => 'Chuyên sản xuất linh kiện cơ khí chính xác, gia công CNC, đúc kim loại cho ngành ô tô và điện tử.',
                'business_categories' => ['cnc_machining', 'precision_parts', 'automotive'],
                'business_phone' => '+84-251-234-5678',
                'business_email' => 'production@dongamech.com',
                'business_address' => 'KCN Đồng Nai, Biên Hòa, Đồng Nai',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.8,
                'total_reviews' => 203,
                'points' => 4200,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Đồng Nai, Việt Nam',
            ],
            [
                'name' => 'Vietnam Precision Manufacturing',
                'username' => 'vn_precision',
                'email' => 'info@vnprecision.com',
                'password' => Hash::make('Precision2024@'),
                'role' => 'manufacturer',
                'avatar' => '/images/avatars/manufacturer2.jpg',
                'company_name' => 'Công ty TNHH Vietnam Precision Manufacturing',
                'business_license' => '4455667788',
                'tax_code' => '4455667788-006',
                'business_description' => 'Nhà sản xuất linh kiện chính xác cho ngành hàng không, y tế và năng lượng. Chứng nhận ISO 9001:2015 và AS9100.',
                'business_categories' => ['aerospace_parts', 'medical_devices', 'precision_manufacturing'],
                'business_phone' => '+84-28-5678-9012',
                'business_email' => 'manufacturing@vnprecision.com',
                'business_address' => 'KCN Tân Thuận, Quận 7, TP.HCM',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.9,
                'total_reviews' => 167,
                'points' => 4800,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
            ],
            [
                'name' => 'Hanoi Industrial Manufacturing',
                'username' => 'hanoi_industrial',
                'email' => 'contact@hanoiindustrial.vn',
                'password' => Hash::make('Industrial2024@'),
                'role' => 'manufacturer',
                'avatar' => '/images/avatars/manufacturer3.jpg',
                'company_name' => 'Công ty Cổ phần Hanoi Industrial Manufacturing',
                'business_license' => '5566778899',
                'tax_code' => '5566778899-007',
                'business_description' => 'Sản xuất máy móc công nghiệp, thiết bị tự động hóa, và hệ thống băng tải cho các nhà máy sản xuất.',
                'business_categories' => ['industrial_machinery', 'automation_equipment', 'conveyor_systems'],
                'business_phone' => '+84-24-6789-0123',
                'business_email' => 'production@hanoiindustrial.vn',
                'business_address' => 'KCN Thăng Long, Hà Nội',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'premium',
                'business_rating' => 4.6,
                'total_reviews' => 134,
                'points' => 3900,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Hà Nội, Việt Nam',
            ],
            [
                'name' => 'Mekong Delta Engineering Works',
                'username' => 'mekong_engineering',
                'email' => 'info@mekongworks.vn',
                'password' => Hash::make('Mekong2024@'),
                'role' => 'manufacturer',
                'avatar' => '/images/avatars/manufacturer4.jpg',
                'company_name' => 'Công ty TNHH Mekong Delta Engineering Works',
                'business_license' => '6677889900',
                'tax_code' => '6677889900-008',
                'business_description' => 'Chuyên sản xuất thiết bị nông nghiệp, máy móc chế biến thực phẩm và hệ thống tưới tiêu tự động.',
                'business_categories' => ['agricultural_machinery', 'food_processing', 'irrigation_systems'],
                'business_phone' => '+84-292-789-0123',
                'business_email' => 'manufacturing@mekongworks.vn',
                'business_address' => 'KCN Trà Nóc, Cần Thơ',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'basic',
                'business_rating' => 4.4,
                'total_reviews' => 89,
                'points' => 3100,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Cần Thơ, Việt Nam',
            ]
        ];

        // ====================================================================
        // BRANDS - Nhãn hàng/Thương hiệu
        // ====================================================================
        $brands = [
            [
                'name' => 'MechaTech Solutions Vietnam',
                'username' => 'mechatech_vn',
                'email' => 'vietnam@mechatech.global',
                'password' => Hash::make('Brand2024@'),
                'role' => 'brand',
                'avatar' => '/images/avatars/brand1.jpg',
                'company_name' => 'MechaTech Solutions Vietnam Co., Ltd',
                'business_license' => '9988776655',
                'tax_code' => '9988776655-005',
                'business_description' => 'Đại diện chính thức thương hiệu MechaTech tại Việt Nam. Chuyên phân phối thiết bị đo lường, kiểm tra chất lượng.',
                'business_categories' => ['measurement_tools', 'quality_control', 'testing_equipment'],
                'business_phone' => '+84-28-3456-7890',
                'business_email' => 'sales@mechatech.vn',
                'business_address' => 'Tầng 15, Tòa nhà Bitexco, Quận 1, TP.HCM',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.9,
                'total_reviews' => 312,
                'points' => 5500,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
            ],
            [
                'name' => 'Siemens Vietnam Representative',
                'username' => 'siemens_vietnam',
                'email' => 'vietnam@siemens.com',
                'password' => Hash::make('Siemens2024@'),
                'role' => 'brand',
                'avatar' => '/images/avatars/brand2.jpg',
                'company_name' => 'Siemens Vietnam Co., Ltd',
                'business_license' => '1357924680',
                'tax_code' => '1357924680-009',
                'business_description' => 'Đại diện chính thức Siemens tại Việt Nam. Cung cấp giải pháp tự động hóa công nghiệp, điều khiển và năng lượng.',
                'business_categories' => ['automation', 'industrial_control', 'energy_solutions'],
                'business_phone' => '+84-24-1357-9246',
                'business_email' => 'contact@siemens.vn',
                'business_address' => 'Tầng 12, Lotte Center, Cầu Giấy, Hà Nội',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.95,
                'total_reviews' => 487,
                'points' => 7200,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Hà Nội, Việt Nam',
            ],
            [
                'name' => 'Mitsubishi Electric Vietnam',
                'username' => 'mitsubishi_vn',
                'email' => 'vietnam@mitsubishielectric.com',
                'password' => Hash::make('Mitsubishi2024@'),
                'role' => 'brand',
                'avatar' => '/images/avatars/brand3.jpg',
                'company_name' => 'Mitsubishi Electric Vietnam Co., Ltd',
                'business_license' => '2468135790',
                'tax_code' => '2468135790-010',
                'business_description' => 'Thương hiệu hàng đầu về thiết bị điện công nghiệp, hệ thống tự động hóa và giải pháp Factory Automation.',
                'business_categories' => ['factory_automation', 'electrical_equipment', 'servo_systems'],
                'business_phone' => '+84-28-2468-1357',
                'business_email' => 'sales@mitsubishielectric.vn',
                'business_address' => 'Tầng 8, Saigon Trade Center, Quận 1, TP.HCM',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.8,
                'total_reviews' => 356,
                'points' => 6800,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
            ],
            [
                'name' => 'Schneider Electric Vietnam',
                'username' => 'schneider_vn',
                'email' => 'vietnam@schneider-electric.com',
                'password' => Hash::make('Schneider2024@'),
                'role' => 'brand',
                'avatar' => '/images/avatars/brand4.jpg',
                'company_name' => 'Schneider Electric Vietnam Co., Ltd',
                'business_license' => '3691470258',
                'tax_code' => '3691470258-011',
                'business_description' => 'Chuyên gia về quản lý năng lượng và tự động hóa. Cung cấp giải pháp toàn diện cho công nghiệp và tòa nhà thông minh.',
                'business_categories' => ['energy_management', 'building_automation', 'power_distribution'],
                'business_phone' => '+84-24-3691-4702',
                'business_email' => 'contact@schneider-electric.vn',
                'business_address' => 'Tầng 10, Vincom Center, Ba Đình, Hà Nội',
                'is_verified_business' => true,
                'business_verified_at' => now(),
                'subscription_level' => 'enterprise',
                'business_rating' => 4.7,
                'total_reviews' => 289,
                'points' => 6200,
                'email_verified_at' => now(),
                'status' => 'active',
                'location' => 'Hà Nội, Việt Nam',
            ]
        ];

            // Tạo users với error handling
        $createdSuppliers = 0;
        $createdManufacturers = 0;
        $createdBrands = 0;

        // Tạo Suppliers
        echo "🏪 Creating Suppliers...\n";
        foreach ($suppliers as $supplier) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $supplier['email']],
                    $supplier
                );
                echo "✅ Created supplier: {$user->name}\n";
                $createdSuppliers++;
            } catch (\Exception $e) {
                echo "❌ Failed to create supplier: {$supplier['name']} - " . $e->getMessage() . "\n";
            }
        }

        // Tạo Manufacturers
        echo "\n🏭 Creating Manufacturers...\n";
        foreach ($manufacturers as $manufacturer) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $manufacturer['email']],
                    $manufacturer
                );
                echo "✅ Created manufacturer: {$user->name}\n";
                $createdManufacturers++;
            } catch (\Exception $e) {
                echo "❌ Failed to create manufacturer: {$manufacturer['name']} - " . $e->getMessage() . "\n";
            }
        }

        // Tạo Brands
        echo "\n🏷️ Creating Brands...\n";
        foreach ($brands as $brand) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $brand['email']],
                    $brand
                );
                echo "✅ Created brand: {$user->name}\n";
                $createdBrands++;
            } catch (\Exception $e) {
                echo "❌ Failed to create brand: {$brand['name']} - " . $e->getMessage() . "\n";
            }
        }

        // Output results
        $this->command->info('✅ Business users created successfully!');
        $this->command->info('🏪 Created ' . $createdSuppliers . '/' . count($suppliers) . ' suppliers');
        $this->command->info('🏭 Created ' . $createdManufacturers . '/' . count($manufacturers) . ' manufacturers');
        $this->command->info('🏷️ Created ' . $createdBrands . '/' . count($brands) . ' brands');

        $totalCreated = $createdSuppliers + $createdManufacturers + $createdBrands;
        $totalExpected = count($suppliers) + count($manufacturers) + count($brands);
        $this->command->info('📊 Total business users: ' . $totalCreated . '/' . $totalExpected);
        $this->command->info('🖼️ Avatar URLs configured for /images/avatars/ directory');
        $this->command->info('🔐 All business accounts have verified status and subscription levels');
    }
}
