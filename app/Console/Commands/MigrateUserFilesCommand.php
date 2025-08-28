<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateUserFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'files:migrate-user-structure 
                            {--dry-run : Run without actually moving files}
                            {--user= : Migrate files for specific user ID}
                            {--category= : Migrate specific category only}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate existing files to user-based directory structure';

    /**
     * File mapping for migration
     */
    private array $fileMappings = [
        // Storage paths
        'comment-images' => 'comments',
        'thread-images' => 'threads', 
        'uploads/gallery' => 'gallery',
        'showcases/attachments' => 'showcases',
        'avatars' => 'avatars',
        
        // Public paths
        'images/comments' => 'comments',
        'images/threads' => 'threads',
        'images/showcases' => 'showcases',
    ];

    /**
     * Statistics tracking
     */
    private array $stats = [
        'total_files' => 0,
        'migrated_files' => 0,
        'failed_files' => 0,
        'skipped_files' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting file migration to user-based structure...');
        
        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No files will be actually moved');
        }

        // Create backup before migration
        if (!$this->option('dry-run')) {
            $this->createBackup();
        }

        // Migrate media records from database
        $this->migrateMediaRecords();

        // Migrate orphaned files (files without database records)
        $this->migrateOrphanedFiles();

        // Display results
        $this->displayResults();

        return Command::SUCCESS;
    }

    /**
     * Create backup of current file structure
     */
    private function createBackup(): void
    {
        $this->info('📦 Creating backup...');
        
        $backupDir = storage_path('backups/files_' . date('Y-m-d_H-i-s'));
        
        // Backup public/images
        if (File::exists(public_path('images'))) {
            File::copyDirectory(public_path('images'), $backupDir . '/public_images');
        }
        
        // Backup storage/app/public
        if (File::exists(storage_path('app/public'))) {
            File::copyDirectory(storage_path('app/public'), $backupDir . '/storage_public');
        }
        
        $this->info("✅ Backup created at: {$backupDir}");
    }

    /**
     * Migrate files based on media records in database
     */
    private function migrateMediaRecords(): void
    {
        $this->info('📁 Migrating files from database records...');

        $query = Media::with('user');
        
        if ($this->option('user')) {
            $query->where('user_id', $this->option('user'));
        }
        
        if ($this->option('category')) {
            $query->where('file_category', $this->option('category'));
        }

        $mediaRecords = $query->get();
        $this->stats['total_files'] += $mediaRecords->count();

        $progressBar = $this->output->createProgressBar($mediaRecords->count());
        $progressBar->start();

        foreach ($mediaRecords as $media) {
            $this->migrateMediaFile($media);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Migrate a single media file
     */
    private function migrateMediaFile(Media $media): void
    {
        if (!$media->user) {
            $this->stats['skipped_files']++;
            return;
        }

        $oldPath = $media->file_path;
        $newPath = $this->generateNewPath($media);

        if ($this->moveFile($oldPath, $newPath)) {
            if (!$this->option('dry-run')) {
                $media->update(['file_path' => $newPath]);
            }
            $this->stats['migrated_files']++;
        } else {
            $this->stats['failed_files']++;
        }
    }

    /**
     * Generate new file path based on user ID
     */
    private function generateNewPath(Media $media): string
    {
        $userId = $media->user_id;
        $category = $this->mapCategoryToFolder($media->file_category);
        $filename = basename($media->file_path);

        return "images/{$userId}/{$category}/{$filename}";
    }

    /**
     * Map file category to folder name
     */
    private function mapCategoryToFolder(string $category): string
    {
        return match($category) {
            'image' => 'gallery',
            'avatar' => 'avatars',
            'document' => 'documents',
            'cad' => 'cad',
            default => 'misc'
        };
    }

    /**
     * Move file from old path to new path
     */
    private function moveFile(string $oldPath, string $newPath): bool
    {
        // Try storage disk first
        if (Storage::disk('public')->exists($oldPath)) {
            return $this->moveStorageFile($oldPath, $newPath);
        }

        // Try public disk
        $publicOldPath = public_path($oldPath);
        if (File::exists($publicOldPath)) {
            return $this->movePublicFile($oldPath, $newPath);
        }

        return false;
    }

    /**
     * Move file in storage disk
     */
    private function moveStorageFile(string $oldPath, string $newPath): bool
    {
        if ($this->option('dry-run')) {
            return true;
        }

        try {
            // Create directory if not exists
            $newDir = dirname($newPath);
            if (!Storage::disk('public')->exists($newDir)) {
                Storage::disk('public')->makeDirectory($newDir);
            }

            // Move file
            return Storage::disk('public')->move($oldPath, $newPath);
        } catch (\Exception $e) {
            $this->error("Failed to move {$oldPath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Move file in public directory
     */
    private function movePublicFile(string $oldPath, string $newPath): bool
    {
        if ($this->option('dry-run')) {
            return true;
        }

        try {
            $fullOldPath = public_path($oldPath);
            $fullNewPath = public_path($newPath);

            // Create directory if not exists
            $newDir = dirname($fullNewPath);
            if (!File::exists($newDir)) {
                File::makeDirectory($newDir, 0755, true);
            }

            // Move file
            return File::move($fullOldPath, $fullNewPath);
        } catch (\Exception $e) {
            $this->error("Failed to move {$oldPath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Migrate files that don't have database records
     */
    private function migrateOrphanedFiles(): void
    {
        $this->info('🔍 Checking for orphaned files...');
        
        // This is a placeholder - implement based on specific needs
        $this->warn('⚠️ Orphaned file migration not implemented yet');
    }

    /**
     * Display migration results
     */
    private function displayResults(): void
    {
        $this->newLine();
        $this->info('📊 Migration Results:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files', $this->stats['total_files']],
                ['Migrated Successfully', $this->stats['migrated_files']],
                ['Failed', $this->stats['failed_files']],
                ['Skipped', $this->stats['skipped_files']],
            ]
        );

        if ($this->stats['failed_files'] > 0) {
            $this->error('⚠️ Some files failed to migrate. Check the logs above.');
        } else {
            $this->info('✅ Migration completed successfully!');
        }
    }
}
