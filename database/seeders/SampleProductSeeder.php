<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class SampleProductSeeder extends Seeder
{
    /**
     * Seed sample products for MechaMap marketplace
     * Creates products for 3 business roles: Supplier, Manufacturer, Brand
     */
    public function run(): void
    {
        $this->command->info('🛒 Bắt đầu seed sample products cho marketplace...');

        // Get business users
        $suppliers = User::where('role', 'supplier')->get();
        $manufacturers = User::where('role', 'manufacturer')->get();
        $brands = User::where('role', 'brand')->get();
        
        // Get product categories
        $categories = ProductCategory::all();

        if ($suppliers->isEmpty() || $manufacturers->isEmpty() || $brands->isEmpty()) {
            $this->command->error('❌ Cần có business users trước!');
            return;
        }

        if ($categories->isEmpty()) {
            $this->command->error('❌ Cần có product categories trước!');
            return;
        }

        // Create products for each business role
        $supplierProducts = $this->createSupplierProducts($suppliers, $categories);
        $this->command->info('✅ Đã tạo ' . count($supplierProducts) . ' sản phẩm cho Suppliers');

        $manufacturerProducts = $this->createManufacturerProducts($manufacturers, $categories);
        $this->command->info('✅ Đã tạo ' . count($manufacturerProducts) . ' sản phẩm cho Manufacturers');

        $brandProducts = $this->createBrandProducts($brands, $categories);
        $this->command->info('✅ Đã tạo ' . count($brandProducts) . ' sản phẩm cho Brands');

        $this->command->info('🎉 Hoàn thành seed sample products!');
    }

    private function createSupplierProducts($suppliers, $categories): array
    {
        $products = [];
        
        $supplierProductData = [
            [
                'name' => 'Thép không gỉ 316L - Tấm 2mm',
                'description' => 'Tấm thép không gỉ 316L chất lượng cao, độ dày 2mm. Phù hợp cho ứng dụng trong công nghiệp thực phẩm, hóa chất và y tế. Chống ăn mòn tuyệt vời, dễ gia công và hàn.',
                'short_description' => 'Tấm thép không gỉ 316L 2mm chất lượng cao',
                'price' => 850000,
                'product_type' => 'physical',
                'material' => 'Stainless Steel 316L',
                'technical_specs' => [
                    'thickness' => '2mm',
                    'grade' => '316L',
                    'surface_finish' => '2B',
                    'tensile_strength' => '515-620 MPa',
                    'yield_strength' => '205 MPa min'
                ],
                'mechanical_properties' => [
                    'density' => '8.0 g/cm³',
                    'elastic_modulus' => '200 GPa',
                    'hardness' => '95 HRB max'
                ],
                'standards_compliance' => ['ASTM A240', 'JIS G4304', 'EN 10088-2'],
                'stock_quantity' => 50,
                'weight' => 15.7,
                'dimensions' => ['1000', '2000', '2']
            ],
            [
                'name' => 'Bearing SKF 6205-2RS1',
                'description' => 'Vòng bi bi cầu SKF 6205-2RS1 với seal cao su ở cả hai phía. Phù hợp cho ứng dụng tốc độ trung bình với yêu cầu bảo trì thấp.',
                'short_description' => 'Vòng bi SKF 6205-2RS1 sealed',
                'price' => 125000,
                'product_type' => 'physical',
                'material' => 'Chrome Steel',
                'technical_specs' => [
                    'inner_diameter' => '25mm',
                    'outer_diameter' => '52mm',
                    'width' => '15mm',
                    'dynamic_load_rating' => '14.0 kN',
                    'static_load_rating' => '6.95 kN'
                ],
                'standards_compliance' => ['ISO 15', 'DIN 625-1'],
                'stock_quantity' => 200,
                'weight' => 0.13
            ],
            [
                'name' => 'Máy khoan bàn 16mm BOSCH PBD 40',
                'description' => 'Máy khoan bàn chuyên nghiệp với khả năng khoan lỗ đến 16mm trên thép. Động cơ 710W mạnh mẽ, bàn làm việc có thể điều chỉnh góc nghiêng.',
                'short_description' => 'Máy khoan bàn BOSCH PBD 40 - 16mm',
                'price' => 4500000,
                'product_type' => 'physical',
                'technical_specs' => [
                    'max_drilling_capacity_steel' => '16mm',
                    'max_drilling_capacity_wood' => '40mm',
                    'motor_power' => '710W',
                    'spindle_speed' => '280-2350 rpm',
                    'table_size' => '405 x 405mm'
                ],
                'stock_quantity' => 5,
                'weight' => 28.5,
                'requires_shipping' => true
            ]
        ];

        foreach ($supplierProductData as $index => $productData) {
            $supplier = $suppliers->random();
            $category = $categories->random();

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . time() . '-' . $index,
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'seller_id' => $supplier->id,
                'seller_type' => 'supplier',
                'product_category_id' => $category->id,
                'product_type' => $productData['product_type'],
                'price' => $productData['price'],
                'sale_price' => $productData['price'] * 0.9, // 10% discount
                'is_on_sale' => rand(0, 1),
                'stock_quantity' => $productData['stock_quantity'],
                'material' => $productData['material'] ?? null,
                'technical_specs' => $productData['technical_specs'] ?? null,
                'mechanical_properties' => $productData['mechanical_properties'] ?? null,
                'standards_compliance' => $productData['standards_compliance'] ?? null,
                'weight' => $productData['weight'] ?? null,
                'dimensions' => $productData['dimensions'] ?? null,
                'requires_shipping' => $productData['requires_shipping'] ?? true,
                'status' => 'published',
                'is_approved' => true,
                'approved_at' => now(),
                'average_rating' => rand(40, 50) / 10, // 4.0-5.0
                'review_count' => rand(5, 25),
                'sales_count' => rand(10, 100),
                'view_count' => rand(50, 500),
                'is_featured' => rand(0, 1),
            ]);

            $products[] = $product;
        }

        return $products;
    }

    private function createManufacturerProducts($manufacturers, $categories): array
    {
        $products = [];
        
        $manufacturerProductData = [
            [
                'name' => 'Bản vẽ kỹ thuật Gearbox 5-speed',
                'description' => 'Bộ bản vẽ kỹ thuật hoàn chỉnh cho hộp số 5 cấp dành cho xe máy 150cc. Bao gồm assembly drawing, part drawings, BOM và quy trình gia công.',
                'short_description' => 'Bản vẽ kỹ thuật Gearbox 5-speed hoàn chỉnh',
                'price' => 2500000,
                'product_type' => 'technical_file',
                'file_formats' => ['DWG', 'PDF', 'STEP', 'IGES'],
                'software_compatibility' => 'AutoCAD 2020+, SolidWorks 2019+',
                'file_size_mb' => 45.2,
                'download_limit' => 3,
                'technical_specs' => [
                    'gear_ratio' => '1st: 3.08, 2nd: 1.94, 3rd: 1.35, 4th: 1.04, 5th: 0.85',
                    'input_torque' => '12 Nm max',
                    'material_specification' => 'SCM420, S45C',
                    'tolerance_class' => 'IT7/IT8'
                ]
            ],
            [
                'name' => 'CAD Model - Turbocharger Assembly',
                'description' => 'Mô hình CAD 3D chi tiết của turbocharger cho động cơ diesel 2.0L. Bao gồm compressor wheel, turbine wheel, housing và bearing system.',
                'short_description' => 'CAD Model Turbocharger 3D hoàn chỉnh',
                'price' => 1800000,
                'product_type' => 'digital',
                'file_formats' => ['SLDPRT', 'STEP', 'IGES', 'STL'],
                'software_compatibility' => 'SolidWorks, CATIA, Inventor',
                'file_size_mb' => 28.7,
                'download_limit' => 5
            ],
            [
                'name' => 'Manufacturing Process - CNC Machining Guide',
                'description' => 'Hướng dẫn quy trình gia công CNC chi tiết cho các part phức tạp. Bao gồm setup, toolpath, cutting parameters và quality control.',
                'short_description' => 'Quy trình gia công CNC chuyên nghiệp',
                'price' => 950000,
                'product_type' => 'service',
                'file_formats' => ['PDF', 'MP4', 'DOCX'],
                'file_size_mb' => 156.3
            ]
        ];

        foreach ($manufacturerProductData as $index => $productData) {
            $manufacturer = $manufacturers->random();
            $category = $categories->random();

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . time() . '-' . $index,
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'seller_id' => $manufacturer->id,
                'seller_type' => 'manufacturer',
                'product_category_id' => $category->id,
                'product_type' => $productData['product_type'],
                'price' => $productData['price'],
                'file_formats' => $productData['file_formats'] ?? null,
                'software_compatibility' => $productData['software_compatibility'] ?? null,
                'file_size_mb' => $productData['file_size_mb'] ?? null,
                'download_limit' => $productData['download_limit'] ?? null,
                'technical_specs' => $productData['technical_specs'] ?? null,
                'is_digital_download' => true,
                'requires_shipping' => false,
                'manage_stock' => false,
                'status' => 'published',
                'is_approved' => true,
                'approved_at' => now(),
                'average_rating' => rand(42, 50) / 10, // 4.2-5.0
                'review_count' => rand(8, 30),
                'sales_count' => rand(15, 80),
                'view_count' => rand(100, 800),
                'is_featured' => rand(0, 1),
            ]);

            $products[] = $product;
        }

        return $products;
    }

    private function createBrandProducts($brands, $categories): array
    {
        $products = [];
        
        $brandProductData = [
            [
                'name' => 'HONDA Genuine Parts Catalog 2024',
                'description' => 'Catalog chính hãng của Honda với đầy đủ thông tin về phụ tùng, mã part number và giá bán lẻ khuyến nghị. Phiên bản 2024 mới nhất.',
                'short_description' => 'Catalog phụ tùng Honda chính hãng 2024',
                'price' => 0, // Free promotional material
                'product_type' => 'digital'
            ],
            [
                'name' => 'YAMAHA Technical Training Materials',
                'description' => 'Tài liệu đào tạo kỹ thuật chính thức từ Yamaha Motor. Bao gồm engine technology, maintenance procedures và troubleshooting guide.',
                'short_description' => 'Tài liệu đào tạo kỹ thuật Yamaha',
                'price' => 500000,
                'product_type' => 'service'
            ]
        ];

        foreach ($brandProductData as $index => $productData) {
            $brand = $brands->random();
            $category = $categories->random();

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . time() . '-' . $index,
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'seller_id' => $brand->id,
                'seller_type' => 'brand',
                'product_category_id' => $category->id,
                'product_type' => $productData['product_type'],
                'price' => $productData['price'],
                'is_digital_download' => true,
                'requires_shipping' => false,
                'manage_stock' => false,
                'status' => 'published',
                'is_approved' => true,
                'approved_at' => now(),
                'average_rating' => rand(45, 50) / 10, // 4.5-5.0 (brands usually have high ratings)
                'review_count' => rand(20, 100),
                'sales_count' => rand(50, 200),
                'view_count' => rand(200, 1000),
                'is_featured' => true, // Brand products are usually featured
            ]);

            $products[] = $product;
        }

        return $products;
    }
}
