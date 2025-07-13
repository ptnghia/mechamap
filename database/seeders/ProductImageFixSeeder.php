<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\File;

class ProductImageFixSeeder extends Seeder
{
    /**
     * 🖼️ Fix Product Images - Replace Placeholder with Real Images
     * 
     * Thay thế tất cả hình ảnh placeholder bằng hình ảnh thực tế
     * phù hợp với từng loại sản phẩm
     */
    public function run(): void
    {
        $this->command->info('🖼️ Bắt đầu fix hình ảnh products...');
        
        // Disable observers
        MarketplaceProduct::unsetEventDispatcher();
        
        // Backup trước khi fix
        $this->createBackup();
        
        // Tạo thư mục products nếu chưa có
        $this->createProductsDirectory();
        
        // Copy hình ảnh mẫu vào thư mục products
        $this->copyProductImages();
        
        // Fix tất cả products có hình placeholder hoặc không hợp lệ
        $this->fixAllProductImages();
        
        $this->command->info('✅ Hoàn thành fix hình ảnh products!');
    }

    /**
     * Tạo backup
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup...');
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/product_images_backup_{$timestamp}.json");
        
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }
        
        $products = MarketplaceProduct::select('id', 'name', 'featured_image', 'images')->get()->toArray();
        file_put_contents($backupFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->command->info("✅ Backup tạo tại: {$backupFile}");
    }

    /**
     * Tạo thư mục products
     */
    private function createProductsDirectory(): void
    {
        $productsDir = public_path('images/products');
        
        if (!File::exists($productsDir)) {
            File::makeDirectory($productsDir, 0755, true);
            $this->command->info("📁 Tạo thư mục: {$productsDir}");
        }
    }

    /**
     * Copy hình ảnh từ các thư mục khác vào products
     */
    private function copyProductImages(): void
    {
        $this->command->info('📋 Copy hình ảnh vào thư mục products...');
        
        $sourceImages = [
            // Từ showcase
            'public/images/showcase/1567174641278.jpg' => 'public/images/products/mechanical-component-1.jpg',
            'public/images/showcase/DesignEngineer.jpg' => 'public/images/products/design-engineer.jpg',
            'public/images/showcase/Mechanical-Engineering.jpg' => 'public/images/products/mechanical-engineering.jpg',
            'public/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg' => 'public/images/products/industrial-equipment.jpg',
            'public/images/showcase/mechanical-design-vs-mechanical-engineer2.jpg.webp' => 'public/images/products/mechanical-design.jpg',
            
            // Từ threads
            'public/images/threads/Mechanical_components.png' => 'public/images/products/components.jpg',
            'public/images/threads/mechanical-mini-projects-cover-pic.webp' => 'public/images/products/mini-projects.jpg',
            'public/images/threads/male-worker-factory.webp' => 'public/images/products/factory-worker.jpg',
            'public/images/threads/man-woman-engineering-computer-mechanical.jpg' => 'public/images/products/engineering-computer.jpg',
            
            // Từ demo
            'public/images/demo/showcase-1.jpg' => 'public/images/products/product-1.jpg',
            'public/images/demo/showcase-2.jpg' => 'public/images/products/product-2.jpg',
            'public/images/demo/showcase-3.jpg' => 'public/images/products/product-3.jpg',
            'public/images/demo/showcase-4.jpg' => 'public/images/products/product-4.jpg',
            'public/images/demo/showcase-5.jpg' => 'public/images/products/product-5.jpg',
        ];
        
        $copied = 0;
        foreach ($sourceImages as $source => $destination) {
            if (File::exists($source) && !File::exists($destination)) {
                File::copy($source, $destination);
                $copied++;
                $this->command->info("✅ Copy: " . basename($destination));
            }
        }
        
        $this->command->info("📋 Đã copy {$copied} hình ảnh");
    }

