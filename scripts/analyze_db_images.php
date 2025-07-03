<?php

/**
 * Database Image Reference Analyzer
 * Phân tích database dump để tìm image references
 */

class DatabaseImageAnalyzer
{
    private $dumpFile;
    private $publicImagesPath;
    private $results = [];
    
    public function __construct()
    {
        $this->dumpFile = __DIR__ . '/../v8.sql';
        $this->publicImagesPath = __DIR__ . '/../public/images';
    }
    
    /**
     * Thực hiện phân tích toàn bộ
     */
    public function performAnalysis()
    {
        echo "🔍 DATABASE IMAGE REFERENCE ANALYSIS\n";
        echo "====================================\n\n";
        
        // 1. Phân tích image references trong database
        $this->analyzeImageReferences();
        
        // 2. Tìm orphaned files
        $this->findOrphanedFiles();
        
        // 3. Tạo báo cáo cleanup
        $this->generateCleanupReport();
        
        return $this->results;
    }
    
    /**
     * Phân tích image references trong database dump
     */
    private function analyzeImageReferences()
    {
        echo "📊 ANALYZING DATABASE IMAGE REFERENCES\n";
        echo "--------------------------------------\n";
        
        if (!file_exists($this->dumpFile)) {
            echo "❌ Database dump file not found: {$this->dumpFile}\n";
            return;
        }
        
        $content = file_get_contents($this->dumpFile);
        
        // Patterns để tìm image references
        $patterns = [
            'images_path' => '/\/images\/[^"\'\\s]+\.(jpg|jpeg|png|gif|webp|svg)/i',
            'storage_images' => '/storage\/images\/[^"\'\\s]+\.(jpg|jpeg|png|gif|webp|svg)/i',
            'cover_image' => '/cover_image["\']:\s*["\']([^"\']+)/i',
            'image_gallery' => '/image_gallery["\']:\s*["\']([^"\']+)/i',
            'avatar' => '/avatar["\']:\s*["\']([^"\']+)/i',
            'file_path' => '/file_path["\']:\s*["\']([^"\']+\.(jpg|jpeg|png|gif|webp|svg))/i'
        ];
        
        $foundReferences = [];
        
        foreach ($patterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            
            if (!empty($matches[0])) {
                $foundReferences[$type] = array_unique($matches[0]);
                echo sprintf("🔗 %-15s | %d references\n", ucfirst($type), count($foundReferences[$type]));
            }
        }
        
        $this->results['database_references'] = $foundReferences;
        echo "\n";
    }
    
    /**
     * Tìm orphaned files
     */
    private function findOrphanedFiles()
    {
        echo "🗑️  FINDING ORPHANED FILES\n";
        echo "-------------------------\n";
        
        // Lấy tất cả files trong public/images
        $allFiles = $this->getAllImageFiles();
        
        // Lấy tất cả references từ database
        $referencedFiles = $this->extractReferencedFiles();
        
        // Tìm orphaned files
        $orphanedFiles = [];
        $systemFiles = [
            'default-avatar.svg', 'placeholder.svg', 'logo.svg', 'favicon.svg',
            'hero-bg.svg', 'city-illustration.svg', 'moon.svg', 'sun.svg',
            'no-image.svg', 'avata.jpg', 'post.jpg'
        ];
        
        foreach ($allFiles as $file) {
            $relativePath = str_replace($this->publicImagesPath . DIRECTORY_SEPARATOR, '', $file);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Skip system files
            $fileName = basename($file);
            if (in_array($fileName, $systemFiles)) {
                continue;
            }
            
            // Skip placeholders directory
            if (strpos($relativePath, 'placeholders/') === 0) {
                continue;
            }
            
            // Skip brand directory
            if (strpos($relativePath, 'brand/') === 0) {
                continue;
            }
            
            // Skip category-forum directory
            if (strpos($relativePath, 'category-forum/') === 0) {
                continue;
            }
            
            // Check if file is referenced in database
            $isReferenced = false;
            foreach ($referencedFiles as $ref) {
                if (strpos($ref, $relativePath) !== false || strpos($ref, $fileName) !== false) {
                    $isReferenced = true;
                    break;
                }
            }
            
            if (!$isReferenced) {
                $orphanedFiles[] = [
                    'path' => $file,
                    'relative_path' => $relativePath,
                    'size' => filesize($file),
                    'category' => $this->categorizeFile($relativePath)
                ];
            }
        }
        
        // Group by category
        $orphanedByCategory = [];
        foreach ($orphanedFiles as $file) {
            $category = $file['category'];
            if (!isset($orphanedByCategory[$category])) {
                $orphanedByCategory[$category] = [];
            }
            $orphanedByCategory[$category][] = $file;
        }
        
        foreach ($orphanedByCategory as $category => $files) {
            $totalSize = array_sum(array_column($files, 'size'));
            echo sprintf("📁 %-15s | %d files | %s\n", 
                ucfirst($category), 
                count($files), 
                $this->formatBytes($totalSize)
            );
        }
        
        $this->results['orphaned_files'] = $orphanedByCategory;
        echo "\n";
    }
    
