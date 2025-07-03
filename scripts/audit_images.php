<?php

/**
 * MechaMap Image Audit Script
 * Phân tích và audit hệ thống quản lý hình ảnh
 */

class ImageAuditService
{
    private $publicImagesPath;
    private $auditResults = [];

    public function __construct()
    {
        $this->publicImagesPath = __DIR__ . '/../public/images';
    }

    /**
     * Thực hiện audit toàn bộ hệ thống hình ảnh
     */
    public function performFullAudit()
    {
        echo "🔍 MECHAMAP IMAGE AUDIT REPORT\n";
        echo "=====================================\n\n";

        // 1. Audit cấu trúc thư mục
        $this->auditDirectoryStructure();

        // 2. Phân tích files theo category
        $this->analyzeFilesByCategory();

        // 3. Kiểm tra file sizes
        $this->analyzeFileSizes();

        // 4. Tạo báo cáo tổng kết
        $this->generateSummaryReport();

        return $this->auditResults;
    }

    /**
     * Audit cấu trúc thư mục
     */
    private function auditDirectoryStructure()
    {
        echo "📁 DIRECTORY STRUCTURE ANALYSIS\n";
        echo "--------------------------------\n";

        $directories = [];
        $this->scanDirectory($this->publicImagesPath, $directories);

        foreach ($directories as $dir => $info) {
            echo sprintf("📂 %-30s | %d files | %s\n",
                str_replace($this->publicImagesPath . DIRECTORY_SEPARATOR, '', $dir),
                $info['file_count'],
                $this->formatBytes($info['total_size'])
            );
        }

        $this->auditResults['directories'] = $directories;
        echo "\n";
    }

