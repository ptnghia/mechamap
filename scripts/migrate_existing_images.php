<?php

/**
 * Migration Script for Existing Images
 * Move existing images to unified structure and update database
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExistingImagesMigration
{
    private $publicImagesPath;
    private $backupPath;
    private $migrationLog = [];
    
    public function __construct()
    {
        $this->publicImagesPath = __DIR__ . '/../public/images';
        $this->backupPath = __DIR__ . '/../storage/app/backup/images_' . date('Y-m-d_H-i-s');
    }
    
    /**
     * Run migration
     */
    public function migrate()
    {
        echo "🔄 MIGRATING EXISTING IMAGES TO UNIFIED STRUCTURE\n";
        echo "================================================\n\n";
        
        try {
            // 1. Create backup
            $this->createBackup();
            
            // 2. Create unified directory structure
            $this->createUnifiedStructure();
            
            // 3. Migrate showcase images
            $this->migrateShowcaseImages();
            
            // 4. Migrate user avatars
            $this->migrateUserAvatars();
            
            // 5. Migrate thread images
            $this->migrateThreadImages();
            
            // 6. Update database records
            $this->updateDatabaseRecords();
            
            // 7. Generate migration report
            $this->generateMigrationReport();
            
            echo "✅ Migration completed successfully!\n";
            
        } catch (Exception $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            echo "🔄 Restoring from backup...\n";
            $this->restoreFromBackup();
        }
    }
    
    /**
     * Create backup of existing images
     */
    private function createBackup()
    {
        echo "💾 Creating backup...\n";
        
        if (!file_exists(dirname($this->backupPath))) {
            mkdir(dirname($this->backupPath), 0755, true);
        }
        
        $this->copyDirectory($this->publicImagesPath, $this->backupPath);
        echo "  ✅ Backup created: " . $this->backupPath . "\n\n";
    }
    
    /**
     * Create unified directory structure
     */
    private function createUnifiedStructure()
    {
        echo "📁 Creating unified directory structure...\n";
        
        $directories = [
            'users/avatars',
            'threads', 
            'showcases',
            'categories',
            'forums',
            'temp',
            'demo',
            'placeholders',
            'brand'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = $this->publicImagesPath . '/' . $dir;
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                echo "  📂 Created: {$dir}\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Migrate showcase images
     */
    private function migrateShowcaseImages()
    {
        echo "🏆 Migrating showcase images...\n";
        
        $showcaseDir = $this->publicImagesPath . '/showcase';
        $targetDir = $this->publicImagesPath . '/showcases';
        
        if (file_exists($showcaseDir)) {
            $files = glob($showcaseDir . '/*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    $targetPath = $targetDir . '/' . $filename;
                    
                    if (!file_exists($targetPath)) {
                        copy($file, $targetPath);
                        $this->migrationLog[] = [
                            'action' => 'move',
                            'from' => $file,
                            'to' => $targetPath,
                            'category' => 'showcase'
                        ];
                        echo "  📄 Moved: {$filename}\n";
                    }
                }
            }
        }
        echo "\n";
    }
    
    /**
     * Migrate user avatars
     */
    private function migrateUserAvatars()
    {
        echo "👤 Migrating user avatars...\n";
        
        $usersDir = $this->publicImagesPath . '/users';
        $targetDir = $this->publicImagesPath . '/users/avatars';
        
        if (file_exists($usersDir)) {
            $files = glob($usersDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            
            foreach ($files as $file) {
                $filename = basename($file);
                $targetPath = $targetDir . '/' . $filename;
                
                if (!file_exists($targetPath)) {
                    copy($file, $targetPath);
                    $this->migrationLog[] = [
                        'action' => 'move',
                        'from' => $file,
                        'to' => $targetPath,
                        'category' => 'user_avatar'
                    ];
                    echo "  👤 Moved: {$filename}\n";
                }
            }
        }
        echo "\n";
    }
    
    /**
     * Migrate thread images
     */
    private function migrateThreadImages()
    {
        echo "📝 Migrating thread images...\n";
        
        $threadsDir = $this->publicImagesPath . '/threads';
        
        if (file_exists($threadsDir)) {
            $files = glob($threadsDir . '/*');
            $moved = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    // Thread images are already in correct location
                    $this->migrationLog[] = [
                        'action' => 'keep',
                        'path' => $file,
                        'category' => 'thread'
                    ];
                    $moved++;
                }
            }
            
            echo "  📝 Verified: {$moved} thread images\n";
        }
        echo "\n";
    }
    
    /**
     * Update database records
     */
    private function updateDatabaseRecords()
    {
        echo "🗄️  Updating database records...\n";
        
        // Update showcase cover_image paths
        $this->updateShowcasePaths();
        
        // Update user avatar paths
        $this->updateUserAvatarPaths();
        
        // Update media table paths
        $this->updateMediaPaths();
        
        echo "\n";
    }
    
    /**
     * Update showcase paths
     */
    private function updateShowcasePaths()
    {
        $showcases = DB::table('showcases')
            ->where('cover_image', 'like', '/images/showcase/%')
            ->get();
        
        foreach ($showcases as $showcase) {
            $newPath = str_replace('/images/showcase/', '/images/showcases/', $showcase->cover_image);
            
            DB::table('showcases')
                ->where('id', $showcase->id)
                ->update(['cover_image' => $newPath]);
            
            echo "  🏆 Updated showcase #{$showcase->id} cover_image\n";
        }
    }
    
    /**
     * Update user avatar paths
     */
    private function updateUserAvatarPaths()
    {
        $users = DB::table('users')
            ->where('avatar', 'like', '/images/users/%')
            ->whereNotLike('avatar', '/images/users/avatars/%')
            ->get();
        
        foreach ($users as $user) {
            $filename = basename($user->avatar);
            $newPath = '/images/users/avatars/' . $filename;
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['avatar' => $newPath]);
            
            echo "  👤 Updated user #{$user->id} avatar\n";
        }
    }
    
    /**
     * Update media paths
     */
    private function updateMediaPaths()
    {
        $mediaRecords = DB::table('media')
            ->where('file_path', 'like', '/images/showcase/%')
            ->get();
        
        foreach ($mediaRecords as $media) {
            $newPath = str_replace('/images/showcase/', '/images/showcases/', $media->file_path);
            
            DB::table('media')
                ->where('id', $media->id)
                ->update(['file_path' => $newPath]);
            
            echo "  📄 Updated media #{$media->id} file_path\n";
        }
    }
    
    /**
     * Generate migration report
     */
    private function generateMigrationReport()
    {
        echo "📋 Generating migration report...\n";
        
        $report = [
            'migration_date' => date('Y-m-d H:i:s'),
            'backup_location' => $this->backupPath,
            'actions' => $this->migrationLog,
            'summary' => [
                'total_files' => count($this->migrationLog),
                'moved_files' => count(array_filter($this->migrationLog, fn($log) => $log['action'] === 'move')),
                'kept_files' => count(array_filter($this->migrationLog, fn($log) => $log['action'] === 'keep'))
            ]
        ];
        
        $reportPath = __DIR__ . '/migration_report_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        
        echo "  📄 Report saved: " . basename($reportPath) . "\n";
        echo "  📊 Total files processed: " . $report['summary']['total_files'] . "\n";
        echo "  📦 Files moved: " . $report['summary']['moved_files'] . "\n";
        echo "  ✅ Files kept in place: " . $report['summary']['kept_files'] . "\n\n";
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!file_exists($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }
    
    /**
     * Restore from backup
     */
    private function restoreFromBackup()
    {
        if (file_exists($this->backupPath)) {
            $this->copyDirectory($this->backupPath, $this->publicImagesPath);
            echo "✅ Restored from backup successfully\n";
        }
    }
}

// Run migration if script is called directly
if (php_sapi_name() === 'cli') {
    $migration = new ExistingImagesMigration();
    $migration->migrate();
}
