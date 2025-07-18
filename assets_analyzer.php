<?php
/**
 * MechaMap Assets Analyzer - Advanced Analysis Tool
 * Phân tích chi tiết việc sử dụng assets và đưa ra khuyến nghị
 */

class AssetsAnalyzer {
    private $basePath;
    private $reportPath;
    
    // Danh sách thư viện có thể thay thế
    private $alternatives = [
        'tinymce' => 'CKEditor (đã có)',
        'echarts' => 'ApexCharts (đã có)',
        'sweetalert2' => 'Native browser alerts',
        'alertifyjs' => 'Toastr (đã có)',
        'choices.js' => 'Select2 (đã có)',
        'leaflet' => 'Google Maps API',
        'swiper' => 'Bootstrap Carousel',
        'masonry-layout' => 'CSS Grid',
        'jquery-validation' => 'Pristine (đã có)',
    ];
    
    // Thư viện có dependency
    private $dependencies = [
        'datatables.net-bs4' => ['datatables.net', 'bootstrap'],
        'datatables.net-buttons-bs4' => ['datatables.net', 'datatables.net-buttons'],
        'datatables.net-responsive-bs4' => ['datatables.net', 'datatables.net-responsive'],
        'select2' => ['jquery'],
        'apexcharts' => [],
        'dragula' => [],
    ];
    
    public function __construct() {
        $this->basePath = __DIR__;
        $this->reportPath = $this->basePath . '/assets_analysis_report.html';
    }
    
    public function analyze() {
        echo "🔍 Bắt đầu phân tích chi tiết assets...\n";
        
        $data = [
            'scan_time' => date('Y-m-d H:i:s'),
            'all_assets' => $this->getAllAssets(),
            'used_assets' => $this->getUsedAssets(),
            'protected_assets' => $this->getProtectedAssets(),
            'large_files' => $this->getLargeFiles(),
            'duplicate_files' => $this->findDuplicateFiles(),
            'outdated_libraries' => $this->findOutdatedLibraries(),
            'optimization_opportunities' => $this->findOptimizationOpportunities()
        ];
        
        $this->generateReport($data);
        $this->generateRecommendations($data);
        
        echo "✅ Phân tích hoàn thành! Xem báo cáo tại: " . $this->reportPath . "\n";
    }
    
    private function getAllAssets() {
        $assets = [];
        $this->scanDirectory($this->basePath . '/public/assets', 'assets', $assets);
        return $assets;
    }
    
