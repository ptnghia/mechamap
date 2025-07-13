<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\MarketplaceSeller;
use Carbon\Carbon;

class ProductDataStandardizationSeeder extends Seeder
{
    /**
     * 🔧 MechaMap Product Data Standardization Seeder
     *
     * Chuẩn hóa dữ liệu marketplace products theo yêu cầu:
     * - 30% Digital Products (guest, supplier, manufacturer)
     * - 40% New Physical Products (supplier, manufacturer only)
     * - 30% Used Physical Products (supplier, manufacturer only)
     * - Pricing VNĐ, không vượt quá 1,000,000 VNĐ
     * - Business rules theo seller permissions
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu chuẩn hóa dữ liệu marketplace products...');

        // Disable observers để tránh lỗi
        MarketplaceProduct::unsetEventDispatcher();

        // Backup trước khi thực hiện
        $this->createBackup();

        // Phân tích hiện trạng
        $this->analyzeCurrentState();

        // Chuẩn hóa dữ liệu
        $this->validateSellers();
        $this->standardizeDigitalProducts();
        $this->standardizePhysicalProducts();
        $this->addImages();
        $this->updatePricing();

        $this->command->info('✅ Hoàn thành chuẩn hóa dữ liệu marketplace products!');
    }

    /**
     * Tạo backup trước khi chuẩn hóa
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup dữ liệu products...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/products_backup_{$timestamp}.json");

        // Tạo thư mục backup nếu chưa có
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        // Export products data as JSON
        $products = MarketplaceProduct::all()->toArray();
        file_put_contents($backupFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->command->info("✅ Backup tạo tại: {$backupFile}");
        $this->command->info("📊 Đã backup " . count($products) . " products");
    }

    /**
     * Phân tích hiện trạng dữ liệu
     */
    private function analyzeCurrentState(): void
    {
        $this->command->info('📊 Phân tích hiện trạng products...');

        $totalProducts = MarketplaceProduct::count();
        $this->command->info("Tổng số products: {$totalProducts}");

        // Phân bố theo product_type
        $typeStats = MarketplaceProduct::select('product_type', DB::raw('count(*) as count'))
            ->groupBy('product_type')
            ->get();

        $this->command->info('Phân bố theo product_type:');
        foreach ($typeStats as $stat) {
            $this->command->info("  {$stat->product_type}: {$stat->count} products");
        }

        // Phân bố theo seller_type
        $sellerStats = MarketplaceProduct::select('seller_type', DB::raw('count(*) as count'))
            ->groupBy('seller_type')
            ->get();

        $this->command->info('Phân bố theo seller_type:');
        foreach ($sellerStats as $stat) {
            $this->command->info("  {$stat->seller_type}: {$stat->count} products");
        }

        // Kiểm tra pricing
        $avgPrice = MarketplaceProduct::avg('price');
        $maxPrice = MarketplaceProduct::max('price');
        $minPrice = MarketplaceProduct::min('price');

        $this->command->info('Thống kê giá:');
        $this->command->info("  Giá trung bình: " . number_format($avgPrice, 0) . " VNĐ");
        $this->command->info("  Giá cao nhất: " . number_format($maxPrice, 0) . " VNĐ");
        $this->command->info("  Giá thấp nhất: " . number_format($minPrice, 0) . " VNĐ");

        // Kiểm tra nội dung
        $emptyName = MarketplaceProduct::where('name', '')->orWhereNull('name')->count();
        $emptyDescription = MarketplaceProduct::where('description', '')->orWhereNull('description')->count();
        $noFeaturedImage = MarketplaceProduct::whereNull('featured_image')->count();

        $this->command->info('Vấn đề nội dung:');
        $this->command->info("  Thiếu name: {$emptyName}");
        $this->command->info("  Thiếu description: {$emptyDescription}");
        $this->command->info("  Không có featured_image: {$noFeaturedImage}");
    }

