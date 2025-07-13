<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\DB;

class FixDomainLinksSeeder extends Seeder
{
    /**
     * 🔧 Fix Domain Links in Product Images
     * 
     * Loại bỏ tất cả domain links trong featured_image và images
     * để tránh infinite loading loops
     */
    public function run(): void
    {
        $this->command->info('🔧 Bắt đầu fix domain links trong product images...');
        
        // Disable observers
        MarketplaceProduct::unsetEventDispatcher();
        
        // Backup trước khi fix
        $this->createBackup();
        
        // Fix featured_image có domain links
        $this->fixFeaturedImages();
        
        // Fix gallery images có domain links
        $this->fixGalleryImages();
        
        // Verify kết quả
        $this->verifyResults();
        
        $this->command->info('✅ Hoàn thành fix domain links!');
    }

    /**
     * Tạo backup
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup...');
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/fix_domain_links_backup_{$timestamp}.json");
        
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }
        
        $products = DB::table('marketplace_products')
            ->select('id', 'name', 'featured_image', 'images')
            ->get()
            ->toArray();
            
        file_put_contents($backupFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->command->info("✅ Backup tạo tại: {$backupFile}");
    }

    /**
     * Fix featured_image có domain links
     */
    private function fixFeaturedImages(): void
    {
        $this->command->info('🖼️ Fix featured_image có domain links...');
        
        // Tìm products có domain trong featured_image
        $productsWithDomainImages = DB::table('marketplace_products')
            ->where('featured_image', 'like', '%mechamap.test%')
            ->orWhere('featured_image', 'like', '%http%')
            ->get();
        
        $this->command->info("Tìm thấy {$productsWithDomainImages->count()} products có domain links");
        
        $availableImages = [
            '/images/products/mechanical-design.jpg',
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
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
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/DesignEngineer.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
            '/images/threads/Mechanical_components.png',
            '/images/threads/mechanical-mini-projects-cover-pic.webp',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg',
            '/images/demo/showcase-4.jpg',
            '/images/demo/showcase-5.jpg'
        ];
        
        $updated = 0;
        foreach ($productsWithDomainImages as $product) {
            // Chọn hình ảnh ngẫu nhiên
            $newImage = $availableImages[array_rand($availableImages)];
            
            DB::table('marketplace_products')
                ->where('id', $product->id)
                ->update(['featured_image' => $newImage]);
            
            $updated++;
            $this->command->info("✅ Fixed product ID {$product->id}: {$newImage}");
        }
        
        $this->command->info("🖼️ Đã fix {$updated} featured images");
    }

    /**
     * Fix gallery images có domain links
     */
    private function fixGalleryImages(): void
    {
        $this->command->info('🎨 Fix gallery images có domain links...');
        
        $availableImages = [
            '/images/products/mechanical-design.jpg',
            '/images/products/design-engineer.jpg',
            '/images/products/engineering-computer.jpg',
            '/images/products/mini-projects.jpg',
            '/images/products/mechanical-component-1.jpg',
            '/images/products/mechanical-engineering.jpg',
            '/images/products/industrial-equipment.jpg',
            '/images/products/components.jpg',
            '/images/products/factory-worker.jpg',
            '/images/products/product-1.jpg',
            '/images/products/product-2.jpg',
            '/images/products/product-3.jpg',
            '/images/showcase/1567174641278.jpg',
            '/images/showcase/DesignEngineer.jpg',
            '/images/showcase/Mechanical-Engineering.jpg',
            '/images/threads/Mechanical_components.png',
            '/images/demo/showcase-1.jpg',
            '/images/demo/showcase-2.jpg',
            '/images/demo/showcase-3.jpg'
        ];
        
        $allProducts = DB::table('marketplace_products')
            ->whereNotNull('images')
            ->get();
        
        $updated = 0;
        foreach ($allProducts as $product) {
            $images = json_decode($product->images, true);
            
            if (!is_array($images)) {
                continue;
            }
            
            $needsUpdate = false;
            $newImages = [];
            
            foreach ($images as $image) {
                if (strpos($image, 'mechamap.test') !== false || strpos($image, 'http') !== false) {
                    $newImages[] = $availableImages[array_rand($availableImages)];
                    $needsUpdate = true;
                } else {
                    $newImages[] = $image;
                }
            }
            
            if ($needsUpdate) {
                DB::table('marketplace_products')
                    ->where('id', $product->id)
                    ->update(['images' => json_encode(array_unique($newImages))]);
                
                $updated++;
                $this->command->info("✅ Fixed gallery for product ID {$product->id}");
            }
        }
        
        $this->command->info("🎨 Đã fix {$updated} gallery images");
    }

    /**
     * Verify kết quả
     */
    private function verifyResults(): void
    {
        $this->command->info('🔍 Verify kết quả...');
        
        // Kiểm tra featured_image
        $domainFeatured = DB::table('marketplace_products')
            ->where('featured_image', 'like', '%mechamap.test%')
            ->orWhere('featured_image', 'like', '%http%')
            ->count();
        
        // Kiểm tra gallery images
        $domainGallery = DB::table('marketplace_products')
            ->where('images', 'like', '%mechamap.test%')
            ->orWhere('images', 'like', '%http%')
            ->count();
        
        $this->command->info("📊 Kết quả verify:");
        $this->command->info("  - Featured images có domain: {$domainFeatured}");
        $this->command->info("  - Gallery images có domain: {$domainGallery}");
        
        if ($domainFeatured == 0 && $domainGallery == 0) {
            $this->command->info("✅ Tất cả domain links đã được loại bỏ!");
        } else {
            $this->command->warn("⚠️ Vẫn còn domain links cần fix");
        }
        
        // Kiểm tra product cụ thể bị lỗi
        $problemProduct = DB::table('marketplace_products')
            ->where('slug', 'may-cat-laser-co2-trumpf-trulaser-3030-cu')
            ->first();
        
        if ($problemProduct) {
            $this->command->info("\n🎯 Product bị lỗi:");
            $this->command->info("  - ID: {$problemProduct->id}");
            $this->command->info("  - Name: {$problemProduct->name}");
            $this->command->info("  - Featured Image: {$problemProduct->featured_image}");
        }
    }
}
