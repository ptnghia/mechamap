<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\File;

class MarketplaceProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🖼️  Adding featured images to marketplace products...');

        // Danh sách ảnh có sẵn cho products
        $productImages = [
            // Ảnh từ thư mục showcase (engineering/mechanical)
            'images/showcase/Mechanical-Engineering.jpg',
            'images/showcase/DesignEngineer.jpg',
            'images/showcase/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg',
            'images/showcase/PFxP5HX8oNsLtufFRMumpc.jpg',
            'images/showcase/depositphotos_73832701-Mechanical-design-office-.jpg',
            'images/showcase/engineering_mechanical_3042380_cropped.jpg',
            'images/showcase/mechanical-design-vs-mechanical-engineer2.jpg.webp',
            'images/showcase/mj_11208_2.jpg',
            'images/showcase/mj_11226_4.jpg',
            'images/showcase/1567174641278.jpg',

            // Ảnh từ thư mục threads (technical/engineering)
            'images/threads/Mechanical-Engineer-1-1024x536.webp',
            'images/threads/Mechanical-Engineering-thumbnail.jpg',
            'images/threads/Mechanical_components.png',
            'images/threads/Professional Engineer.webp',
            'images/threads/compressed_2151589656.jpg',
            'images/threads/images.jpg',
            'images/threads/male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp',
            'images/threads/male-worker-factory.webp',
            'images/threads/man-woman-engineering-computer-mechanical.jpg',
            'images/threads/mechanical-engineering-la-gi-7.webp',
            'images/threads/mechanical-mini-projects-cover-pic.webp',
            'images/threads/mechanical-update_0.jpg',
            'images/threads/mj_11351_4.jpg',
            'images/threads/program-mech-eng.jpg',
            'images/threads/success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp',
            'images/threads/ImageForArticle_20492_16236782958233468.webp',

            // Ảnh từ thư mục demo
            'images/demo/showcase-1.jpg',
            'images/demo/showcase-2.jpg',
            'images/demo/showcase-3.jpg',
            'images/demo/showcase-4.jpg',
            'images/demo/showcase-5.jpg',
            'images/demo/gallery-1.jpg',
            'images/demo/gallery-2.jpg',
            'images/demo/gallery-3.jpg',
            'images/demo/gallery-4.jpg',
            'images/demo/gallery-5.jpg',
            'images/demo/thread-1.jpg',
            'images/demo/thread-2.jpg',
            'images/demo/thread-3.jpg',
            'images/demo/thread-4.jpg',
            'images/demo/thread-5.jpg',

            // Ảnh từ thư mục categories (icons/technical)
            'images/categories/automation.png',
            'images/categories/brakes.png',
            'images/categories/control.png',
            'images/categories/drill.png',
            'images/categories/engineering.png',
            'images/categories/mechanic.png',
            'images/categories/robot.png',
            'images/categories/robotic-arm.png',
            'images/categories/timing.png',

            // Ảnh từ showcases (duplicate nhưng đảm bảo đủ)
            'images/showcases/Mechanical-Engineering.jpg',
            'images/showcases/DesignEngineer.jpg',
            'images/showcases/Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg',
            'images/showcases/PFxP5HX8oNsLtufFRMumpc.jpg',
            'images/showcases/depositphotos_73832701-Mechanical-design-office-.jpg',
            'images/showcases/engineering_mechanical_3042380_cropped.jpg',
            'images/showcases/mechanical-design-vs-mechanical-engineer2.jpg.webp',
            'images/showcases/mj_11208_2.jpg',
            'images/showcases/mj_11226_4.jpg',
            'images/showcases/1567174641278.jpg',
            'images/showcases/demo-3.jpg',
            'images/showcases/demo-4.jpg',
            'images/showcases/demo-5.jpg',
        ];

        // Lọc chỉ những ảnh thực sự tồn tại
        $validImages = [];
        foreach ($productImages as $imagePath) {
            $fullPath = public_path($imagePath);
            if (File::exists($fullPath)) {
                $validImages[] = $imagePath;
            } else {
                $this->command->warn("⚠️  Image not found: {$imagePath}");
            }
        }

        $this->command->info("✅ Found " . count($validImages) . " valid images");

        // Lấy tất cả products chưa có featured_image
        $products = MarketplaceProduct::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->get();

        $this->command->info("📦 Found {$products->count()} products without featured images");

        if ($products->isEmpty()) {
            $this->command->info('🎉 All products already have featured images!');
            return;
        }

        if (empty($validImages)) {
            $this->command->error('❌ No valid images found to assign!');
            return;
        }

        $updated = 0;
        $imageIndex = 0;

        foreach ($products as $product) {
            // Chọn ảnh theo vòng lặp để đảm bảo phân bố đều
            $selectedImage = $validImages[$imageIndex % count($validImages)];

            // Cập nhật featured_image
            $product->update([
                'featured_image' => $selectedImage
            ]);

            $this->command->info("✅ Updated product #{$product->id}: {$product->name} -> {$selectedImage}");

            $updated++;
            $imageIndex++;
        }

        $this->command->info("🎉 Successfully updated {$updated} products with featured images!");
        $this->command->info("📊 Used " . count($validImages) . " different images in rotation");
    }
}
