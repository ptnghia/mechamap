<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Media;
use App\Models\User;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Showcase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    /**
     * Seed media data với hình ảnh có sẵn trong thư mục public/images
     */
    public function run(): void
    {
        $this->command->info('📸 Bắt đầu seed media data...');

        // Seed category icons
        $this->seedCategoryIcons();

        // Seed user avatars
        $this->seedUserAvatars();

        // Seed showcase images
        $this->seedShowcaseImages();

        // Seed thread images
        $this->seedThreadImages();

        // Seed setting images
        $this->seedSettingImages();

        // Seed demo images
        $this->seedDemoImages();

        $this->command->info('✅ Hoàn thành seed media data!');
    }

    private function seedCategoryIcons(): void
    {
        $this->command->info('📂 Seeding category icons...');

        $categoryIcons = [
            'automation.png' => 'Automation & Control Systems',
            'brakes.png' => 'Brake Systems & Components',
            'control.png' => 'Control Systems',
            'drill.png' => 'Drilling & Machining',
            'engineering.png' => 'General Engineering',
            'mechanic.png' => 'Mechanical Systems',
            'robot.png' => 'Robotics',
            'robotic-arm.png' => 'Industrial Robotics',
            'timing.png' => 'Timing Systems',
        ];

        $categories = Category::all();

        foreach ($categoryIcons as $filename => $description) {
            $category = $categories->random();

            Media::create([
                'user_id' => 1, // Admin user
                'file_name' => $filename,
                'file_path' => '/images/category-forum/' . $filename,
                'disk' => 'public',
                'mime_type' => 'image/png',
                'file_size' => $this->getFileSize('category-forum/' . $filename),
                'file_extension' => 'png',
                'file_category' => 'image',
                'mediable_type' => Category::class,
                'mediable_id' => $category->id,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }
    }

    private function seedUserAvatars(): void
    {
        $this->command->info('👤 Seeding user avatars...');

        $avatarFiles = [
            'avatar-1.jpg', 'avatar-2.jpg', 'avatar-3.jpg', 'avatar-4.jpg', 'avatar-5.jpg',
            'avatar-6.jpg', 'avatar-7.jpg', 'avatar-8.jpg', 'avatar-9.jpg', 'avatar-10.jpg'
        ];

        $users = User::all();

        foreach ($users as $index => $user) {
            $avatarFile = $avatarFiles[$index % count($avatarFiles)];

            Media::create([
                'user_id' => $user->id,
                'file_name' => $avatarFile,
                'file_path' => '/images/users/' . $avatarFile,
                'disk' => 'public',
                'mime_type' => 'image/jpeg',
                'file_size' => $this->getFileSize('users/' . $avatarFile),
                'file_extension' => 'jpg',
                'file_category' => 'image',
                'mediable_type' => User::class,
                'mediable_id' => $user->id,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);

            // Update user avatar path
            $user->update(['avatar' => '/images/users/' . $avatarFile]);
        }
    }

    private function seedShowcaseImages(): void
    {
        $this->command->info('🏆 Seeding showcase images...');

        $showcaseImages = [
            '1567174641278.jpg' => 'Modern Engineering Workspace',
            'DesignEngineer.jpg' => 'Design Engineer at Work',
            'Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg' => 'Mechanical Engineering Professionals',
            'Mechanical-Engineering.jpg' => 'Mechanical Engineering Overview',
            'PFxP5HX8oNsLtufFRMumpc.jpg' => 'Advanced Manufacturing',
            'depositphotos_73832701-Mechanical-design-office-.jpg' => 'Mechanical Design Office',
            'engineering_mechanical_3042380_cropped.jpg' => 'Engineering Mechanical Systems',
            'mechanical-design-vs-mechanical-engineer2.jpg.webp' => 'Mechanical Design vs Engineering',
            'mj_11208_2.jpg' => 'Industrial Machinery',
            'mj_11226_4.jpg' => 'Manufacturing Equipment',
        ];

        foreach ($showcaseImages as $filename => $title) {
            Media::create([
                'user_id' => 1,
                'file_name' => $filename,
                'file_path' => '/images/showcase/' . $filename,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($filename),
                'file_size' => $this->getFileSize('showcase/' . $filename),
                'file_extension' => $this->getExtension($filename),
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for showcase
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }
    }

    private function seedThreadImages(): void
    {
        $this->command->info('💬 Seeding thread images...');

        $threadImages = [
            'ImageForArticle_20492_16236782958233468.webp' => 'Engineering Article Image',
            'Mechanical-Engineer-1-1024x536.webp' => 'Mechanical Engineer Professional',
            'Mechanical-Engineering-thumbnail.jpg' => 'Mechanical Engineering Thumbnail',
            'Mechanical_components.png' => 'Mechanical Components',
            'Professional Engineer.webp' => 'Professional Engineer',
            'compressed_2151589656.jpg' => 'Engineering Workspace',
            'images.jpg' => 'Technical Images',
            'male-asian-engineer-professional-having-discussion-standing-by-machine-factory-two-asian-coworker-brainstorm-explaining-solves-process-curcuit-mother-board-machine.webp' => 'Engineers Discussion',
            'male-worker-factory.webp' => 'Factory Worker',
            'man-woman-engineering-computer-mechanical.jpg' => 'Engineering Team',
            'mechanical-engineering-la-gi-7.webp' => 'Mechanical Engineering Basics',
            'mechanical-mini-projects-cover-pic.webp' => 'Mechanical Mini Projects',
            'mechanical-update_0.jpg' => 'Mechanical Updates',
            'mj_11351_4.jpg' => 'Industrial Equipment',
            'program-mech-eng.jpg' => 'Mechanical Engineering Program',
            'success-story-schuetz-industrie-anlagenmechanikerin-2128x1330-c.jpg.webp' => 'Success Story',
        ];

        foreach ($threadImages as $filename => $title) {
            Media::create([
                'user_id' => 1,
                'file_name' => $filename,
                'file_path' => '/images/threads/' . $filename,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($filename),
                'file_size' => $this->getFileSize('threads/' . $filename),
                'file_extension' => $this->getExtension($filename),
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for threads
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }
    }

    private function seedSettingImages(): void
    {
        $this->command->info('⚙️ Seeding setting images...');

        $settingImages = [
            'banenr.jpg' => 'Site Banner',
            'favicon.png' => 'Site Favicon',
            'logo.png' => 'Site Logo',
        ];

        foreach ($settingImages as $filename => $title) {
            Media::create([
                'user_id' => 1,
                'file_name' => $filename,
                'file_path' => '/images/setting/' . $filename,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($filename),
                'file_size' => $this->getFileSize('setting/' . $filename),
                'file_extension' => $this->getExtension($filename),
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for settings
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }
    }

    private function seedDemoImages(): void
    {
        $this->command->info('🎨 Seeding demo images...');

        // Demo gallery images - sử dụng hình ảnh có sẵn
        $availableImages = [
            'automation.png', 'brakes.png', 'control.png', 'drill.png', 'engineering.png'
        ];

        for ($i = 0; $i < 5; $i++) {
            $filename = $availableImages[$i];
            Media::create([
                'user_id' => 1,
                'file_name' => "gallery-{$filename}",
                'file_path' => "/images/category-forum/{$filename}",
                'disk' => 'public',
                'mime_type' => 'image/png',
                'file_size' => $this->getFileSize("category-forum/{$filename}"),
                'file_extension' => 'png',
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for gallery
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }

        // Demo showcase images - sử dụng hình ảnh showcase có sẵn
        $showcaseFiles = [
            '1567174641278.jpg', 'DesignEngineer.jpg', 'Mechanical-Engineering-MS-Professionals-Hero-1600x900_0.jpg',
            'Mechanical-Engineering.jpg', 'PFxP5HX8oNsLtufFRMumpc.jpg'
        ];

        for ($i = 0; $i < 5; $i++) {
            $filename = $showcaseFiles[$i];
            Media::create([
                'user_id' => 1,
                'file_name' => "demo-showcase-{$filename}",
                'file_path' => "/images/showcase/{$filename}",
                'disk' => 'public',
                'mime_type' => 'image/jpeg',
                'file_size' => $this->getFileSize("showcase/{$filename}"),
                'file_extension' => 'jpg',
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for demo showcase
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }

        // Demo thread images - sử dụng hình ảnh thread có sẵn
        $threadFiles = [
            'ImageForArticle_20492_16236782958233468.webp', 'Mechanical-Engineer-1-1024x536.webp',
            'Mechanical-Engineering-thumbnail.jpg', 'Mechanical_components.png', 'Professional Engineer.webp'
        ];

        for ($i = 0; $i < 5; $i++) {
            $filename = $threadFiles[$i];
            Media::create([
                'user_id' => 1,
                'file_name' => "demo-thread-{$filename}",
                'file_path' => "/images/threads/{$filename}",
                'disk' => 'public',
                'mime_type' => $this->getMimeType($filename),
                'file_size' => $this->getFileSize("threads/{$filename}"),
                'file_extension' => $this->getExtension($filename),
                'file_category' => 'image',
                'mediable_type' => User::class, // Assign to admin user for demo threads
                'mediable_id' => 1,
                'is_public' => true,
                'is_approved' => true,
                'virus_scanned' => true,
                'scanned_at' => now(),
                'contains_sensitive_data' => false,
                'download_count' => 0,
                'processing_status' => 'completed',
            ]);
        }
    }

    private function getFileSize(string $relativePath): int
    {
        $fullPath = public_path('images/' . $relativePath);
        return File::exists($fullPath) ? File::size($fullPath) : 1024; // Default 1KB if file not found
    }

    private function getMimeType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
            default => 'image/jpeg'
        };
    }

    private function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
