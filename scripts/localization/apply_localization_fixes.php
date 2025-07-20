<?php
/**
 * Apply Localization Fixes
 * Automatically apply localization fixes to Blade templates based on audit results
 */

class LocalizationFixer {
    
    private $basePath;
    private $langNewPath;
    private $backupDir;
    
    public function __construct() {
        $this->basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
        $this->langNewPath = $this->basePath . '/resources/lang_new';
        $this->backupDir = $this->basePath . '/storage/localization/blade_backups_' . date('Y_m_d_H_i_s');
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    public function applyFixesToDirectory($directory, $auditResults) {
        echo "🔧 Applying localization fixes to: $directory\n";
        
        $viewsPath = $this->basePath . '/resources/views/' . $directory;
        if (!is_dir($viewsPath)) {
            echo "   ❌ Directory not found: $viewsPath\n";
            return false;
        }
        
        $fixedFiles = 0;
        $totalReplacements = 0;
        
        // Create backup for this directory
        $this->createBackup($viewsPath, $directory);
        
        // Get all blade files in directory
        $bladeFiles = $this->findBladeFiles($viewsPath);
        
        foreach ($bladeFiles as $file) {
            $replacements = $this->applyFixesToFile($file, $auditResults['recommendations']);
            if ($replacements > 0) {
                $fixedFiles++;
                $totalReplacements += $replacements;
                echo "   ✅ Fixed: " . basename($file) . " ($replacements replacements)\n";
            }
        }
        
        echo "   📊 Summary: $fixedFiles files fixed, $totalReplacements total replacements\n";
        return ['files' => $fixedFiles, 'replacements' => $totalReplacements];
    }
    
    private function createBackup($sourcePath, $directory) {
        $backupPath = $this->backupDir . '/' . $directory;
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        $this->copyDirectory($sourcePath, $backupPath);
        echo "   💾 Backup created: $backupPath\n";
    }
    
    private function copyDirectory($source, $destination) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
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
    
    private function applyFixesToFile($filePath, $recommendations) {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $replacements = 0;
        
        foreach ($recommendations as $rec) {
            $hardcodedText = $rec['text'];
            $suggestedKey = $rec['suggested_key'];
            $helperFunction = $rec['helper_function'];
            
            // Create the replacement using helper function
            $replacement = str_replace('()', "('" . explode('.', $suggestedKey, 2)[1] . "')", $helperFunction);
            
            // Try different patterns for hardcoded text
            $patterns = [
                // Simple quoted strings
                '/["\']' . preg_quote($hardcodedText, '/') . '["\']/',
                // In HTML attributes
                '/([a-zA-Z-]+=["\'])' . preg_quote($hardcodedText, '/') . '(["\'])/',
                // In Blade echo statements
                '/\{\{\s*["\']' . preg_quote($hardcodedText, '/') . '["\']\s*\}\}/',
            ];
            
            foreach ($patterns as $pattern) {
                $newContent = preg_replace($pattern, "{{ $replacement }}", $content);
                if ($newContent !== $content) {
                    $content = $newContent;
                    $replacements++;
                    break; // Only replace once per recommendation
                }
            }
        }
        
        // Save the updated content if changes were made
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
        }
        
        return $replacements;
    }
    
    public function createMissingTranslationKeys($recommendations) {
        echo "🔑 Creating missing translation keys...\n";
        
        $createdKeys = 0;
        $keysByCategory = [];
        
        // Group recommendations by category
        foreach ($recommendations as $rec) {
            $key = $rec['suggested_key'];
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $category = $parts[0];
                $file = $parts[1];
                $keyPath = implode('.', array_slice($parts, 2));
                
                if (!isset($keysByCategory[$category])) {
                    $keysByCategory[$category] = [];
                }
                if (!isset($keysByCategory[$category][$file])) {
                    $keysByCategory[$category][$file] = [];
                }
                
                $keysByCategory[$category][$file][$keyPath] = $rec['text'];
            }
        }
        
        // Create keys in both VI and EN
        foreach ($keysByCategory as $category => $files) {
            foreach ($files as $fileName => $keys) {
                $this->addKeysToFile($category, $fileName, $keys, 'vi');
                $this->addKeysToFile($category, $fileName, $keys, 'en');
                $createdKeys += count($keys);
            }
        }
        