    /**
     * Validate và fix seller permissions
     */
    private function validateSellers(): void
    {
        $this->command->info('👤 Kiểm tra và sửa seller permissions...');

        $products = MarketplaceProduct::all();
        $updated = 0;

        foreach ($products as $product) {
            $seller = $product->seller;
            if (!$seller || !$seller->user) {
                // Tìm seller phù hợp dựa trên product_type
                $validSeller = $this->findValidSeller($product->product_type);
                if ($validSeller) {
                    $product->update(['seller_id' => $validSeller->id]);
                    $updated++;
                    $this->command->info("✅ Cập nhật seller cho product ID {$product->id}");
                }
                continue;
            }

            $userRole = $seller->user->role;

            // Kiểm tra business rules
            $canSell = $this->canUserSellProductType($userRole, $product->product_type);

            if (!$canSell) {
                // Tìm seller phù hợp
                $validSeller = $this->findValidSeller($product->product_type);
                if ($validSeller) {
                    $product->update(['seller_id' => $validSeller->id]);
                    $updated++;
                    $this->command->info("✅ Chuyển product ID {$product->id} sang seller phù hợp");
                }
            }
        }

        $this->command->info("👤 Đã cập nhật seller cho {$updated} products");
    }

    /**
     * Kiểm tra user có thể bán loại product này không
     */
    private function canUserSellProductType(string $userRole, string $productType): bool
    {
        $permissions = [
            'digital' => ['guest', 'supplier', 'manufacturer'],
            'physical' => ['supplier', 'manufacturer'],
        ];

        $allowedRoles = $permissions[$productType] ?? [];
        return in_array($userRole, $allowedRoles);
    }

    /**
     * Tìm seller hợp lệ cho product type
     */
    private function findValidSeller(string $productType): ?MarketplaceSeller
    {
        $allowedRoles = [];

        switch ($productType) {
            case 'digital':
                $allowedRoles = ['guest', 'supplier', 'manufacturer'];
                break;
            case 'physical':
                $allowedRoles = ['supplier', 'manufacturer'];
                break;
        }

        return MarketplaceSeller::whereHas('user', function($query) use ($allowedRoles) {
            $query->whereIn('role', $allowedRoles);
        })->first();
    }

    /**
     * Chuẩn hóa Digital Products (30%)
     */
    private function standardizeDigitalProducts(): void
    {
        $this->command->info('💾 Chuẩn hóa Digital Products...');

        $digitalProducts = MarketplaceProduct::where('product_type', 'digital')->get();

        $digitalProductNames = [
            'File CAD thiết kế máy ép thủy lực',
            'Bản vẽ kỹ thuật hệ thống băng tải',
            'Phần mềm tính toán bearing',
            'Template Excel tính toán gear',
            'File SolidWorks robot 6 trục',
            'Bản vẽ AutoCAD jig fixture',
            'PDF hướng dẫn lập trình CNC',
            'File ANSYS phân tích FEA',
            'Template PowerPoint báo cáo kỹ thuật',
            'Spreadsheet tính toán độ bền vật liệu'
        ];

        $digitalDescriptions = [
            'File CAD chất lượng cao với đầy đủ thông số kỹ thuật và bản vẽ chi tiết. Phù hợp cho nghiên cứu và ứng dụng thực tế.',
            'Tài liệu kỹ thuật chuyên nghiệp với hướng dẫn chi tiết và ví dụ minh họa. Tiết kiệm thời gian thiết kế và tính toán.',
            'Phần mềm/tool hỗ trợ tính toán nhanh chóng và chính xác. Giao diện thân thiện, dễ sử dụng cho kỹ sư.',
            'Template chuẩn công nghiệp với các công thức và macro được tối ưu hóa. Tương thích với phần mềm phổ biến.',
            'Bộ file đầy đủ bao gồm model 3D, bản vẽ 2D và tài liệu hướng dẫn. Chất lượng cao, chi tiết rõ ràng.'
        ];

        $updated = 0;
        foreach ($digitalProducts as $product) {
            $updates = [];

            if (empty($product->name) || strlen($product->name) < 10) {
                $updates['name'] = $digitalProductNames[array_rand($digitalProductNames)];
            }

            if (empty($product->description) || strlen($product->description) < 50) {
                $updates['description'] = $digitalDescriptions[array_rand($digitalDescriptions)];
            }

            // Cập nhật digital-specific fields
            $updates['file_formats'] = ['dwg', 'sldprt', 'pdf', 'xlsx'];
            $updates['software_compatibility'] = ['AutoCAD', 'SolidWorks', 'Excel'];
            $updates['file_size_mb'] = rand(1, 50);
            $updates['download_limit'] = rand(3, 10);

            // Pricing cho digital products: 5,000-50,000 VNĐ
            if ($product->price < 5000 || $product->price > 50000) {
                $updates['price'] = rand(5000, 50000);
            }

            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật digital product ID {$product->id}");
            }
        }

