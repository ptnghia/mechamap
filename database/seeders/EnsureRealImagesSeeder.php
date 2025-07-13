<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\File;

class EnsureRealImagesSeeder extends Seeder
{
    /**
     * 🖼️ Ensure All Products Have Real Images
     * 
     * Đảm bảo tất cả products có hình ảnh thực tế, không phải SVG placeholder
     */
    public function run(): void
    {
        $this->command->info('🖼️ Đảm bảo tất cả products có hình ảnh thực tế...');
        
        // Disable observers
        MarketplaceProduct::unsetEventDispatcher();
        
        // Copy real images to products directory
        $this->copyRealImages();
        
        // Update all products with real images
        $this->updateProductImages();
        
        $this->command->info('✅ Hoàn thành đảm bảo hình ảnh thực tế!');
    }

    /**
     * Copy hình ảnh thực tế vào thư mục products
     */
    private function copyRealImages(): void
    {
        $this->command->info('📋 Copy hình ảnh thực tế...');
        
        $sourceImages = [
            'public/images/showcase/1567174641278.jpg' => 'public/images/products/laser-cutting-machine.jpg',
            'public/images/showcase/DesignEngineer.jpg' => 'public/images/products/design-engineer.jpg',
            'public/images/showcase/Mechanical-Engineering.jpg' => 'public/images/products/mechanical-engineering.jpg',
            'public/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg' => 'public/images/products/industrial-equipment.jpg',
            'public/images/threads/Mechanical_components.png' => 'public/images/products/mechanical-components.jpg',
            'public/images/threads/mechanical-mini-projects-cover-pic.webp' => 'public/images/products/mini-projects.jpg',
            'public/images/threads/male-worker-factory.webp' => 'public/images/products/factory-worker.jpg',
            'public/images/threads/man-woman-engineering-computer-mechanical.jpg' => 'public/images/products/engineering-computer.jpg',
        ];
        
        $copied = 0;
        foreach ($sourceImages as $source => $destination) {
            if (File::exists($source)) {
                File::copy($source, $destination);
                $copied++;
                $this->command->info("✅ Copy: " . basename($destination));
            }
        }
        
        $this->command->info("📋 Đã copy {$copied} hình ảnh thực tế");
    }

    /**
     * Update tất cả products với hình ảnh thực tế
     */
    private function updateProductImages(): void
    {
        $this->command->info('🔄 Update products với hình ảnh thực tế...');
        
        $realImages = [
            // Digital products
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
            
            // Physical products
            '/images/products/laser-cutting-machine.jpg',
            '/images/products/mechanical-engineering.jpg',
            '/images/products/industrial-equipment.jpg',
            '/images/products/mechanical-components.jpg',
            '/images/products/factory-worker.jpg',
            '/images/demo/showcase-4.jpg',
            '/images/demo/showcase-5.jpg',
            
            // From showcase
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/DesignEngineer.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
            '/images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg',
            '/images/showcase/engineering_mechanical_3042380_cropped.jpg',
            
            // From threads
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
            
            // Check featured image
            if ($this->needsRealImage($product->featured_image)) {
                $updates['featured_image'] = $this->selectImageByType($product, $realImages);
                $needsUpdate = true;
            }
            
            // Check gallery images
            $gallery = $product->images ?? [];
            if (is_array($gallery) && count($gallery) > 0) {
                $newGallery = [];
                $galleryNeedsUpdate = false;
                
                foreach ($gallery as $image) {
                    if ($this->needsRealImage($image)) {
                        $newGallery[] = $realImages[array_rand($realImages)];
                        $galleryNeedsUpdate = true;
                    } else {
                        $newGallery[] = $image;
                    }
                }
                
                if ($galleryNeedsUpdate) {
                    $updates['images'] = array_unique($newGallery);
                    $needsUpdate = true;
                }
            } else {
                // Create new gallery if empty
                $galleryCount = rand(3, 4);
                $newGallery = [];
                for ($i = 0; $i < $galleryCount; $i++) {
                    $newGallery[] = $realImages[array_rand($realImages)];
                }
                $updates['images'] = array_unique($newGallery);
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $product->update($updates);
                $updated++;
                $this->command->info("✅ Updated product ID {$product->id}: {$product->name}");
            }
        }
        
        $this->command->info("🔄 Đã update {$updated} products");
    }

    /**
     * Kiểm tra xem hình ảnh có cần thay thế không
     */
    private function needsRealImage($imagePath): bool
    {
        if (empty($imagePath)) {
            return true;
        }
        
        // Check if it's a placeholder or SVG
        if (strpos($imagePath, 'placeholder') !== false) {
            return true;
        }
        
        // Check if file exists and is real image
        $fullPath = public_path($imagePath);
        if (!File::exists($fullPath)) {
            return true;
        }
        
        // Check if it's SVG (text file)
        $content = File::get($fullPath);
        if (strpos($content, '<svg') !== false || strpos($content, '<?xml') !== false) {
            return true;
        }
        
        return false;
    }

    /**
     * Chọn hình ảnh phù hợp theo loại sản phẩm
     */
    private function selectImageByType($product, $images): string
    {
        $digitalImages = [
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
        ];
        
        $physicalImages = [
            '/images/products/laser-cutting-machine.jpg',
            '/images/products/mechanical-engineering.jpg',
            '/images/products/industrial-equipment.jpg',
            '/images/products/mechanical-components.jpg',
            '/images/products/factory-worker.jpg',
            '/images/demo/showcase-4.jpg',
            '/images/demo/showcase-5.jpg',
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
        ];
        
        if ($product->product_type === 'digital') {
            return $digitalImages[array_rand($digitalImages)];
        } else {
            return $physicalImages[array_rand($physicalImages)];
        }
    }
}