    /**
     * Fix tất cả hình ảnh products
     */
    private function fixAllProductImages(): void
    {
        $this->command->info('🔧 Fix hình ảnh cho tất cả products...');
        
        // Danh sách hình ảnh thực tế có sẵn
        $realImages = [
            // Digital products
            '/images/products/mechanical-design.jpg',
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
            
            // Physical products  
            '/images/products/mechanical-component-1.jpg',
            '/images/products/mechanical-engineering.jpg',
            '/images/products/industrial-equipment.jpg',
            '/images/products/components.jpg',
            '/images/products/factory-worker.jpg',
            '/images/products/product-1.jpg',
            '/images/products/product-2.jpg',
            '/images/products/product-3.jpg',
            '/images/products/product-4.jpg',
            '/images/products/product-5.jpg',
            
            // Từ showcase
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/DesignEngineer.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
            '/images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg',
            '/images/showcase/engineering_mechanical_3042380_cropped.jpg',
            '/images/showcase/mj_11208_2.jpg',
            '/images/showcase/mj_11226_4.jpg',
            
            // Từ threads
            '/images/threads/Mechanical_components.png',
            '/images/threads/Mechanical-Engineer-1-1024x536.webp',
            '/images/threads/Mechanical-Engineering-thumbnail.jpg',
            '/images/threads/male-worker-factory.webp',
            '/images/threads/man-woman-engineering-computer-mechanical.jpg',
            '/images/threads/mechanical-mini-projects-cover-pic.webp',
            '/images/threads/program-mech-eng.jpg',
        ];
        
        $products = MarketplaceProduct::all();
        $updated = 0;
        
        foreach ($products as $product) {
            $needsUpdate = false;
            $updates = [];
            
            // Kiểm tra featured_image
            if (empty($product->featured_image) || 
                strpos($product->featured_image, 'placeholder') !== false ||
                strpos($product->featured_image, 'mechamap.test') !== false ||
                !$this->imageExists($product->featured_image)) {
                
                // Chọn hình ảnh phù hợp theo loại sản phẩm
                $updates['featured_image'] = $this->selectImageByProductType($product, $realImages);
                $needsUpdate = true;
            }
            
            // Kiểm tra và cập nhật gallery images
            $gallery = $product->images ?? [];
            $newGallery = [];
            $galleryNeedsUpdate = false;
            
            if (empty($gallery) || count($gallery) < 2) {
                // Tạo gallery mới với 3-4 hình ảnh
                $galleryCount = rand(3, 4);
                for ($i = 0; $i < $galleryCount; $i++) {
                    $newGallery[] = $realImages[array_rand($realImages)];
                }
                $galleryNeedsUpdate = true;
            } else {
                // Kiểm tra từng hình trong gallery
                foreach ($gallery as $image) {
                    if (strpos($image, 'placeholder') !== false || 
                        strpos($image, 'mechamap.test') !== false ||
                        !$this->imageExists($image)) {
                        $newGallery[] = $realImages[array_rand($realImages)];
                        $galleryNeedsUpdate = true;
                    } else {
                        $newGallery[] = $image;
                    }
                }
            }
            
            if ($galleryNeedsUpdate) {
                $updates['images'] = array_unique($newGallery);
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Cập nhật hình ảnh product ID {$product->id}: {$product->name}");
            }
        }
        
        $this->command->info("🔧 Đã fix hình ảnh cho {$updated} products");
    }

    /**
     * Chọn hình ảnh phù hợp theo loại sản phẩm
     */
    private function selectImageByProductType($product, $images): string
    {
        $digitalImages = [
            '/images/products/mechanical-design.jpg',
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
        ];
        
        $physicalImages = [
            '/images/products/mechanical-component-1.jpg',
            '/images/products/mechanical-engineering.jpg',
            '/images/products/industrial-equipment.jpg',
            '/images/products/components.jpg',
            '/images/products/factory-worker.jpg',
            '/images/products/product-1.jpg',
            '/images/products/product-2.jpg',
            '/images/products/product-3.jpg',
            '/images/products/product-4.jpg',
            '/images/products/product-5.jpg',
        ];
        
        // Chọn hình theo loại sản phẩm
        if ($product->product_type === 'digital') {
            return $digitalImages[array_rand($digitalImages)];
        } else {
            return $physicalImages[array_rand($physicalImages)];
        }
    }

    /**
     * Kiểm tra hình ảnh có tồn tại không
     */
    private function imageExists($imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }
        
        // Loại bỏ domain nếu có
        $imagePath = str_replace(['https://mechamap.test', 'http://mechamap.test'], '', $imagePath);
        
        // Đảm bảo path bắt đầu bằng /
        if (!str_starts_with($imagePath, '/')) {
            $imagePath = '/' . $imagePath;
        }
        
        $fullPath = public_path($imagePath);
        return File::exists($fullPath);
    }
}