    /**
     * Scan directory recursively
     */
    private function scanDirectory($path, &$directories, $level = 0)
    {
        if (!is_dir($path) || $level > 3) return;

        $files = scandir($path);
        $fileCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $directories, $level + 1);
            } else {
                $fileCount++;
                $totalSize += filesize($fullPath);
            }
        }

        $directories[$path] = [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'level' => $level
        ];
    }

    /**
     * Phân tích files theo category
     */
    private function analyzeFilesByCategory()
    {
        echo "📊 FILE ANALYSIS BY CATEGORY\n";
        echo "-----------------------------\n";

        $categories = [
            'users' => $this->analyzeUserImages(),
            'showcases' => $this->analyzeShowcaseImages(),
            'threads' => $this->analyzeThreadImages(),
            'categories' => $this->analyzeCategoryImages(),
            'demo' => $this->analyzeDemoImages(),
            'placeholders' => $this->analyzePlaceholderImages(),
            'brand' => $this->analyzeBrandImages(),
            'settings' => $this->analyzeSettingsImages(),
            'root' => $this->analyzeRootImages()
        ];

        foreach ($categories as $category => $data) {
            echo sprintf("🏷️  %-15s | %d files | %s | %s\n",
                ucfirst($category),
                $data['count'],
                $this->formatBytes($data['size']),
                $data['status']
            );
        }

        $this->auditResults['categories'] = $categories;
        echo "\n";
    }

    /**
     * Analyze user images
     */
    private function analyzeUserImages()
    {
        $path = $this->publicImagesPath . '/users';
        return $this->analyzeDirectory($path, 'User avatars - Well organized');
    }

    /**
     * Analyze showcase images
     */
    private function analyzeShowcaseImages()
    {
        $path = $this->publicImagesPath . '/showcase';
        return $this->analyzeDirectory($path, 'Showcase images - Need DB verification');
    }

    /**
     * Analyze thread images
     */
    private function analyzeThreadImages()
    {
        $path = $this->publicImagesPath . '/threads';
        return $this->analyzeDirectory($path, 'Thread images - Need DB verification');
    }

    /**
     * Analyze category images
     */
    private function analyzeCategoryImages()
    {
        $path = $this->publicImagesPath . '/category-forum';
        return $this->analyzeDirectory($path, 'Category icons - System files');
    }

    /**
     * Analyze demo images
     */
    private function analyzeDemoImages()
    {
        $path = $this->publicImagesPath . '/demo';
        return $this->analyzeDirectory($path, 'Demo images - For seeding');
    }

    /**
     * Analyze placeholder images
     */
    private function analyzePlaceholderImages()
    {
        $path = $this->publicImagesPath . '/placeholders';
        return $this->analyzeDirectory($path, 'Placeholder images - System files');
    }

    /**
     * Analyze brand images
     */
    private function analyzeBrandImages()
    {
        $path = $this->publicImagesPath . '/brand';
        return $this->analyzeDirectory($path, 'Brand assets - System files');
    }

    /**
     * Analyze settings images
     */
    private function analyzeSettingsImages()
    {
        $path = $this->publicImagesPath . '/settings';
        return $this->analyzeDirectory($path, 'Settings uploads - Need cleanup');
    }

    /**
     * Analyze root images
     */
    private function analyzeRootImages()
    {
        $files = glob($this->publicImagesPath . '/*');
        $count = 0;
        $size = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $count++;
                $size += filesize($file);
            }
        }

        return [
            'count' => $count,
            'size' => $size,
            'status' => 'Root level files - Need organization'
        ];
    }

    /**
     * Generic directory analyzer
     */
    private function analyzeDirectory($path, $status = 'Unknown')
    {
        if (!is_dir($path)) {
            return ['count' => 0, 'size' => 0, 'status' => 'Directory not found'];
        }

        $files = glob($path . '/*');
        $count = 0;
        $size = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $count++;
                $size += filesize($file);
            }
        }

        return [
            'count' => $count,
            'size' => $size,
            'status' => $status
        ];
    }

    /**
     * Analyze file sizes
     */
    private function analyzeFileSizes()
    {
        echo "📏 FILE SIZE ANALYSIS\n";
        echo "---------------------\n";

        $sizeRanges = [
            'tiny' => ['min' => 0, 'max' => 10240, 'count' => 0, 'size' => 0], // < 10KB
            'small' => ['min' => 10240, 'max' => 102400, 'count' => 0, 'size' => 0], // 10KB - 100KB
            'medium' => ['min' => 102400, 'max' => 1048576, 'count' => 0, 'size' => 0], // 100KB - 1MB
            'large' => ['min' => 1048576, 'max' => 5242880, 'count' => 0, 'size' => 0], // 1MB - 5MB
            'huge' => ['min' => 5242880, 'max' => PHP_INT_MAX, 'count' => 0, 'size' => 0] // > 5MB
        ];

        $this->analyzeSizeRanges($this->publicImagesPath, $sizeRanges);

        foreach ($sizeRanges as $range => $data) {
            echo sprintf("📦 %-10s | %d files | %s\n",
                ucfirst($range),
                $data['count'],
                $this->formatBytes($data['size'])
            );
        }

        $this->auditResults['size_ranges'] = $sizeRanges;
        echo "\n";
    }

    /**
     * Analyze files by size ranges
     */
    private function analyzeSizeRanges($path, &$sizeRanges)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size = $file->getSize();

                foreach ($sizeRanges as $range => &$data) {
                    if ($size >= $data['min'] && $size < $data['max']) {
                        $data['count']++;
                        $data['size'] += $size;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Generate summary report
     */
    private function generateSummaryReport()
    {
        echo "📋 SUMMARY REPORT\n";
        echo "=================\n";

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($this->auditResults['categories'] as $category => $data) {
            $totalFiles += $data['count'];
            $totalSize += $data['size'];
        }

        echo "📊 Total Files: {$totalFiles}\n";
        echo "💾 Total Size: " . $this->formatBytes($totalSize) . "\n";
        echo "📁 Total Directories: " . count($this->auditResults['directories']) . "\n\n";

        echo "🎯 RECOMMENDATIONS:\n";
        echo "- Move settings uploads to proper storage\n";
        echo "- Organize root level files into subdirectories\n";
        echo "- Verify database references for showcase/thread images\n";
        echo "- Implement image optimization for large files\n";
        echo "- Create unified upload directory structure\n\n";
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

// Chạy audit nếu script được gọi trực tiếp
if (php_sapi_name() === 'cli') {
    $audit = new ImageAuditService();
    $results = $audit->performFullAudit();
}