    /**
     * Lấy tất cả image files
     */
    private function getAllImageFiles()
    {
        $files = [];
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->publicImagesPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, $extensions)) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Extract referenced files từ database
     */
    private function extractReferencedFiles()
    {
        $references = [];
        
        if (isset($this->results['database_references'])) {
            foreach ($this->results['database_references'] as $type => $refs) {
                foreach ($refs as $ref) {
                    // Clean up reference
                    $cleaned = preg_replace('/["\']/', '', $ref);
                    $cleaned = preg_replace('/.*?\/images\//', '', $cleaned);
                    $references[] = $cleaned;
                }
            }
        }
        
        return array_unique($references);
    }
    
    /**
     * Categorize file based on path
     */
    private function categorizeFile($path)
    {
        if (strpos($path, 'users/') === 0) return 'users';
        if (strpos($path, 'showcase/') === 0) return 'showcase';
        if (strpos($path, 'threads/') === 0) return 'threads';
        if (strpos($path, 'demo/') === 0) return 'demo';
        if (strpos($path, 'settings/') === 0) return 'settings';
        if (strpos($path, 'setting/') === 0) return 'settings';
        return 'root';
    }
    
    /**
     * Generate cleanup report
     */
    private function generateCleanupReport()
    {
        echo "📋 CLEANUP RECOMMENDATIONS\n";
        echo "==========================\n";
        
        $totalOrphaned = 0;
        $totalSize = 0;
        
        if (isset($this->results['orphaned_files'])) {
            foreach ($this->results['orphaned_files'] as $category => $files) {
                $totalOrphaned += count($files);
                $totalSize += array_sum(array_column($files, 'size'));
            }
        }
        
        echo "📊 Total orphaned files: {$totalOrphaned}\n";
        echo "💾 Total size to cleanup: " . $this->formatBytes($totalSize) . "\n\n";
        
        echo "🎯 RECOMMENDED ACTIONS:\n";
        echo "1. ✅ Keep: System files (placeholders, brand, category-forum)\n";
        echo "2. ✅ Keep: Demo files (for seeding)\n";
        echo "3. ⚠️  Review: Settings uploads (move to proper storage)\n";
        echo "4. ❌ Delete: Orphaned showcase images\n";
        echo "5. ❌ Delete: Orphaned thread images\n";
        echo "6. ❌ Delete: Orphaned user avatars\n";
        echo "7. 🔧 Migrate: Referenced images to media table\n\n";
        
        // Generate cleanup script
        $this->generateCleanupScript();
    }
    
    /**
     * Generate cleanup script
     */
    private function generateCleanupScript()
    {
        $script = "#!/bin/bash\n";
        $script .= "# MechaMap Image Cleanup Script\n";
        $script .= "# Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        $script .= "echo \"🧹 Starting MechaMap image cleanup...\"\n\n";
        
        if (isset($this->results['orphaned_files'])) {
            foreach ($this->results['orphaned_files'] as $category => $files) {
                if ($category === 'demo') continue; // Keep demo files
                
                $script .= "echo \"Cleaning up {$category} files...\"\n";
                foreach ($files as $file) {
                    $script .= "rm -f \"" . $file['path'] . "\"\n";
                }
                $script .= "\n";
            }
        }
        
        $script .= "echo \"✅ Cleanup completed!\"\n";
        
        file_put_contents(__DIR__ . '/cleanup_images.sh', $script);
        echo "📝 Cleanup script generated: scripts/cleanup_images.sh\n";
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Chạy analysis nếu script được gọi trực tiếp
if (php_sapi_name() === 'cli') {
    $analyzer = new DatabaseImageAnalyzer();
    $results = $analyzer->performAnalysis();
}
