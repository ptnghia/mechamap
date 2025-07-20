<?php
/**
 * Blade Audit Toolkit
 * Comprehensive tool for auditing and standardizing localization in Blade templates
 */

class BladeAuditToolkit {
    
    private $basePath;
    private $langNewPath;
    private $auditResults = [];
    private $hardcodedPatterns = [];
    private $translationKeyPatterns = [];
    
    public function __construct() {
        $this->basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
        $this->langNewPath = $this->basePath . '/resources/lang_new';
        $this->initializePatterns();
    }
    
    private function initializePatterns() {
        // Patterns to detect hardcoded text (Vietnamese and English)
        $this->hardcodedPatterns = [
            // Vietnamese text patterns
            '/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/',
            // English sentences (3+ words with spaces)
            '/["\']([A-Z][a-zA-Z\s]{10,})["\']/',
            // Common UI text patterns
            '/["\']([A-Z][a-z]+\s+[A-Z][a-z]+)["\']/',
            // Button/action text
            '/["\']([A-Z][a-z]+)["\'](?=\s*>|\s*}}|\s*\)|\s*,)/',
        ];
        
        // Patterns to detect existing translation keys
        $this->translationKeyPatterns = [
            // Laravel translation functions
            '/__\(["\']([^"\']+)["\']\)/',
            '/trans\(["\']([^"\']+)["\']\)/',
            '/@lang\(["\']([^"\']+)["\']\)/',
            // Blade directives
            '/@(core|ui|content|feature|user|admin)\(["\']([^"\']+)["\']\)/',
            // Helper functions
            '/t_(core|ui|content|feature|user|admin)\(["\']([^"\']+)["\']\)/',
        ];
    }
    
    public function auditDirectory($directory) {
        echo "🔍 Auditing directory: $directory\n";
        
        $fullPath = $this->basePath . '/resources/views/' . $directory;
        if (!is_dir($fullPath)) {
            echo "   ❌ Directory not found: $fullPath\n";
            return [];
        }
        
        $results = [
            'directory' => $directory,
            'files_processed' => 0,
            'hardcoded_texts' => [],
            'existing_keys' => [],
            'missing_translations' => [],
            'recommendations' => []
        ];
        
        $bladeFiles = $this->findBladeFiles($fullPath);
        
        foreach ($bladeFiles as $file) {
            $fileResults = $this->auditFile($file);
            $results['files_processed']++;
            $results['hardcoded_texts'] = array_merge($results['hardcoded_texts'], $fileResults['hardcoded']);
            $results['existing_keys'] = array_merge($results['existing_keys'], $fileResults['existing_keys']);
        }
        
        // Remove duplicates
        $results['hardcoded_texts'] = array_unique($results['hardcoded_texts']);
        $results['existing_keys'] = array_unique($results['existing_keys']);
        
        // Generate recommendations
        $results['recommendations'] = $this->generateRecommendations($results, $directory);
        
        echo "   ✅ Processed {$results['files_processed']} files\n";
        echo "   📝 Found " . count($results['hardcoded_texts']) . " hardcoded texts\n";
        echo "   🔑 Found " . count($results['existing_keys']) . " existing keys\n";
        
        return $results;
    }
    
