<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Models\Thread;
use App\Models\Showcase;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ComprehensiveImageSeeder extends Seeder
{
    private $sourceImagesPath;
    private $publicImagesPath;
    
    public function __construct()
    {
        $this->sourceImagesPath = base_path('images');
        $this->publicImagesPath = public_path('images');
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎨 Starting Comprehensive Image Seeding...');
        
        // 1. Tạo cấu trúc thư mục chuẩn
        $this->createDirectoryStructure();
        
        // 2. Copy demo images từ root/images
        $this->copyDemoImages();
        
        // 3. Migrate existing showcase images
        $this->migrateShowcaseImages();
        
        // 4. Seed user avatars
        $this->seedUserAvatars();
        
        // 5. Seed thread images
        $this->seedThreadImages();
        
        // 6. Seed showcase images với media relationships
        $this->seedShowcaseImagesWithMedia();
        
        // 7. Cleanup orphaned files
        $this->cleanupOrphanedFiles();
        
        $this->command->info('✅ Comprehensive Image Seeding completed!');
    }
    
    /**
     * Tạo cấu trúc thư mục chuẩn
     */
    private function createDirectoryStructure(): void
    {
        $this->command->info('📁 Creating unified directory structure...');
        
        $directories = [
            'users/avatars',
            'threads',
            'showcases',
            'categories',
            'forums',
            'temp',
            'demo',
            'placeholders',
            'brand',
            'settings'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = $this->publicImagesPath . '/' . $dir;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->command->line("  ✅ Created: {$dir}");
            }
        }
    }
    
    /**
     * Copy demo images từ root/images
     */
    private function copyDemoImages(): void
    {
        $this->command->info('📦 Copying demo images from root/images...');
        
        if (!File::exists($this->sourceImagesPath)) {
            $this->command->warn('⚠️  Source images directory not found: ' . $this->sourceImagesPath);
            return;
        }
        
        $categories = ['users', 'threads', 'showcase', 'category-forum', 'setting'];
        
        foreach ($categories as $category) {
            $sourcePath = $this->sourceImagesPath . '/' . $category;
            
            if (File::exists($sourcePath)) {
                $files = File::files($sourcePath);
                
                foreach ($files as $file) {
                    $destinationDir = $this->getDestinationDirectory($category);
                    $destinationPath = $this->publicImagesPath . '/' . $destinationDir . '/' . $file->getFilename();
                    
                    if (!File::exists($destinationPath)) {
                        File::copy($file->getPathname(), $destinationPath);
                        $this->command->line("  📄 Copied: {$category}/{$file->getFilename()}");
                    }
                }
            }
        }
    }
    
    /**
     * Get destination directory based on category
     */
    private function getDestinationDirectory(string $category): string
    {
        $mapping = [
            'users' => 'users/avatars',
            'threads' => 'threads',
            'showcase' => 'showcases',
            'category-forum' => 'categories',
            'setting' => 'brand'
        ];
        
        return $mapping[$category] ?? $category;
    }
    
    /**
     * Migrate existing showcase images
     */
    private function migrateShowcaseImages(): void
    {
        $this->command->info('🔄 Migrating existing showcase images...');
        
        $showcases = Showcase::all();
        
        foreach ($showcases as $showcase) {
            if ($showcase->cover_image) {
                // Extract filename from cover_image path
                $filename = basename($showcase->cover_image);
                $sourcePath = public_path($showcase->cover_image);
                $destinationPath = $this->publicImagesPath . '/showcases/' . $filename;
                
                if (File::exists($sourcePath) && !File::exists($destinationPath)) {
                    File::copy($sourcePath, $destinationPath);
                    
                    // Update showcase cover_image path
                    $showcase->update([
                        'cover_image' => '/images/showcases/' . $filename
                    ]);
                    
                    $this->command->line("  🔄 Migrated showcase image: {$filename}");
                }
            }
        }
    }
    
    /**
     * Seed user avatars
     */
    private function seedUserAvatars(): void
    {
        $this->command->info('👤 Seeding user avatars...');
        
        $users = User::whereNull('avatar')->orWhere('avatar', '')->get();
        $avatarFiles = File::files($this->publicImagesPath . '/users/avatars');
        
        foreach ($users as $index => $user) {
            if (empty($avatarFiles)) break;
            
            $avatarFile = $avatarFiles[$index % count($avatarFiles)];
            $avatarPath = '/images/users/avatars/' . $avatarFile->getFilename();
            
            // Update user avatar
            $user->update(['avatar' => $avatarPath]);
            
            // Create media record
            Media::create([
                'user_id' => $user->id,
                'file_name' => $avatarFile->getFilename(),
                'file_path' => $avatarPath,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($avatarFile->getExtension()),
                'file_size' => $avatarFile->getSize(),
                'file_extension' => $avatarFile->getExtension(),
                'file_category' => 'image',
                'mediable_id' => $user->id,
                'mediable_type' => User::class,
                'is_public' => true,
                'is_approved' => true
            ]);
            
            $this->command->line("  👤 Assigned avatar to: {$user->name}");
        }
    }
    
    /**
     * Seed thread images
     */
    private function seedThreadImages(): void
    {
        $this->command->info('📝 Seeding thread images...');
        
        $threads = Thread::whereDoesntHave('media')->take(20)->get();
        $threadImages = File::files($this->publicImagesPath . '/threads');
        
        foreach ($threads as $index => $thread) {
            if (empty($threadImages)) break;
            
            $imageFile = $threadImages[$index % count($threadImages)];
            $imagePath = '/images/threads/' . $imageFile->getFilename();
            
            // Create media record
            Media::create([
                'user_id' => $thread->user_id,
                'file_name' => $imageFile->getFilename(),
                'file_path' => $imagePath,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($imageFile->getExtension()),
                'file_size' => $imageFile->getSize(),
                'file_extension' => $imageFile->getExtension(),
                'file_category' => 'image',
                'mediable_id' => $thread->id,
                'mediable_type' => Thread::class,
                'is_public' => true,
                'is_approved' => true
            ]);
            
            $this->command->line("  📝 Added image to thread: {$thread->title}");
        }
    }
    
    /**
     * Seed showcase images với media relationships
     */
    private function seedShowcaseImagesWithMedia(): void
    {
        $this->command->info('🏆 Seeding showcase images with media relationships...');
        
        $showcases = Showcase::whereDoesntHave('media')->get();
        $showcaseImages = File::files($this->publicImagesPath . '/showcases');
        
        foreach ($showcases as $index => $showcase) {
            if (empty($showcaseImages)) break;
            
            $imageFile = $showcaseImages[$index % count($showcaseImages)];
            $imagePath = '/images/showcases/' . $imageFile->getFilename();
            
            // Create media record
            Media::create([
                'user_id' => $showcase->user_id,
                'file_name' => '[Featured] ' . $imageFile->getFilename(),
                'file_path' => $imagePath,
                'disk' => 'public',
                'mime_type' => $this->getMimeType($imageFile->getExtension()),
                'file_size' => $imageFile->getSize(),
                'file_extension' => $imageFile->getExtension(),
                'file_category' => 'image',
                'mediable_id' => $showcase->id,
                'mediable_type' => Showcase::class,
                'is_public' => true,
                'is_approved' => true
            ]);
            
            $this->command->line("  🏆 Added media to showcase: {$showcase->title}");
        }
    }
    
    /**
     * Cleanup orphaned files
     */
    private function cleanupOrphanedFiles(): void
    {
        $this->command->info('🧹 Cleaning up orphaned files...');
        
        // Remove files from settings directory (move to proper storage)
        $settingsPath = $this->publicImagesPath . '/settings';
        if (File::exists($settingsPath)) {
            $files = File::files($settingsPath);
            foreach ($files as $file) {
                File::delete($file->getPathname());
                $this->command->line("  🗑️  Removed orphaned: settings/{$file->getFilename()}");
            }
        }
    }
    
    /**
     * Get MIME type based on extension
     */
    private function getMimeType(string $extension): string
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml'
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
