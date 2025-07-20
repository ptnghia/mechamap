<?php
/**
 * Improved Blade Audit - Focus on actual localizable text
 * More precise detection of text that needs localization
 */

class ImprovedBladeAudit {
    
    private $basePath;
    private $langNewPath;
    
    public function __construct() {
        $this->basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
        $this->langNewPath = $this->basePath . '/resources/lang_new';
    }
    
    public function auditDirectory($directory) {
        echo "🔍 Improved audit for directory: $directory\n";
        
        $fullPath = $this->basePath . '/resources/views/' . $directory;
        if (!is_dir($fullPath)) {
            echo "   ❌ Directory not found: $fullPath\n";
            return [];
        }
        
        $results = [
            'directory' => $directory,
            'files_processed' => 0,
            'localizable_texts' => [],
            'existing_keys' => [],
            'priority_fixes' => []
        ];
        
        $bladeFiles = $this->findBladeFiles($fullPath);
        
        foreach ($bladeFiles as $file) {
            $fileResults = $this->auditFile($file);
            $results['files_processed']++;
            $results['localizable_texts'] = array_merge($results['localizable_texts'], $fileResults['localizable']);
            $results['existing_keys'] = array_merge($results['existing_keys'], $fileResults['existing_keys']);
        }
        
        // Remove duplicates and filter
        $results['localizable_texts'] = $this->filterAndCleanTexts($results['localizable_texts']);
        $results['existing_keys'] = array_unique($results['existing_keys']);
        
        // Generate priority fixes
        $results['priority_fixes'] = $this->generatePriorityFixes($results['localizable_texts'], $directory);
        
        echo "   ✅ Processed {$results['files_processed']} files\n";
        echo "   📝 Found " . count($results['localizable_texts']) . " localizable texts\n";
        echo "   🔑 Found " . count($results['existing_keys']) . " existing keys\n";
        echo "   🎯 Generated " . count($results['priority_fixes']) . " priority fixes\n";
        
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
            'localizable' => [],
            'existing_keys' => []
        ];
        
        // Find localizable text with improved patterns
        $localizableTexts = $this->findLocalizableText($content);
        $results['localizable'] = $localizableTexts;
        
        // Find existing translation keys
        $existingKeys = $this->findExistingKeys($content);
        $results['existing_keys'] = $existingKeys;
        
