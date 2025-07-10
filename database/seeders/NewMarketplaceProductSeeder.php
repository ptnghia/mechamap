<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class NewMarketplaceProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create sample products for the new 3-type system with proper permissions
     */
    public function run(): void
    {
        echo "🌱 Creating new marketplace products with 3-type system...\n";

        // Get users by role
        $suppliers = User::where('role', 'supplier')->get();
        $manufacturers = User::where('role', 'manufacturer')->get();
        $brands = User::where('role', 'brand')->get();
        $members = User::where('role', 'member')->get();

        // Get categories
        $categories = ProductCategory::all();

        if ($categories->isEmpty()) {
            echo "❌ No product categories found. Please run ProductCategorySeeder first.\n";
            return;
        }

        $products = [];

        // 1. DIGITAL PRODUCTS (All business roles can sell these)
        $digitalProducts = [
            // Supplier digital products
            [
                'name' => 'Bộ bản vẽ CAD - Hộp giảm tốc bánh răng',
                'description' => 'Bộ bản vẽ CAD hoàn chỉnh cho hộp giảm tốc bánh răng, tỷ số truyền 1:10. Bao gồm file SolidWorks, AutoCAD và PDF.',
                'short_description' => 'Bản vẽ CAD hộp giảm tốc bánh răng tỷ số 1:10',
                'product_type' => MarketplaceProduct::TYPE_DIGITAL,
                'seller_type' => 'supplier',
                'price' => 150000,
                'file_formats' => ['dwg', 'sldprt', 'pdf'],
                'software_compatibility' => ['SolidWorks 2020+', 'AutoCAD 2019+'],
                'digital_files' => [
                    'gearbox_assembly.sldasm',
                    'gearbox_parts.zip',
                    'technical_drawing.pdf'
                ]
            ],
            [
                'name' => 'Thư viện linh kiện chuẩn ISO - Ốc vít và bu lông',
                'description' => 'Thư viện CAD đầy đủ các loại ốc vít, bu lông theo tiêu chuẩn ISO. Hơn 500 model 3D với kích thước chuẩn.',
                'short_description' => 'Thư viện CAD ốc vít bu lông chuẩn ISO',
                'product_type' => MarketplaceProduct::TYPE_DIGITAL,
                'seller_type' => 'supplier',
                'price' => 200000,
                'file_formats' => ['sldprt', 'step', 'iges'],
                'software_compatibility' => ['SolidWorks', 'Inventor', 'Fusion 360'],
            ],

            [
                'name' => 'Catalog sản phẩm bearing SKF - File CAD',
                'description' => 'Catalog đầy đủ các loại bearing SKF với file CAD 3D. Bao gồm thông số kỹ thuật và hướng dẫn lắp đặt.',
                'short_description' => 'Catalog bearing SKF với file CAD 3D',
                'product_type' => MarketplaceProduct::TYPE_DIGITAL,
                'seller_type' => 'supplier',
                'price' => 300000,
                'file_formats' => ['step', 'iges', 'pdf'],
                'software_compatibility' => ['Universal CAD format'],
            ],

            // Manufacturer digital products
            [
                'name' => 'Bản vẽ kỹ thuật máy phay CNC 3 trục',
                'description' => 'Bộ bản vẽ kỹ thuật hoàn chỉnh máy phay CNC 3 trục. Bao gồm assembly, part drawings và bill of materials.',
                'short_description' => 'Bản vẽ kỹ thuật máy phay CNC 3 trục',
                'product_type' => MarketplaceProduct::TYPE_DIGITAL,
                'seller_type' => 'manufacturer',
                'price' => 2000000,
                'file_formats' => ['dwg', 'sldasm', 'pdf', 'xlsx'],
                'software_compatibility' => ['SolidWorks 2021+', 'AutoCAD 2020+'],
            ]
        ];

        foreach ($digitalProducts as $productData) {
            $users = $this->getUsersBySellerType($productData['seller_type'], $suppliers, $manufacturers, $brands, $members);
            if ($users->isNotEmpty()) {
                $seller = $users->random();
                $this->createProduct($productData, $seller, $categories->random());
                $products[] = $productData['name'];
            }
        }

        echo "✅ Created " . count($digitalProducts) . " digital products\n";

        // 2. NEW PRODUCTS (Only suppliers can sell these)
        $newProducts = [
            [
                'name' => 'Bearing SKF 6205-2RS1 - Vòng bi bịt kín',
                'description' => 'Vòng bi SKF 6205-2RS1 chính hãng, bịt kín 2 phía. Kích thước: 25x52x15mm. Tải trọng động: 14kN.',
                'short_description' => 'Vòng bi SKF 6205-2RS1 chính hãng',
                'product_type' => MarketplaceProduct::TYPE_NEW_PRODUCT,
                'seller_type' => 'supplier',
                'price' => 85000,
                'stock_quantity' => 50,
                'material' => 'Thép chrome',
                'technical_specs' => [
                    'inner_diameter' => '25mm',
                    'outer_diameter' => '52mm',
                    'width' => '15mm',
                    'dynamic_load' => '14kN',
                    'static_load' => '6.8kN'
                ]
            ],
            [
                'name' => 'Motor servo Mitsubishi HC-KFS43 - 400W',
                'description' => 'Motor servo Mitsubishi HC-KFS43, công suất 400W, tốc độ 3000rpm. Bao gồm encoder tuyệt đối 17-bit.',
                'short_description' => 'Motor servo Mitsubishi HC-KFS43 400W',
                'product_type' => MarketplaceProduct::TYPE_NEW_PRODUCT,
                'seller_type' => 'supplier',
                'price' => 8500000,
                'stock_quantity' => 5,
                'material' => 'Hợp kim nhôm',
                'technical_specs' => [
                    'power' => '400W',
                    'speed' => '3000rpm',
                    'torque' => '1.27Nm',
                    'encoder' => '17-bit absolute',
                    'voltage' => '200V'
                ]
            ],
            [
                'name' => 'Thép tấm SS304 - 2mm x 1000mm x 2000mm',
                'description' => 'Thép không gỉ SS304 tấm phẳng, độ dày 2mm, kích thước 1000x2000mm. Bề mặt 2B finish.',
                'short_description' => 'Thép tấm SS304 2mm x 1000x2000mm',
                'product_type' => MarketplaceProduct::TYPE_NEW_PRODUCT,
                'seller_type' => 'supplier',
                'price' => 1200000,
                'stock_quantity' => 20,
                'material' => 'Thép không gỉ SS304',
                'technical_specs' => [
                    'thickness' => '2mm',
                    'width' => '1000mm',
                    'length' => '2000mm',
                    'surface' => '2B finish',
                    'grade' => 'SS304'
                ]
            ]
        ];

        foreach ($newProducts as $productData) {
            if ($suppliers->isNotEmpty()) {
                $seller = $suppliers->random();
                $this->createProduct($productData, $seller, $categories->random());
                $products[] = $productData['name'];
            }
        }

        echo "✅ Created " . count($newProducts) . " new products\n";

        // 3. USED PRODUCTS (Currently no one can sell these, but we'll create some for future)
        // Note: According to requirements, no role can sell used_product yet
        echo "ℹ️  Used products not created - no role has permission to sell them yet\n";

        echo "\n🎉 Total products created: " . count($products) . "\n";
        echo "📊 Product distribution:\n";
        echo "   - Digital products: " . count($digitalProducts) . " (all roles can sell)\n";
        echo "   - New products: " . count($newProducts) . " (only suppliers can sell)\n";
        echo "   - Used products: 0 (no role can sell yet)\n";
    }

    /**
     * Get users by seller type
     */
    private function getUsersBySellerType($sellerType, $suppliers, $manufacturers, $brands, $members)
    {
        switch ($sellerType) {
            case 'supplier':
                return $suppliers;
            case 'manufacturer':
                return $manufacturers;
            case 'brand':
                return $brands;
            case 'member':
                return $members;
            default:
                return collect();
        }
    }

    /**
     * Create a marketplace product
     */
    private function createProduct($data, $seller, $category)
    {
        MarketplaceProduct::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'short_description' => $data['short_description'],
            'sku' => 'MP-' . strtoupper(Str::random(8)),
            'seller_id' => $seller->id,
            'product_category_id' => $category->id,
            'product_type' => $data['product_type'],
            'seller_type' => $data['seller_type'],
            'price' => $data['price'],
            'stock_quantity' => $data['product_type'] === MarketplaceProduct::TYPE_DIGITAL ? 999 : ($data['stock_quantity'] ?? 10),
            'manage_stock' => $data['product_type'] !== MarketplaceProduct::TYPE_DIGITAL,
            'in_stock' => true,
            'file_formats' => $data['file_formats'] ?? null,
            'software_compatibility' => $data['software_compatibility'] ?? null,
            'digital_files' => $data['digital_files'] ?? null,
            'material' => $data['material'] ?? null,
            'manufacturing_process' => $data['manufacturing_process'] ?? null,
            'technical_specs' => $data['technical_specs'] ?? null,
            'status' => 'approved',
            'is_active' => true,
            'is_featured' => rand(0, 1) === 1,
            'approved_at' => now(),
            'approved_by' => 1, // Admin user
            'view_count' => rand(10, 500),
            'purchase_count' => rand(0, 50),
            'rating_average' => rand(35, 50) / 10, // 3.5 to 5.0
            'rating_count' => rand(5, 25),
        ]);
    }
}
