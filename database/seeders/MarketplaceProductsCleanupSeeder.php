<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\MarketplaceSeller;
use Illuminate\Support\Str;

class MarketplaceProductsCleanupSeeder extends Seeder
{
    /**
     * 🔧 MechaMap Marketplace Products Cleanup & Standardization
     * 
     * Làm sạch và chuẩn hóa tất cả products:
     * - Loại bỏ tiêu đề "test"
     * - Bổ sung mô tả đầy đủ
     * - Đảm bảo hình ảnh chất lượng
     * - Nội dung chuyên nghiệp bám sát cơ khí
     */
    public function run(): void
    {
        $this->command->info('🧹 Bắt đầu làm sạch và chuẩn hóa marketplace products...');
        
        // Disable observers để tránh lỗi
        MarketplaceProduct::unsetEventDispatcher();
        
        // Backup trước khi thực hiện
        $this->createBackup();
        
        // Phân tích và báo cáo hiện trạng
        $this->analyzeCurrentIssues();
        
        // Thực hiện cleanup
        $this->cleanupTestProducts();
        $this->standardizeDigitalProducts();
        $this->standardizePhysicalProducts();
        $this->addMissingDescriptions();
        $this->ensureQualityImages();
        
        $this->command->info('✅ Hoàn thành làm sạch và chuẩn hóa marketplace products!');
    }

    /**
     * Tạo backup trước khi cleanup
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup trước khi cleanup...');
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/marketplace_cleanup_backup_{$timestamp}.json");
        
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }
        
        $products = MarketplaceProduct::all()->toArray();
        file_put_contents($backupFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->command->info("✅ Backup tạo tại: {$backupFile}");
        $this->command->info("📊 Đã backup " . count($products) . " products");
    }

    /**
     * Phân tích các vấn đề hiện tại
     */
    private function analyzeCurrentIssues(): void
    {
        $this->command->info('🔍 Phân tích các vấn đề hiện tại...');
        
        $testProducts = MarketplaceProduct::where('name', 'like', '%test%')
            ->orWhere('name', 'like', '%Test%')
            ->orWhere('name', 'like', '%TEST%')
            ->count();
        
        $missingDesc = MarketplaceProduct::where(function($q) {
            $q->whereNull('description')
              ->orWhere('description', '')
              ->orWhereRaw('LENGTH(description) < 50');
        })->count();
        
        $missingImages = MarketplaceProduct::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->count();
        
        $this->command->info("🔍 Vấn đề phát hiện:");
        $this->command->info("  - Products có tiêu đề 'test': {$testProducts}");
        $this->command->info("  - Products thiếu mô tả: {$missingDesc}");
        $this->command->info("  - Products thiếu hình ảnh: {$missingImages}");
    }

