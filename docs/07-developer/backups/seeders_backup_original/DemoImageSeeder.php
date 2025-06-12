<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class DemoImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo hình ảnh demo cho showcases và threads
     */
    public function run(): void
    {
        $this->command->info('🖼️ Tạo hình ảnh demo cho Showcases và Threads...');

        // Tạo demo images trong public/images/demo
        $this->createDemoImages();

        // Seed images cho Showcases
        $this->seedShowcaseImages();

        // Seed images cho Threads
        $this->seedThreadImages();

        $this->command->info('✅ Hoàn thành tạo hình ảnh demo!');
    }

    /**
     * Tạo demo images trong thư mục public/images/demo
     */
    private function createDemoImages(): void
    {
        $demoPath = public_path('images/demo');

        if (!File::exists($demoPath)) {
            File::makeDirectory($demoPath, 0755, true);
        }

        // Demo images cho mechanical engineering forum
        $demoImages = [
            // Showcase Images
            'showcase-1.jpg' => $this->generateDemoImageContent('Mechanical Design Showcase', '#2563eb'),
            'showcase-2.jpg' => $this->generateDemoImageContent('CAD/CAM Project', '#059669'),
            'showcase-3.jpg' => $this->generateDemoImageContent('3D Printing Model', '#dc2626'),
            'showcase-4.jpg' => $this->generateDemoImageContent('CNC Machining', '#7c3aed'),
            'showcase-5.jpg' => $this->generateDemoImageContent('Automation System', '#ea580c'),

            // Thread Images
            'thread-1.jpg' => $this->generateDemoImageContent('Forum Discussion', '#1f2937'),
            'thread-2.jpg' => $this->generateDemoImageContent('Technical Question', '#374151'),
            'thread-3.jpg' => $this->generateDemoImageContent('Engineering Solution', '#4b5563'),
            'thread-4.jpg' => $this->generateDemoImageContent('Community Help', '#6b7280'),
            'thread-5.jpg' => $this->generateDemoImageContent('Project Sharing', '#9ca3af'),

            // Media Gallery Images
            'gallery-1.jpg' => $this->generateDemoImageContent('Gallery Image 1', '#f59e0b'),
            'gallery-2.jpg' => $this->generateDemoImageContent('Gallery Image 2', '#10b981'),
            'gallery-3.jpg' => $this->generateDemoImageContent('Gallery Image 3', '#3b82f6'),
            'gallery-4.jpg' => $this->generateDemoImageContent('Gallery Image 4', '#8b5cf6'),
            'gallery-5.jpg' => $this->generateDemoImageContent('Gallery Image 5', '#ef4444'),
        ];

        foreach ($demoImages as $filename => $content) {
            $filePath = $demoPath . '/' . $filename;
            if (!File::exists($filePath)) {
                File::put($filePath, $content);
                $this->command->info("📁 Tạo demo image: {$filename}");
            }
        }
    }

    /**
     * Generate demo image content (SVG)
     */
    private function generateDemoImageContent(string $title, string $color): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:' . $color . ';stop-opacity:0.8" />
      <stop offset="100%" style="stop-color:' . $color . ';stop-opacity:0.4" />
    </linearGradient>
  </defs>
  <rect width="100%" height="100%" fill="url(#grad1)"/>
  <rect x="50" y="50" width="700" height="500" rx="10" ry="10" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
  <text x="50%" y="40%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="bold" fill="white">' . $title . '</text>
  <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="rgba(255,255,255,0.8)">MechaMap Demo Image</text>
  <text x="50%" y="65%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="rgba(255,255,255,0.6)">800 x 600 px</text>
  <circle cx="100" cy="100" r="30" fill="rgba(255,255,255,0.2)"/>
  <circle cx="700" cy="500" r="40" fill="rgba(255,255,255,0.15)"/>
  <rect x="600" y="80" width="120" height="80" rx="5" ry="5" fill="rgba(255,255,255,0.1)"/>
</svg>';
    }

    /**
     * Seed images cho Showcases
     */
    private function seedShowcaseImages(): void
    {
        $showcases = Showcase::with(['media'])->limit(10)->get();
        $demoImages = ['showcase-1.jpg', 'showcase-2.jpg', 'showcase-3.jpg', 'showcase-4.jpg', 'showcase-5.jpg'];
        $galleryImages = ['gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg'];

        foreach ($showcases as $index => $showcase) {
            // Chỉ thêm media nếu showcase chưa có
            if ($showcase->media->count() === 0) {
                // Thêm featured image
                $featuredImageIndex = $index % count($demoImages);
                $featuredImage = $demoImages[$featuredImageIndex];

                $this->createMediaRecord($showcase, $featuredImage, '[Featured] ' . $showcase->title, true);

                // Thêm gallery images (2-4 images)
                $galleryCount = rand(2, 4);
                for ($i = 0; $i < $galleryCount; $i++) {
                    $galleryImageIndex = ($index + $i + 1) % count($galleryImages);
                    $galleryImage = $galleryImages[$galleryImageIndex];

                    $this->createMediaRecord($showcase, $galleryImage, "Gallery Image " . ($i + 1), false);
                }

                $this->command->info("🖼️ Thêm " . ($galleryCount + 1) . " hình ảnh cho showcase: {$showcase->title}");
            }
        }
    }

    /**
     * Seed images cho Threads
     */
    private function seedThreadImages(): void
    {
        $threads = Thread::whereNull('featured_image')->limit(10)->get();
        $demoImages = ['thread-1.jpg', 'thread-2.jpg', 'thread-3.jpg', 'thread-4.jpg', 'thread-5.jpg'];

        foreach ($threads as $index => $thread) {
            $imageIndex = $index % count($demoImages);
            $imageName = $demoImages[$imageIndex];
            $imageUrl = asset('images/demo/' . $imageName);

            // Update thread với featured_image
            $thread->update([
                'featured_image' => $imageUrl
            ]);

            $this->command->info("🖼️ Thêm featured image cho thread: {$thread->title}");
        }
    }

    /**
     * Tạo Media record cho showcase
     */
    private function createMediaRecord(Showcase $showcase, string $imageName, string $title, bool $isFeatured): void
    {
        $fileName = $isFeatured ? '[Featured] ' . $imageName : $imageName;
        $fileUrl = asset('images/demo/' . $imageName);

        Media::create([
            'mediable_type' => Showcase::class,
            'mediable_id' => $showcase->id,
            'file_name' => $fileName,
            'file_path' => 'demo/' . $imageName,
            'file_type' => 'image/jpeg',
            'file_size' => 50000, // Demo size
            'url' => $fileUrl,
            'title' => $title,
            'description' => $isFeatured ? 'Featured image for ' . $showcase->title : 'Gallery image for ' . $showcase->title,
            'is_featured' => $isFeatured,
            'sort_order' => $isFeatured ? 0 : rand(1, 10),
            'uploaded_by' => $showcase->user_id,
        ]);
    }
}
