<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;

class ComprehensiveValidator
{
    private $basePath;
    private $viTranslator;
    private $enTranslator;
    private $issues = [];
    private $stats = [
        'total_files' => 0,
        'files_with_translations' => 0,
        'total_keys_found' => 0,
        'missing_vi_keys' => 0,
        'missing_en_keys' => 0,
        'raw_keys' => 0,
    ];

    public function __construct()
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/lang');
        
        // Initialize translators
        $loader = new FileLoader(new Filesystem(), $this->basePath);
        $this->viTranslator = new Translator($loader, 'vi');
        $this->enTranslator = new Translator($loader, 'en');
    }

    public function validate()
    {
        echo "🔍 COMPREHENSIVE LANGUAGE VALIDATION\n";
        echo "====================================\n\n";

        $this->validateCriticalPages();
        $this->validateAllViewFiles();
        $this->validateLanguageFiles();
        $this->printDetailedReport();
    }

    private function validateCriticalPages()
    {
        echo "📋 Validating Critical Pages...\n";
        
        $criticalPages = [
            'resources/views/home.blade.php' => 'Home Page',
            'resources/views/components/header.blade.php' => 'Header Navigation',
            'resources/views/components/auth-modal.blade.php' => 'Authentication Modal',
            'resources/views/threads/index.blade.php' => 'Forum Threads',
            'resources/views/showcase/public.blade.php' => 'Public Showcases',
            'resources/views/marketplace/products/index.blade.php' => 'Marketplace',
        ];

        foreach ($criticalPages as $file => $description) {
            $fullPath = realpath(__DIR__ . '/../../' . $file);
            if (file_exists($fullPath)) {
                echo "  📄 $description: ";
                $issues = $this->validateFile($fullPath);
                if (empty($issues)) {
                    echo "✅ OK\n";
                } else {
                    echo "❌ " . count($issues) . " issues\n";
                    $this->issues[$file] = $issues;
                }
            } else {
                echo "  📄 $description: ⚠️  File not found\n";
            }
        }
        echo "\n";
    }

    private function validateAllViewFiles()
    {
        echo "📁 Scanning All View Files...\n";
        
        $viewsPath = realpath(__DIR__ . '/../../resources/views');
        $files = $this->getAllPhpFiles($viewsPath);
        
        $this->stats['total_files'] = count($files);
        
        foreach ($files as $file) {
            $issues = $this->validateFile($file);
            if (!empty($issues)) {
                $this->stats['files_with_translations']++;
                $relativePath = str_replace(realpath(__DIR__ . '/../../') . '/', '', $file);
                $this->issues[$relativePath] = $issues;
            }
        }
        
        echo "  📊 Scanned {$this->stats['total_files']} files\n";
        echo "  📊 {$this->stats['files_with_translations']} files have translation keys\n\n";
    }

    private function validateFile($filePath)
    {
        $content = file_get_contents($filePath);
        $issues = [];
        
        // Find all translation keys
        preg_match_all("/__\('([^']+)'\)/", $content, $matches);
        preg_match_all("/@lang\('([^']+)'\)/", $content, $langMatches);
        
        $allKeys = array_merge($matches[1] ?? [], $langMatches[1] ?? []);
        $this->stats['total_keys_found'] += count($allKeys);
        
        foreach ($allKeys as $key) {
            // Check if key exists in Vietnamese
            $viValue = $this->viTranslator->get($key);
            if ($viValue === $key) {
                $issues[] = "Missing VI: $key";
                $this->stats['missing_vi_keys']++;
            }
            
            // Check if key exists in English
            $enValue = $this->enTranslator->get($key);
            if ($enValue === $key) {
                $issues[] = "Missing EN: $key";
                $this->stats['missing_en_keys']++;
            }
            
            // Check for raw keys (keys that look like they should be translated)
            if (preg_match('/^[a-z][a-z_]*\.[a-z_\.]+$/', $key) && ($viValue === $key || $enValue === $key)) {
                $this->stats['raw_keys']++;
            }
        }
        
        return $issues;
    }

    private function validateLanguageFiles()
    {
        echo "🗂️  Validating Language Files...\n";
        
        $viFiles = glob($this->basePath . '/vi/*.php');
        $enFiles = glob($this->basePath . '/en/*.php');
        
        echo "  📊 Vietnamese files: " . count($viFiles) . "\n";
        echo "  📊 English files: " . count($enFiles) . "\n";
        
        // Check for missing English files
        foreach ($viFiles as $viFile) {
            $filename = basename($viFile);
            $enFile = $this->basePath . '/en/' . $filename;
            if (!file_exists($enFile)) {
                $this->issues['missing_en_files'][] = $filename;
            }
        }
        echo "\n";
    }

    private function getAllPhpFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    private function printDetailedReport()
    {
        echo "📊 COMPREHENSIVE VALIDATION REPORT\n";
        echo "==================================\n\n";

        // Statistics
        echo "📈 STATISTICS:\n";
        echo "  • Total files scanned: {$this->stats['total_files']}\n";
        echo "  • Files with translations: {$this->stats['files_with_translations']}\n";
        echo "  • Total translation keys: {$this->stats['total_keys_found']}\n";
        echo "  • Missing Vietnamese keys: {$this->stats['missing_vi_keys']}\n";
        echo "  • Missing English keys: {$this->stats['missing_en_keys']}\n";
        echo "  • Raw/untranslated keys: {$this->stats['raw_keys']}\n\n";

        // Issues summary
        $totalIssues = array_sum(array_map('count', $this->issues));
        
        if ($totalIssues === 0) {
            echo "🎉 NO ISSUES FOUND! All translations are working correctly.\n\n";
        } else {
            echo "⚠️  ISSUES FOUND: $totalIssues total issues\n\n";
            
            // Show top 10 files with most issues
            $fileIssues = [];
            foreach ($this->issues as $file => $issues) {
                if (is_array($issues)) {
                    $fileIssues[$file] = count($issues);
                }
            }
            arsort($fileIssues);
            
            echo "🔥 TOP FILES WITH ISSUES:\n";
            $count = 0;
            foreach ($fileIssues as $file => $issueCount) {
                if ($count >= 10) break;
                echo "  • $file: $issueCount issues\n";
                $count++;
            }
            echo "\n";
        }

        // Health score
        $totalKeys = $this->stats['total_keys_found'];
        $workingKeys = $totalKeys - $this->stats['missing_vi_keys'] - $this->stats['missing_en_keys'];
        $healthScore = $totalKeys > 0 ? round(($workingKeys / $totalKeys) * 100, 1) : 100;
        
        echo "🏥 HEALTH SCORE: $healthScore%\n";
        
        if ($healthScore >= 95) {
            echo "🎉 EXCELLENT! Language system is in great shape.\n";
        } elseif ($healthScore >= 85) {
            echo "✅ GOOD! Minor issues to address.\n";
        } elseif ($healthScore >= 70) {
            echo "⚠️  FAIR! Some attention needed.\n";
        } else {
            echo "🚨 POOR! Significant issues require immediate attention.\n";
        }
        
        echo "\n💡 RECOMMENDATIONS:\n";
        if ($this->stats['missing_en_keys'] > 0) {
            echo "  • Add missing English translations\n";
        }
        if ($this->stats['missing_vi_keys'] > 0) {
            echo "  • Add missing Vietnamese translations\n";
        }
        if ($this->stats['raw_keys'] > 0) {
            echo "  • Review and fix raw/untranslated keys\n";
        }
        echo "  • Test language switching functionality\n";
        echo "  • Run regular validation checks\n";
    }
}

$validator = new ComprehensiveValidator();
$validator->validate();