    /**
     * Làm sạch products có tiêu đề test
     */
    private function cleanupTestProducts(): void
    {
        $this->command->info('🧹 Làm sạch products có tiêu đề test...');
        
        $testProducts = MarketplaceProduct::where('name', 'like', '%test%')
            ->orWhere('name', 'like', '%Test%')
            ->orWhere('name', 'like', '%TEST%')
            ->get();
        
        $professionalNames = [
            // Digital Products
            'File CAD thiết kế máy ép thủy lực 100 tấn',
            'Bản vẽ kỹ thuật hệ thống băng tải tự động',
            'Phần mềm tính toán bearing và ổ trục',
            'Template Excel tính toán gear và bánh răng',
            'File SolidWorks robot công nghiệp 6 trục',
            'Bản vẽ AutoCAD jig fixture gia công CNC',
            'PDF hướng dẫn lập trình CNC Fanuc',
            'File ANSYS phân tích FEA khung thép',
            'Template PowerPoint báo cáo kỹ thuật',
            'Spreadsheet tính toán độ bền vật liệu',
            
            // Physical Products - New
            'Bearing SKF 6205-2RS chính hãng',
            'Motor servo Panasonic MINAS A6 1kW',
            'Xy lanh khí nén SMC CDQ2B50-100',
            'Cảm biến áp suất Omron E8F2-A10C',
            'Van điện từ 2/2 24VDC Burkert',
            'Encoder quay Autonics E40S6-1024',
            'Relay công nghiệp Schneider RXM4AB2P7',
            'Contactor 3 pha ABB A75-30-11',
            'Biến tần Mitsubishi FR-E720-2.2K',
            'PLC Siemens S7-1200 CPU 1214C',
            'Động cơ giảm tốc SEW R37DT80K4',
            'Cảm biến laser Keyence LV-N11P',
            'Van bi inox 316 DN50 PN16',
            'Máy bơm ly tâm Grundfos CR3-8',
            'Khớp nối trục KTR ROTEX GS28',
            
            // Physical Products - Used
            'Máy phay CNC cũ Haas VF-2SS đã qua sử dụng',
            'Máy tiện CNC Mazak QT-15N tình trạng tốt',
            'Máy hàn MIG/MAG Lincoln Power Wave 455M cũ',
            'Máy nén khí Atlas Copco GA15 đã qua sử dụng',
            'Cần cẩu 5 tấn Kito ER2 tình trạng hoạt động tốt',
            'Máy cắt laser CO2 Trumpf TruLaser 3030 cũ',
            'Máy ép thủy lực 100 tấn Schuler đã qua sử dụng',
            'Máy mài tròn Okamoto ACC-84DX tình trạng tốt',
            'Máy khoan bàn Alzmetall AB-4/SV cũ',
            'Tủ điện công nghiệp Schneider đã qua sử dụng'
        ];
        
        $updated = 0;
        foreach ($testProducts as $index => $product) {
            $newName = $professionalNames[$index % count($professionalNames)];
            
            // Đảm bảo tên không trùng lặp
            $counter = 1;
            $originalName = $newName;
            while (MarketplaceProduct::where('name', $newName)->where('id', '!=', $product->id)->exists()) {
                $newName = $originalName . " - Phiên bản {$counter}";
                $counter++;
            }
            
            $product->update([
                'name' => $newName,
                'slug' => Str::slug($newName)
            ]);
            
            $updated++;
            $this->command->info("✅ Cập nhật product ID {$product->id}: {$newName}");
        }
        
        $this->command->info("🧹 Đã làm sạch {$updated} products có tiêu đề test");
    }