        $this->command->info("💾 Đã cập nhật {$updated} digital products");
    }

    /**
     * Chuẩn hóa Physical Products (New + Used)
     */
    private function standardizePhysicalProducts(): void
    {
        $this->command->info('📦 Chuẩn hóa Physical Products...');

        $physicalProducts = MarketplaceProduct::where('product_type', 'physical')->get();

        $newProductNames = [
            'Bearing SKF 6205-2RS mới',
            'Motor servo Panasonic 1kW',
            'Xy lanh khí nén SMC 50mm',
            'Cảm biến áp suất Omron',
            'Van điện từ 24VDC',
            'Encoder quay Autonics',
            'Relay công nghiệp Schneider',
            'Contactor 3 pha ABB',
            'Biến tần Mitsubishi 2.2kW',
            'PLC Siemens S7-1200'
        ];

        $usedProductNames = [
            'Máy phay CNC cũ Haas VF-2',
            'Máy tiện CNC đã qua sử dụng',
            'Máy hàn MIG/MAG cũ Lincoln',
            'Máy nén khí Atlas Copco cũ',
            'Cần cẩu 5 tấn đã qua sử dụng',
            'Máy cắt laser CO2 cũ',
            'Máy ép thủy lực 100 tấn cũ',
            'Máy mài tròn cũ Okamoto',
            'Máy khoan bàn cũ',
            'Tủ điện công nghiệp đã qua sử dụng'
        ];

        $physicalDescriptions = [
            'Sản phẩm chính hãng với đầy đủ giấy tờ bảo hành. Chất lượng cao, độ bền vượt trội, phù hợp cho ứng dụng công nghiệp.',
            'Thiết bị đã qua kiểm tra chất lượng nghiêm ngặt. Tình trạng tốt, vận hành ổn định, giá cả hợp lý.',
            'Máy móc chuyên dụng cho ngành cơ khí. Bảo trì định kỳ, vận hành êm ái, hiệu suất cao.',
            'Linh kiện thay thế chính hãng với thông số kỹ thuật chuẩn. Tương thích với nhiều loại máy móc.',
            'Thiết bị công nghiệp chất lượng cao. Đã qua sử dụng nhưng vẫn hoạt động tốt, giá thành hợp lý.'
        ];

        $updated = 0;
        foreach ($physicalProducts as $index => $product) {
            $updates = [];

            // Xác định là new hay used (60% new, 40% used)
            $isUsed = $index % 5 < 2; // 40% used

            if (empty($product->name) || strlen($product->name) < 10) {
                $updates['name'] = $isUsed ?
                    $usedProductNames[array_rand($usedProductNames)] :
                    $newProductNames[array_rand($newProductNames)];
            }

            if (empty($product->description) || strlen($product->description) < 50) {
                $updates['description'] = $physicalDescriptions[array_rand($physicalDescriptions)];
            }

            // Cập nhật physical-specific fields
            $updates['manage_stock'] = true;
            $updates['stock_quantity'] = rand(1, 20);
            $updates['in_stock'] = true;

            if ($isUsed) {
                $updates['name'] = str_replace('mới', 'cũ', $updates['name'] ?? $product->name);
                $updates['description'] = 'Thiết bị đã qua sử dụng. ' . ($updates['description'] ?? $product->description);
                // Used products: 5,000-300,000 VNĐ
                if ($product->price < 5000 || $product->price > 300000) {
                    $updates['price'] = rand(5000, 300000);
                }
            } else {
                // New products: 10,000-500,000 VNĐ
                if ($product->price < 10000 || $product->price > 500000) {
                    $updates['price'] = rand(10000, 500000);
                }
            }

            // Technical specs
            $updates['technical_specs'] = [
                'weight' => rand(1, 100) . ' kg',
                'dimensions' => rand(100, 1000) . 'x' . rand(100, 1000) . 'x' . rand(100, 1000) . ' mm',
                'power' => rand(1, 10) . ' kW',
                'voltage' => ['220V', '380V', '24V'][array_rand(['220V', '380V', '24V'])],
                'material' => ['Steel', 'Aluminum', 'Cast Iron'][array_rand(['Steel', 'Aluminum', 'Cast Iron'])]
            ];

            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật physical product ID {$product->id}");
            }
        }

        $this->command->info("📦 Đã cập nhật {$updated} physical products");
    }

    /**
     * Thêm hình ảnh cho products
     */
    private function addImages(): void
    {
        $this->command->info('🖼️ Thêm hình ảnh cho products...');

        $availableImages = [
            '/images/products/product-1.jpg',
            '/images/products/product-2.jpg',
            '/images/products/product-3.jpg',
            '/images/products/product-4.jpg',
            '/images/products/product-5.jpg',
            '/images/marketplace/digital-1.jpg',
            '/images/marketplace/digital-2.jpg',
            '/images/marketplace/physical-1.jpg',
            '/images/marketplace/physical-2.jpg',
            '/images/marketplace/used-1.jpg',
            '/images/demo/product-1.jpg',
            '/images/demo/product-2.jpg',
            '/images/demo/product-3.jpg'
        ];

        $productsWithoutImages = MarketplaceProduct::whereNull('featured_image')->get();
        $updated = 0;

        foreach ($productsWithoutImages as $product) {
            $randomImage = $availableImages[array_rand($availableImages)];

            // Tạo gallery với 2-4 hình ảnh
            $galleryCount = rand(2, 4);
            $gallery = [];
            for ($i = 0; $i < $galleryCount; $i++) {
                $gallery[] = $availableImages[array_rand($availableImages)];
            }

            $product->update([
                'featured_image' => $randomImage,
                'images' => $gallery
            ]);

            $updated++;
            $this->command->info("✅ Thêm hình ảnh cho product ID {$product->id}");
        }

        $this->command->info("🖼️ Đã thêm hình ảnh cho {$updated} products");
    }

    /**
     * Cập nhật pricing theo VNĐ
     */
    private function updatePricing(): void
    {
        $this->command->info('💰 Cập nhật pricing theo VNĐ...');

        $products = MarketplaceProduct::all();
        $updated = 0;

        foreach ($products as $product) {
            $updates = [];

            // Đảm bảo giá không vượt quá 1,000,000 VNĐ
            if ($product->price > 1000000) {
                $updates['price'] = rand(50000, 1000000);
            }

            // Cập nhật status và visibility
            $updates['status'] = ['approved', 'approved', 'approved', 'pending'][array_rand(['approved', 'approved', 'approved', 'pending'])]; // 75% approved
            $updates['is_active'] = true;
            $updates['is_featured'] = rand(0, 9) < 2; // 20% featured

            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
            }
        }

        $this->command->info("💰 Đã cập nhật pricing cho {$updated} products");
    }
}