        return $results;
    }
    
    private function findLocalizableText($content) {
        $texts = [];
        
        // Pattern 1: Vietnamese text in quotes
        if (preg_match_all('/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                if ($this->isValidLocalizableText($match)) {
                    $texts[] = trim($match);
                }
            }
        }
        
        // Pattern 2: English sentences (meaningful text)
        if (preg_match_all('/["\']([A-Z][a-zA-Z\s]{5,50})["\']/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                if ($this->isValidLocalizableText($match) && $this->isEnglishSentence($match)) {
                    $texts[] = trim($match);
                }
            }
        }
        
        // Pattern 3: Button/label text
        if (preg_match_all('/>\s*([A-ZÀ-Ỹ][a-zA-Zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ\s]{2,30})\s*</', $content, $matches)) {
            foreach ($matches[1] as $match) {
                if ($this->isValidLocalizableText($match)) {
                    $texts[] = trim($match);
                }
            }
        }
        
        // Pattern 4: Placeholder text
        if (preg_match_all('/placeholder\s*=\s*["\']([^"\']+)["\']/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                if ($this->isValidLocalizableText($match)) {
                    $texts[] = trim($match);
                }
            }
        }
        
        // Pattern 5: Title attributes
        if (preg_match_all('/title\s*=\s*["\']([^"\']+)["\']/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                if ($this->isValidLocalizableText($match)) {
                    $texts[] = trim($match);
                }
            }
        }
        
        return $texts;
    }
    
    private function findExistingKeys($content) {
        $keys = [];
        
        // Laravel translation functions
        $patterns = [
            '/__\(["\']([^"\']+)["\']\)/',
            '/trans\(["\']([^"\']+)["\']\)/',
            '/@lang\(["\']([^"\']+)["\']\)/',
            '/@(core|ui|content|feature|user|admin)\(["\']([^"\']+)["\']\)/',
            '/t_(core|ui|content|feature|user|admin)\(["\']([^"\']+)["\']\)/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $key) {
                        $keys[] = $key;
                    }
                }
                if (isset($matches[2])) {
                    foreach ($matches[2] as $key) {
                        $keys[] = $key;
                    }
                }
            }
        }
        
        return $keys;
    }
    
    private function isValidLocalizableText($text) {
        $text = trim($text);
        
        // Must have minimum length
        if (strlen($text) < 2) return false;
        
        // Must contain letters
        if (!preg_match('/[a-zA-Zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text)) return false;
        
        // Exclude obvious non-text
        $excludePatterns = [
            '/^[a-z-]+$/',          // CSS classes
            '/^#[0-9a-f]+$/i',      // Colors
            '/^\d+$/',              // Numbers only
            '/^[a-z]+\.[a-z]+$/',   // File extensions
            '/^https?:\/\//',       // URLs
            '/^\/[a-z\/\-]+$/',     // Paths
            '/^[A-Z_]+$/',          // Constants
            '/^[a-z]+\(\)$/',       // Functions
            '/^[a-z]+\.[a-z]+/',    // Object properties
            '/^\$[a-z]+/',          // Variables
            '/^@[a-z]+/',           // Directives
            '/^<[^>]+>$/',          // HTML tags
            '/^\s*$/',              // Whitespace only
            '/^[^a-zA-Zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]+$/', // No letters
        ];
        
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return false;
            }
        }
        
        // Exclude HTML entities and markup
        if (strpos($text, '&') !== false || strpos($text, '<') !== false || strpos($text, '>') !== false) {
            return false;
        }
        
        return true;
    }
    
    private function isEnglishSentence($text) {
        // Check if it's likely an English sentence
        $englishWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'];
        $lowerText = strtolower($text);
        
        foreach ($englishWords as $word) {
            if (strpos($lowerText, ' ' . $word . ' ') !== false || 
                strpos($lowerText, $word . ' ') === 0 || 
                strpos($lowerText, ' ' . $word) === strlen($lowerText) - strlen($word) - 1) {
                return true;
            }
        }
        
        // Check for common English patterns
        return preg_match('/^[A-Z][a-z]+ [a-z]/', $text) || 
               preg_match('/\b(is|are|was|were|have|has|will|would|can|could|should|may|might)\b/i', $text);
    }
    
    private function filterAndCleanTexts($texts) {
        $filtered = [];
        $seen = [];
        
        foreach ($texts as $text) {
            $clean = trim($text);
            if (!empty($clean) && !isset($seen[$clean]) && $this->isValidLocalizableText($clean)) {
                $filtered[] = $clean;
                $seen[$clean] = true;
            }
        }
        
        return $filtered;
    }
    
    private function generatePriorityFixes($texts, $directory) {
        $fixes = [];
        $category = $this->categorizeDirectory($directory);
        
        foreach ($texts as $text) {
            // Only include high-priority texts
            if ($this->isHighPriorityText($text)) {
                $key = $this->generateTranslationKey($text, $category, $directory);
                $fixes[] = [
                    'text' => $text,
                    'key' => $key,
                    'helper' => "t_$category('$key')",
                    'directive' => "@$category('$key')",
                    'priority' => $this->calculateTextPriority($text)
                ];
            }
        }
        
        // Sort by priority
        usort($fixes, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        
        return array_slice($fixes, 0, 20); // Top 20 priority fixes
    }
    
    private function isHighPriorityText($text) {
        // High priority: User-facing text, buttons, labels, messages
        $highPriorityPatterns = [
            '/^(Đăng nhập|Login|Đăng ký|Register|Lưu|Save|Hủy|Cancel|Xóa|Delete)$/',
            '/^(Tìm kiếm|Search|Thêm|Add|Sửa|Edit|Xem|View)/',
            '/^(Thành công|Success|Lỗi|Error|Cảnh báo|Warning)/',
            '/^(Trang chủ|Home|Hồ sơ|Profile|Cài đặt|Settings)/',
        ];
        
        foreach ($highPriorityPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        
        // Vietnamese text is generally high priority
        if (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text)) {
            return true;
        }
        
        return false;
    }
    
    private function calculateTextPriority($text) {
        $priority = 0;
        
        // Vietnamese text gets higher priority
        if (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text)) {
            $priority += 10;
        }
        
        // Common UI elements
        $uiElements = ['Đăng nhập', 'Login', 'Đăng ký', 'Register', 'Lưu', 'Save', 'Hủy', 'Cancel'];
        foreach ($uiElements as $element) {
            if (stripos($text, $element) !== false) {
                $priority += 5;
                break;
            }
        }
        
        // Shorter text is often more important (buttons, labels)
        if (strlen($text) <= 20) {
            $priority += 3;
        }
        
        return $priority;
    }
    
    private function categorizeDirectory($directory) {
        $categoryMap = [
            'components' => 'ui',
            'layouts' => 'ui',
            'partials' => 'ui',
            'auth' => 'core',
            'forums' => 'features',
            'marketplace' => 'features',
            'profile' => 'user',
            'user' => 'user',
        ];
        
        return $categoryMap[$directory] ?? 'content';
    }
    
    private function generateTranslationKey($text, $category, $directory) {
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', $key);
        $key = substr($key, 0, 30);
        
        return "$directory.$key";
    }
    
    public function generateImprovedReport($results, $outputPath) {
        $report = "# Improved Blade Localization Audit Report\n\n";
        $report .= "**Directory:** {$results['directory']}\n";
        $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Files processed:** {$results['files_processed']}\n\n";
        
        $report .= "## 📝 Localizable Texts Found (" . count($results['localizable_texts']) . ")\n\n";
        foreach (array_slice($results['localizable_texts'], 0, 50) as $text) {
            $report .= "- `$text`\n";
        }
        if (count($results['localizable_texts']) > 50) {
            $report .= "\n... and " . (count($results['localizable_texts']) - 50) . " more\n";
        }
        
        $report .= "\n## 🔑 Existing Translation Keys (" . count($results['existing_keys']) . ")\n\n";
        foreach (array_slice($results['existing_keys'], 0, 30) as $key) {
            $report .= "- `$key`\n";
        }
        if (count($results['existing_keys']) > 30) {
            $report .= "\n... and " . (count($results['existing_keys']) - 30) . " more\n";
        }
        
        $report .= "\n## 🎯 Priority Fixes (" . count($results['priority_fixes']) . ")\n\n";
        foreach ($results['priority_fixes'] as $fix) {
            $report .= "### Text: `{$fix['text']}` (Priority: {$fix['priority']})\n";
            $report .= "- **Key:** `{$fix['key']}`\n";
            $report .= "- **Helper:** `{$fix['helper']}`\n";
            $report .= "- **Directive:** `{$fix['directive']}`\n\n";
        }
        
        file_put_contents($outputPath, $report);
        return $outputPath;
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    if ($argc < 2) {
        echo "Usage: php improved_blade_audit.php <directory>\n";
        exit(1);
    }
    
    $directory = $argv[1];
    $audit = new ImprovedBladeAudit();
    $results = $audit->auditDirectory($directory);
    
    $reportPath = "storage/localization/improved_audit_$directory.md";
    $audit->generateImprovedReport($results, $reportPath);
    
    echo "📊 Improved report: $reportPath\n";
}