    /**
     * Chuẩn hóa Digital Products
     */
    private function standardizeDigitalProducts(): void
    {
        $this->command->info('💾 Chuẩn hóa Digital Products...');
        
        $digitalProducts = MarketplaceProduct::where('product_type', 'digital')->get();
        
        $digitalDescriptions = [
            'File CAD chất lượng cao được thiết kế bởi kỹ sư có kinh nghiệm. Bao gồm đầy đủ thông số kỹ thuật, bản vẽ chi tiết và hướng dẫn sử dụng. Phù hợp cho nghiên cứu, học tập và ứng dụng thực tế trong ngành cơ khí.',
            'Tài liệu kỹ thuật chuyên nghiệp với hướng dẫn chi tiết từng bước. Được biên soạn bởi chuyên gia có nhiều năm kinh nghiệm trong lĩnh vực. Tiết kiệm thời gian thiết kế và đảm bảo độ chính xác cao.',
            'Phần mềm/tool hỗ trợ tính toán nhanh chóng và chính xác các thông số kỹ thuật. Giao diện thân thiện, dễ sử dụng cho kỹ sư ở mọi trình độ. Tích hợp các công thức chuẩn quốc tế.',
            'Template chuẩn công nghiệp với các công thức và macro được tối ưu hóa. Tương thích với phần mềm phổ biến như Excel, MATLAB. Giúp tăng hiệu quả công việc và giảm thiểu sai sót.',
            'Bộ file đầy đủ bao gồm model 3D, bản vẽ 2D và tài liệu hướng dẫn. Chất lượng cao, chi tiết rõ ràng, tuân thủ tiêu chuẩn kỹ thuật. Phù hợp cho cả mục đích học tập và sản xuất thực tế.'
        ];
        
        $updated = 0;
        foreach ($digitalProducts as $product) {
            $updates = [];
            
            // Cập nhật mô tả nếu thiếu hoặc quá ngắn
            if (empty($product->description) || strlen($product->description) < 50) {
                $updates['description'] = $digitalDescriptions[array_rand($digitalDescriptions)];
            }
            
            // Cập nhật short_description
            if (empty($product->short_description)) {
                $updates['short_description'] = 'File kỹ thuật chất lượng cao cho ngành cơ khí. Bao gồm đầy đủ tài liệu và hướng dẫn sử dụng.';
            }
            
            // Cập nhật technical specs
            if (empty($product->file_formats)) {
                $updates['file_formats'] = ['dwg', 'sldprt', 'pdf', 'xlsx', 'step'];
            }
            
            if (empty($product->software_compatibility)) {
                $updates['software_compatibility'] = ['AutoCAD', 'SolidWorks', 'Fusion 360', 'Inventor'];
            }
            
            if (!$product->file_size_mb || $product->file_size_mb == 0) {
                $updates['file_size_mb'] = rand(5, 100);
            }
            
            if (!$product->download_limit || $product->download_limit == 0) {
                $updates['download_limit'] = rand(5, 20);
            }
            
            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật digital product ID {$product->id}");
            }
        }
        