    private function findBladeFiles($directory) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                strpos($file->getFilename(), '.blade.') !== false) {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
    
    private function auditFile($filePath) {
        $content = file_get_contents($filePath);
        $results = [
            'file' => $filePath,
            'hardcoded' => [],
            'existing_keys' => []
        ];
        
        // Find hardcoded texts
        foreach ($this->hardcodedPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    // Filter out obvious non-text content
                    if ($this->isLikelyText($match)) {
                        $results['hardcoded'][] = trim($match);
                    }
                }
            }
        }
        
        // Find existing translation keys
        foreach ($this->translationKeyPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $key) {
                        $results['existing_keys'][] = $key;
                    }
                }
                if (isset($matches[2])) {
                    foreach ($matches[2] as $key) {
                        $results['existing_keys'][] = $key;
                    }
                }
            }
        }
        
        return $results;
    }
    
    private function isLikelyText($text) {
        // Filter out obvious non-text content
        $excludePatterns = [
            '/^[a-z-]+$/',  // CSS classes
            '/^#[0-9a-f]+$/i', // Colors
            '/^\d+$/',      // Numbers only
            '/^[a-z]+\.[a-z]+$/', // File extensions
            '/^https?:\/\//', // URLs
            '/^\/[a-z\/]+$/', // Paths
            '/^[A-Z_]+$/',   // Constants
        ];
        
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return false;
            }
        }
        
        // Must have at least one letter and be meaningful length
        return preg_match('/[a-zA-Zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text) 
               && strlen($text) >= 3;
    }
    
    private function generateRecommendations($results, $directory) {
        $recommendations = [];
        
        // Categorize directory to suggest appropriate key structure
        $category = $this->categorizeDirectory($directory);
        
        foreach ($results['hardcoded_texts'] as $text) {
            $suggestedKey = $this->suggestTranslationKey($text, $category, $directory);
            $recommendations[] = [
                'text' => $text,
                'suggested_key' => $suggestedKey,
                'helper_function' => $this->getHelperFunction($category),
                'blade_directive' => $this->getBladeDirective($category)
            ];
        }
        
        return $recommendations;
    }
    
    private function categorizeDirectory($directory) {
        $categoryMap = [
            'auth' => 'core',
            'layouts' => 'ui',
            'components' => 'ui',
            'partials' => 'ui',
            'forums' => 'features',
            'marketplace' => 'features',
            'showcase' => 'features',
            'showcases' => 'features',
            'community' => 'features',
            'knowledge' => 'features',
            'user' => 'user',
            'profile' => 'user',
            'notifications' => 'user',
            'pages' => 'content',
            'about' => 'content',
            'help' => 'content',
            'faq' => 'content',
        ];
        
        return $categoryMap[$directory] ?? 'content';
    }
    
    private function suggestTranslationKey($text, $category, $directory) {
        // Generate a suggested key based on text content
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', $key);
        $key = substr($key, 0, 30); // Limit length
        
        return "$category.$directory.$key";
    }
    
    private function getHelperFunction($category) {
        return "t_$category()";
    }
    
    private function getBladeDirective($category) {
        return "@$category()";
    }
    
    public function validateTranslationKeys($keys) {
        $validation = [
            'valid_keys' => [],
            'missing_vi' => [],
            'missing_en' => [],
            'missing_both' => []
        ];
        
        foreach ($keys as $key) {
            $viExists = $this->keyExistsInLang($key, 'vi');
            $enExists = $this->keyExistsInLang($key, 'en');
            
            if ($viExists && $enExists) {
                $validation['valid_keys'][] = $key;
            } elseif ($viExists && !$enExists) {
                $validation['missing_en'][] = $key;
            } elseif (!$viExists && $enExists) {
                $validation['missing_vi'][] = $key;
            } else {
                $validation['missing_both'][] = $key;
            }
        }
        
        return $validation;
    }
    
    private function keyExistsInLang($key, $lang) {
        $parts = explode('.', $key);
        if (count($parts) < 2) return false;
        
        $category = $parts[0];
        $file = $parts[1];
        $keyPath = implode('.', array_slice($parts, 2));
        
        $filePath = "$this->langNewPath/$lang/$category/$file.php";
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        $translations = include $filePath;
        return $this->arrayHasKey($translations, $keyPath);
    }
    
    private function arrayHasKey($array, $key) {
        $keys = explode('.', $key);
        $current = $array;
        
        foreach ($keys as $k) {
            if (!is_array($current) || !isset($current[$k])) {
                return false;
            }
            $current = $current[$k];
        }
        
        return true;
    }
    
    public function generateReport($results, $outputPath) {
        $report = "# Blade Localization Audit Report\n\n";
        $report .= "**Directory:** {$results['directory']}\n";
        $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Files processed:** {$results['files_processed']}\n\n";
        
        $report .= "## 📝 Hardcoded Texts Found (" . count($results['hardcoded_texts']) . ")\n\n";
        foreach ($results['hardcoded_texts'] as $text) {
            $report .= "- `$text`\n";
        }
        
        $report .= "\n## 🔑 Existing Translation Keys (" . count($results['existing_keys']) . ")\n\n";
        foreach ($results['existing_keys'] as $key) {
            $report .= "- `$key`\n";
        }
        
        $report .= "\n## 💡 Recommendations (" . count($results['recommendations']) . ")\n\n";
        foreach ($results['recommendations'] as $rec) {
            $report .= "### Text: `{$rec['text']}`\n";
            $report .= "- **Suggested key:** `{$rec['suggested_key']}`\n";
            $report .= "- **Helper function:** `{$rec['helper_function']}`\n";
            $report .= "- **Blade directive:** `{$rec['blade_directive']}`\n\n";
        }
        
        file_put_contents($outputPath, $report);
        return $outputPath;
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $toolkit = new BladeAuditToolkit();
    
    if ($argc < 2) {
        echo "Usage: php blade_audit_toolkit.php <directory>\n";
        echo "Example: php blade_audit_toolkit.php auth\n";
        exit(1);
    }
    
    $directory = $argv[1];
    $results = $toolkit->auditDirectory($directory);
    
    // Generate report
    $reportPath = "storage/localization/audit_report_$directory.md";
    $toolkit->generateReport($results, $reportPath);
    
    echo "📊 Report generated: $reportPath\n";
}