        echo "   ✅ Created $createdKeys translation keys\n";
        return $createdKeys;
    }
    
    private function addKeysToFile($category, $fileName, $keys, $lang) {
        $filePath = "$this->langNewPath/$lang/$category/$fileName.php";
        
        // Load existing translations
        $translations = [];
        if (file_exists($filePath)) {
            $translations = include $filePath;
        }
        
        // Add new keys
        foreach ($keys as $keyPath => $value) {
            $this->setNestedKey($translations, $keyPath, $value);
        }
        
        // Generate new file content
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * " . ucfirst($lang) . " translations for $category/$fileName\n";
        $content .= " * Updated: " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n\n";
        $content .= "return " . $this->arrayToString($translations, 0) . ";\n";
        
        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($filePath, $content);
    }
    
    private function setNestedKey(&$array, $key, $value) {
        $keys = explode('.', $key);
        $current = &$array;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }
    
    private function arrayToString($array, $indent = 0) {
        if (empty($array)) {
            return '[]';
        }
        
        $spaces = str_repeat('    ', $indent);
        $result = "[\n";
        
        foreach ($array as $key => $value) {
            $result .= $spaces . "    ";
            
            if (is_string($key)) {
                $result .= "'" . addslashes($key) . "' => ";
            }
            
            if (is_array($value)) {
                $result .= $this->arrayToString($value, $indent + 1);
            } else {
                $result .= "'" . addslashes($value) . "'";
            }
            
            $result .= ",\n";
        }
        
        $result .= $spaces . "]";
        return $result;
    }
    
    public function generateFixReport($directory, $results, $outputPath) {
        $report = "# Localization Fixes Applied - $directory\n\n";
        $report .= "**Applied:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Files fixed:** {$results['files']}\n";
        $report .= "**Total replacements:** {$results['replacements']}\n\n";
        
        $report .= "## 💾 Backup Location\n\n";
        $report .= "Original files backed up to: `{$this->backupDir}/$directory`\n\n";
        
        $report .= "## 🔧 Changes Applied\n\n";
        $report .= "- Hardcoded text replaced with helper functions\n";
        $report .= "- Translation keys created in both VI and EN\n";
        $report .= "- Blade templates updated to use new localization structure\n\n";
        
        $report .= "## ✅ Next Steps\n\n";
        $report .= "1. Test the updated views to ensure functionality\n";
        $report .= "2. Review and refine translation values if needed\n";
        $report .= "3. Clear view cache: `php artisan view:clear`\n";
        $report .= "4. Test language switching functionality\n\n";
        
        $report .= "## 🔄 Rollback Instructions\n\n";
        $report .= "If issues occur, restore from backup:\n";
        $report .= "```bash\n";
        $report .= "cp -r {$this->backupDir}/$directory/* resources/views/$directory/\n";
        $report .= "```\n";
        
        file_put_contents($outputPath, $report);
        return $outputPath;
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    if ($argc < 3) {
        echo "Usage: php apply_localization_fixes.php <directory> <audit_results_file>\n";
        echo "Example: php apply_localization_fixes.php auth storage/localization/audit_report_auth.json\n";
        exit(1);
    }
    
    $directory = $argv[1];
    $auditResultsFile = $argv[2];
    
    if (!file_exists($auditResultsFile)) {
        echo "❌ Audit results file not found: $auditResultsFile\n";
        exit(1);
    }
    
    $auditResults = json_decode(file_get_contents($auditResultsFile), true);
    
    $fixer = new LocalizationFixer();
    
    // Create missing translation keys first
    $fixer->createMissingTranslationKeys($auditResults['recommendations']);
    
    // Apply fixes to files
    $results = $fixer->applyFixesToDirectory($directory, $auditResults);
    
    // Generate report
    $reportPath = "storage/localization/fixes_applied_$directory.md";
    $fixer->generateFixReport($directory, $results, $reportPath);
    
    echo "🎉 Localization fixes applied successfully!\n";
    echo "📊 Report: $reportPath\n";
}