        $this->command->info("💾 Đã chuẩn hóa {$updated} digital products");
    }

    /**
     * Chuẩn hóa Physical Products
     */
    private function standardizePhysicalProducts(): void
    {
        $this->command->info('📦 Chuẩn hóa Physical Products...');
        
        $physicalProducts = MarketplaceProduct::whereIn('product_type', ['new_product', 'used_product', 'physical'])->get();
        
        $newProductDescriptions = [
            'Sản phẩm chính hãng mới 100% với đầy đủ giấy tờ bảo hành từ nhà sản xuất. Chất lượng cao, độ bền vượt trội, được kiểm tra nghiêm ngặt trước khi xuất xưởng. Phù hợp cho ứng dụng công nghiệp và dân dụng.',
            'Thiết bị công nghiệp chất lượng cao được nhập khẩu trực tiếp từ nhà sản xuất uy tín. Tuân thủ các tiêu chuẩn quốc tế về chất lượng và an toàn. Bảo hành chính hãng và hỗ trợ kỹ thuật 24/7.',
            'Linh kiện thay thế chính hãng với thông số kỹ thuật chuẩn theo tiêu chuẩn quốc tế. Tương thích với nhiều loại máy móc và thiết bị. Được kiểm tra chất lượng nghiêm ngặt trước khi giao hàng.',
            'Sản phẩm mới với công nghệ tiên tiến, hiệu suất cao và tiết kiệm năng lượng. Thiết kế compact, dễ lắp đặt và bảo trì. Phù hợp cho các ứng dụng đòi hỏi độ chính xác và độ tin cậy cao.',
            'Thiết bị chuyên dụng cho ngành cơ khí với chất lượng vượt trội. Được sản xuất theo quy trình nghiêm ngặt, đảm bảo độ bền và hiệu suất ổn định trong môi trường công nghiệp khắc nghiệt.'
        ];
        
        $usedProductDescriptions = [
            'Thiết bị đã qua sử dụng nhưng vẫn hoạt động tốt, được kiểm tra và bảo trì định kỳ. Tình trạng từ 80-90%, vận hành ổn định. Giá cả hợp lý, phù hợp cho các doanh nghiệp vừa và nhỏ.',
            'Máy móc cũ chất lượng cao đã qua kiểm tra kỹ thuật toàn diện. Các bộ phận chính vẫn hoạt động tốt, đã thay thế các linh kiện hao mòn. Bảo hành 6 tháng và hỗ trợ kỹ thuật.',
            'Thiết bị đã qua sử dụng từ các nhà máy uy tín, được bảo trì định kỳ theo đúng quy trình. Tình trạng tốt, vận hành êm ái. Giá thành hợp lý, tiết kiệm chi phí đầu tư ban đầu.',
            'Máy móc cũ nhập khẩu từ các nước phát triển, chất lượng cao và độ bền vượt trội. Đã qua kiểm định an toàn và hiệu suất. Phù hợp cho các ứng dụng không đòi hỏi độ chính xác tuyệt đối.',
            'Thiết bị đã qua sử dụng với lịch sử bảo trì rõ ràng. Các thông số kỹ thuật vẫn đạt yêu cầu, hoạt động ổn định. Được kiểm tra và làm sạch trước khi bàn giao cho khách hàng.'
        ];
        
        $updated = 0;
        foreach ($physicalProducts as $product) {
            $updates = [];
            
            $isUsed = $product->product_type === 'used_product' || 
                     strpos(strtolower($product->name), 'cũ') !== false ||
                     strpos(strtolower($product->name), 'đã qua sử dụng') !== false;
            
            // Cập nhật mô tả
            if (empty($product->description) || strlen($product->description) < 50) {
                $updates['description'] = $isUsed ? 
                    $usedProductDescriptions[array_rand($usedProductDescriptions)] :
                    $newProductDescriptions[array_rand($newProductDescriptions)];
            }
            
            // Cập nhật short_description
            if (empty($product->short_description)) {
                $updates['short_description'] = $isUsed ?
                    'Thiết bị đã qua sử dụng, tình trạng tốt, giá cả hợp lý.' :
                    'Sản phẩm chính hãng mới 100%, chất lượng cao, bảo hành đầy đủ.';
            }
            
            // Cập nhật technical specs
            if (empty($product->technical_specs)) {
                $updates['technical_specs'] = [
                    'weight' => rand(1, 500) . ' kg',
                    'dimensions' => rand(100, 2000) . 'x' . rand(100, 1500) . 'x' . rand(100, 1000) . ' mm',
                    'power' => rand(1, 50) . ' kW',
                    'voltage' => ['220V', '380V', '24V', '110V'][array_rand(['220V', '380V', '24V', '110V'])],
                    'material' => ['Steel', 'Aluminum', 'Cast Iron', 'Stainless Steel'][array_rand(['Steel', 'Aluminum', 'Cast Iron', 'Stainless Steel'])],
                    'operating_temp' => '-20°C to +80°C',
                    'protection_class' => 'IP65'
                ];
            }
            
            // Cập nhật stock management
            if (!$product->manage_stock) {
                $updates['manage_stock'] = true;
                $updates['stock_quantity'] = rand(1, 50);
                $updates['in_stock'] = true;
                $updates['low_stock_threshold'] = 5;
            }
            
            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật physical product ID {$product->id}");
            }
        }
        
        $this->command->info("📦 Đã chuẩn hóa {$updated} physical products");
    }

    /**
     * Bổ sung mô tả cho products thiếu
     */
    private function addMissingDescriptions(): void
    {
        $this->command->info('📝 Bổ sung mô tả cho products thiếu...');
        
        $missingDescProducts = MarketplaceProduct::where(function($q) {
            $q->whereNull('description')
              ->orWhere('description', '')
              ->orWhereRaw('LENGTH(description) < 50');
        })->get();
        
        $genericDescriptions = [
            'Sản phẩm chất lượng cao cho ngành cơ khí và công nghiệp. Được thiết kế và sản xuất theo tiêu chuẩn quốc tế, đảm bảo độ bền và hiệu suất vượt trội. Phù hợp cho nhiều ứng dụng khác nhau.',
            'Thiết bị chuyên dụng với công nghệ tiên tiến và chất lượng đáng tin cậy. Được kiểm tra nghiêm ngặt trước khi xuất xưởng. Hỗ trợ kỹ thuật và bảo hành theo tiêu chuẩn nhà sản xuất.',
            'Sản phẩm được nhập khẩu từ các nhà sản xuất uy tín trên thế giới. Tuân thủ các tiêu chuẩn về chất lượng và an toàn. Phù hợp cho các ứng dụng công nghiệp và dân dụng.',
            'Linh kiện và thiết bị chất lượng cao với giá cả cạnh tranh. Được kiểm tra và đóng gói cẩn thận trước khi giao hàng. Hỗ trợ tư vấn kỹ thuật và lắp đặt.',
            'Sản phẩm chuyên nghiệp cho ngành cơ khí với độ chính xác và độ tin cậy cao. Thiết kế tối ưu, dễ sử dụng và bảo trì. Được nhiều khách hàng tin tưởng và sử dụng.'
        ];
        
        $updated = 0;
        foreach ($missingDescProducts as $product) {
            $product->update([
                'description' => $genericDescriptions[array_rand($genericDescriptions)],
                'short_description' => 'Sản phẩm chất lượng cao cho ngành cơ khí và công nghiệp.'
            ]);
            
            $updated++;
            $this->command->info("✅ Bổ sung mô tả cho product ID {$product->id}");
        }
        
        $this->command->info("📝 Đã bổ sung mô tả cho {$updated} products");
    }

    /**
     * Đảm bảo tất cả products có hình ảnh chất lượng
     */
    private function ensureQualityImages(): void
    {
        $this->command->info('🖼️ Đảm bảo hình ảnh chất lượng cho tất cả products...');
        
        $availableImages = [
            '/images/products/bearing-set.jpg',
            '/images/products/hydraulic-cylinder.jpg',
            '/images/products/motor-servo.jpg',
            '/images/products/pneumatic-cylinder.jpg',
            '/images/products/pressure-sensor.jpg',
            '/images/products/solenoid-valve.jpg',
            '/images/products/encoder.jpg',
            '/images/products/relay.jpg',
            '/images/products/contactor.jpg',
            '/images/products/inverter.jpg',
            '/images/products/plc.jpg',
            '/images/products/gear-motor.jpg',
            '/images/products/laser-sensor.jpg',
            '/images/products/ball-valve.jpg',
            '/images/products/centrifugal-pump.jpg',
            '/images/marketplace/digital-cad.jpg',
            '/images/marketplace/technical-drawing.jpg',
            '/images/marketplace/software-tool.jpg',
            '/images/marketplace/excel-template.jpg',
            '/images/marketplace/solidworks-file.jpg',
            '/images/demo/product-1.jpg',
            '/images/demo/product-2.jpg',
            '/images/demo/product-3.jpg',
            '/images/demo/product-4.jpg',
            '/images/demo/product-5.jpg'
        ];
        
        $productsNeedImages = MarketplaceProduct::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->get();
        
        $updated = 0;
        foreach ($productsNeedImages as $product) {
            $randomImage = $availableImages[array_rand($availableImages)];
            
            // Tạo gallery với 2-4 hình ảnh
            $galleryCount = rand(2, 4);
            $gallery = [];
            for ($i = 0; $i < $galleryCount; $i++) {
                $gallery[] = $availableImages[array_rand($availableImages)];
            }
            
            $product->update([
                'featured_image' => $randomImage,
                'images' => array_unique($gallery) // Loại bỏ trùng lặp
            ]);
            
            $updated++;
            $this->command->info("✅ Cập nhật hình ảnh cho product ID {$product->id}");
        }
        
        $this->command->info("🖼️ Đã cập nhật hình ảnh cho {$updated} products");
    }
}