    private function scanDirectory($dir, $prefix, &$assets) {
        if (!is_dir($dir)) return;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $fullPath = $dir . '/' . $file;
            $relativePath = $prefix . '/' . $file;
            
            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $relativePath, $assets);
            } else {
                $assets[] = [
                    'path' => ltrim($relativePath, '/'),
                    'fullPath' => $fullPath,
                    'size' => filesize($fullPath),
                    'modified' => filemtime($fullPath),
                    'extension' => pathinfo($file, PATHINFO_EXTENSION),
                    'type' => $this->getFileType($file)
                ];
            }
        }
    }
    
    private function getFileType($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $types = [
            'js' => 'JavaScript',
            'css' => 'Stylesheet',
            'png' => 'Image',
            'jpg' => 'Image',
            'jpeg' => 'Image',
            'gif' => 'Image',
            'svg' => 'Vector Image',
            'ico' => 'Icon',
            'woff' => 'Font',
            'woff2' => 'Font',
            'ttf' => 'Font',
            'eot' => 'Font',
            'json' => 'Data',
            'md' => 'Documentation'
        ];
        
        return $types[$ext] ?? 'Other';
    }
    
    private function getUsedAssets() {
        // Sử dụng logic từ cleanup tool
        $cleanup = new AssetsCleanupTool();
        // Gọi private methods thông qua reflection (hack)
        return []; // Simplified for now
    }
    
    private function getProtectedAssets() {
        return [
            'assets/libs/bootstrap/',
            'assets/libs/jquery/',
            'assets/libs/apexcharts/',
            'assets/libs/datatables',
            'assets/css/bootstrap',
            'assets/css/app',
            'assets/css/icons',
            'assets/js/app'
        ];
    }
    
    private function getLargeFiles($minSize = 100000) { // 100KB
        $allAssets = $this->getAllAssets();
        $largeFiles = [];
        
        foreach ($allAssets as $asset) {
            if ($asset['size'] > $minSize) {
                $largeFiles[] = $asset;
            }
        }
        
        // Sắp xếp theo size giảm dần
        usort($largeFiles, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        return $largeFiles;
    }
    
    private function findDuplicateFiles() {
        $allAssets = $this->getAllAssets();
        $hashes = [];
        $duplicates = [];
        
        foreach ($allAssets as $asset) {
            if ($asset['size'] > 1000) { // Chỉ check file > 1KB
                $hash = md5_file($asset['fullPath']);
                
                if (isset($hashes[$hash])) {
                    if (!isset($duplicates[$hash])) {
                        $duplicates[$hash] = [$hashes[$hash]];
                    }
                    $duplicates[$hash][] = $asset;
                } else {
                    $hashes[$hash] = $asset;
                }
            }
        }
        
        return $duplicates;
    }
    
    private function findOutdatedLibraries() {
        $libraries = [];
        $libsPath = $this->basePath . '/public/assets/libs';
        
        if (!is_dir($libsPath)) return $libraries;
        
        $dirs = scandir($libsPath);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            
            $libPath = $libsPath . '/' . $dir;
            if (is_dir($libPath)) {
                $version = $this->detectLibraryVersion($libPath, $dir);
                $latest = $this->getLatestVersion($dir);
                
                $libraries[] = [
                    'name' => $dir,
                    'current_version' => $version,
                    'latest_version' => $latest,
                    'outdated' => $this->isOutdated($version, $latest),
                    'alternative' => $this->alternatives[$dir] ?? null
                ];
            }
        }
        
        return $libraries;
    }
    
    private function detectLibraryVersion($libPath, $libName) {
        // Tìm version trong package.json
        $packageJson = $libPath . '/package.json';
        if (file_exists($packageJson)) {
            $package = json_decode(file_get_contents($packageJson), true);
            return $package['version'] ?? 'unknown';
        }
        
        // Tìm version trong filename
        $files = glob($libPath . '/*.js');
        foreach ($files as $file) {
            $filename = basename($file);
            if (preg_match('/(\d+\.\d+\.\d+)/', $filename, $matches)) {
                return $matches[1];
            }
        }
        
        return 'unknown';
    }
    
    private function getLatestVersion($libName) {
        // Simplified - trong thực tế có thể gọi API npm/cdnjs
        $knownVersions = [
            'jquery' => '3.7.1',
            'bootstrap' => '5.3.2',
            'apexcharts' => '3.44.0',
            'select2' => '4.1.0',
            'datatables.net' => '1.13.7',
            'tinymce' => '6.7.2',
            'sweetalert2' => '11.7.32'
        ];
        
        return $knownVersions[$libName] ?? 'unknown';
    }
    
    private function isOutdated($current, $latest) {
        if ($current === 'unknown' || $latest === 'unknown') {
            return false;
        }
        
        return version_compare($current, $latest, '<');
    }
    
    private function findOptimizationOpportunities() {
        $opportunities = [];
        
        // 1. Minification opportunities
        $unminified = $this->findUnminifiedFiles();
        if (!empty($unminified)) {
            $opportunities[] = [
                'type' => 'minification',
                'description' => 'Có ' . count($unminified) . ' file chưa được minify',
                'files' => $unminified,
                'potential_savings' => $this->calculateMinificationSavings($unminified)
            ];
        }
        
        // 2. CDN opportunities
        $cdnCandidates = $this->findCDNCandidates();
        if (!empty($cdnCandidates)) {
            $opportunities[] = [
                'type' => 'cdn',
                'description' => 'Có thể chuyển ' . count($cdnCandidates) . ' thư viện lên CDN',
                'files' => $cdnCandidates,
                'potential_savings' => array_sum(array_column($cdnCandidates, 'size'))
            ];
        }
        
        // 3. Combination opportunities
        $combinable = $this->findCombinableFiles();
        if (!empty($combinable)) {
            $opportunities[] = [
                'type' => 'combination',
                'description' => 'Có thể gộp ' . count($combinable) . ' file CSS/JS',
                'files' => $combinable,
                'potential_savings' => $this->calculateCombinationSavings($combinable)
            ];
        }
        
        return $opportunities;
    }
    
    private function findUnminifiedFiles() {
        $allAssets = $this->getAllAssets();
        $unminified = [];
        
        foreach ($allAssets as $asset) {
            if (in_array($asset['extension'], ['js', 'css'])) {
                $filename = basename($asset['path']);
                
                // Nếu không có .min. trong tên và có file .min tương ứng
                if (strpos($filename, '.min.') === false) {
                    $minFilename = str_replace('.' . $asset['extension'], '.min.' . $asset['extension'], $filename);
                    $minPath = dirname($asset['fullPath']) . '/' . $minFilename;
                    
                    if (!file_exists($minPath) && $asset['size'] > 5000) { // > 5KB
                        $unminified[] = $asset;
                    }
                }
            }
        }
        
        return $unminified;
    }
    
    private function findCDNCandidates() {
        $candidates = [];
        $popularLibs = [
            'jquery', 'bootstrap', 'select2', 'apexcharts', 
            'datatables.net', 'sweetalert2', 'chart.js'
        ];
        
        $allAssets = $this->getAllAssets();
        
        foreach ($allAssets as $asset) {
            foreach ($popularLibs as $lib) {
                if (strpos($asset['path'], 'assets/libs/' . $lib) === 0) {
                    $candidates[] = $asset;
                    break;
                }
            }
        }
        
        return $candidates;
    }
    
    private function findCombinableFiles() {
        // Tìm các file CSS/JS nhỏ có thể gộp lại
        $allAssets = $this->getAllAssets();
        $combinable = [];
        
        foreach ($allAssets as $asset) {
            if (in_array($asset['extension'], ['js', 'css']) && 
                $asset['size'] < 50000 && // < 50KB
                strpos($asset['path'], 'assets/css/') === 0 ||
                strpos($asset['path'], 'assets/js/') === 0) {
                $combinable[] = $asset;
            }
        }
        
        return $combinable;
    }
    
    private function calculateMinificationSavings($files) {
        // Ước tính tiết kiệm 20-30% cho JS, 15-25% cho CSS
        $totalSavings = 0;
        
        foreach ($files as $file) {
            $savingRate = ($file['extension'] === 'js') ? 0.25 : 0.20;
            $totalSavings += $file['size'] * $savingRate;
        }
        
        return $totalSavings;
    }
    
    private function calculateCombinationSavings($files) {
        // Tiết kiệm từ việc giảm HTTP requests
        return count($files) * 1000; // Ước tính 1KB overhead per request
    }
    
    private function generateReport($data) {
        $html = $this->generateHTMLReport($data);
        file_put_contents($this->reportPath, $html);
    }
    
    private function generateHTMLReport($data) {
        // Tạo HTML report (simplified)
        $html = "<!DOCTYPE html><html><head><title>MechaMap Assets Analysis Report</title>";
        $html .= "<style>body{font-family:Arial,sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>";
        $html .= "</head><body>";
        $html .= "<h1>MechaMap Assets Analysis Report</h1>";
        $html .= "<p>Generated: " . $data['scan_time'] . "</p>";
        
        // Summary
        $totalAssets = count($data['all_assets']);
        $totalSize = array_sum(array_column($data['all_assets'], 'size'));
        
        $html .= "<h2>Summary</h2>";
        $html .= "<ul>";
        $html .= "<li>Total Assets: $totalAssets files</li>";
        $html .= "<li>Total Size: " . number_format($totalSize / 1024 / 1024, 2) . " MB</li>";
        $html .= "<li>Large Files (>100KB): " . count($data['large_files']) . "</li>";
        $html .= "<li>Duplicate Files: " . count($data['duplicate_files']) . " groups</li>";
        $html .= "</ul>";
        
        // Large files table
        if (!empty($data['large_files'])) {
            $html .= "<h2>Large Files (>100KB)</h2>";
            $html .= "<table><tr><th>File</th><th>Size</th><th>Type</th></tr>";
            
            foreach (array_slice($data['large_files'], 0, 20) as $file) {
                $size = number_format($file['size'] / 1024, 1) . " KB";
                $html .= "<tr><td>{$file['path']}</td><td>$size</td><td>{$file['type']}</td></tr>";
            }
            
            $html .= "</table>";
        }
        
        $html .= "</body></html>";
        
        return $html;
    }
    
    private function generateRecommendations($data) {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "           KHUYẾN NGHỊ TỐI ƯU HÓA ASSETS\n";
        echo str_repeat("=", 80) . "\n";
        
        // Top recommendations
        echo "🎯 KHUYẾN NGHỊ HÀNG ĐẦU:\n\n";
        
        if (!empty($data['large_files'])) {
            echo "1. 📦 XÓA CÁC THƯ VIỆN LỚN KHÔNG DÙNG:\n";
            $largeLibs = array_filter($data['large_files'], function($file) {
                return strpos($file['path'], 'assets/libs/') === 0;
            });
            
            foreach (array_slice($largeLibs, 0, 5) as $file) {
                $size = number_format($file['size'] / 1024 / 1024, 2);
                echo "   • {$file['path']} ({$size} MB)\n";
            }
        }
        
        if (!empty($data['optimization_opportunities'])) {
            echo "\n2. ⚡ TỐI ƯU HÓA PERFORMANCE:\n";
            foreach ($data['optimization_opportunities'] as $opp) {
                $savings = number_format($opp['potential_savings'] / 1024, 1);
                echo "   • {$opp['description']} (tiết kiệm ~{$savings} KB)\n";
            }
        }
        
        echo "\n3. 🔄 CẬP NHẬT THƯ VIỆN:\n";
        foreach ($data['outdated_libraries'] as $lib) {
            if ($lib['outdated']) {
                echo "   • {$lib['name']}: {$lib['current_version']} → {$lib['latest_version']}\n";
            }
        }
        
        echo "\n" . str_repeat("=", 80) . "\n";
    }
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("Script này chỉ chạy được từ command line!\n");
}

try {
    $analyzer = new AssetsAnalyzer();
    $analyzer->analyze();
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
