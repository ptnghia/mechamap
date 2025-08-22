<?php

/**
 * Cleanup Unused Imports Script
 * Tìm và báo cáo các import không sử dụng trong codebase
 */

class UnusedImportCleaner
{
    private $basePath;
    private $excludedDirs = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        '.git',
        'public/build'
    ];
    
    private $checkedFiles = [];
    private $unusedImports = [];
    
    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: getcwd();
    }
    
    /**
     * Scan for unused imports
     */
    public function scan()
    {
        echo "🔍 Scanning for unused imports in: {$this->basePath}\n";
        echo "═══════════════════════════════════════════════════\n\n";
        
        $this->scanDirectory($this->basePath . '/app');
        $this->generateReport();
    }
    
    /**
     * Scan directory recursively
     */
    private function scanDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $fullPath = $dir . '/' . $file;
            
            if (is_dir($fullPath)) {
                $dirName = basename($fullPath);
                if (!in_array($dirName, $this->excludedDirs)) {
                    $this->scanDirectory($fullPath);
                }
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $this->analyzeFile($fullPath);
            }
        }
    }
    
    /**
     * Analyze PHP file for unused imports
     */
    private function analyzeFile($filePath)
    {
        $content = file_get_contents($filePath);
        if (!$content) {
            return;
        }
        
        $relativePath = str_replace($this->basePath . '/', '', $filePath);
        $this->checkedFiles[] = $relativePath;
        
        // Extract use statements
        preg_match_all('/^use\s+([^;]+);/m', $content, $matches);
        
        if (empty($matches[1])) {
            return;
        }
        
        $unusedInFile = [];
        
        foreach ($matches[1] as $useStatement) {
            $useStatement = trim($useStatement);
            
            // Skip Laravel framework imports (usually always used)
            if ($this->isFrameworkImport($useStatement)) {
                continue;
            }
            
            // Extract class name from use statement
            $className = $this->extractClassName($useStatement);
            
            if ($className && !$this->isClassUsed($content, $className, $useStatement)) {
                $unusedInFile[] = [
                    'statement' => $useStatement,
                    'class' => $className
                ];
            }
        }
        
        if (!empty($unusedInFile)) {
            $this->unusedImports[$relativePath] = $unusedInFile;
        }
    }
    
    /**
     * Check if import is framework related
     */
    private function isFrameworkImport($useStatement)
    {
        $frameworkPrefixes = [
            'Illuminate\\',
            'Laravel\\',
            'Symfony\\',
            'Carbon\\',
            'Psr\\',
        ];
        
        foreach ($frameworkPrefixes as $prefix) {
            if (strpos($useStatement, $prefix) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Extract class name from use statement
     */
    private function extractClassName($useStatement)
    {
        // Handle aliased imports
        if (strpos($useStatement, ' as ') !== false) {
            $parts = explode(' as ', $useStatement);
            return trim($parts[1]);
        }
        
        // Get last part of namespace
        $parts = explode('\\', $useStatement);
        return trim(end($parts));
    }
    
    /**
     * Check if class is used in content
     */
    private function isClassUsed($content, $className, $fullUseStatement)
    {
        // Remove the use statement from content for checking
        $contentWithoutUse = preg_replace('/^use\s+' . preg_quote($fullUseStatement, '/') . ';/m', '', $content);
        
        // Common usage patterns
        $patterns = [
            // Direct class usage
            '/\b' . preg_quote($className, '/') . '::/m',
            '/\bnew\s+' . preg_quote($className, '/') . '\b/m',
            '/\b' . preg_quote($className, '/') . '\s*\(/m',
            // Type hints
            '/\b' . preg_quote($className, '/') . '\s+\$\w+/m',
            '/:\s*' . preg_quote($className, '/') . '\b/m',
            // Array/collection type hints
            '/\b' . preg_quote($className, '/') . '\[\]/m',
            // In comments or docblocks (might be intentional)
            '/@\w+\s+' . preg_quote($className, '/') . '\b/m',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $contentWithoutUse)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate cleanup report
     */
    private function generateReport()
    {
        echo "\n📊 CLEANUP REPORT\n";
        echo "═══════════════════\n\n";
        
        echo "📁 Files checked: " . count($this->checkedFiles) . "\n";
        echo "🗑️  Files with unused imports: " . count($this->unusedImports) . "\n\n";
        
        if (empty($this->unusedImports)) {
            echo "✅ No unused imports found!\n";
            return;
        }
        
        echo "🔍 UNUSED IMPORTS FOUND:\n";
        echo "─────────────────────────\n\n";
        
        foreach ($this->unusedImports as $file => $imports) {
            echo "📄 {$file}\n";
            foreach ($imports as $import) {
                echo "   ❌ use {$import['statement']};\n";
            }
            echo "\n";
        }
        
        echo "💡 RECOMMENDATIONS:\n";
        echo "─────────────────────\n";
        echo "1. Review each unused import manually\n";
        echo "2. Remove confirmed unused imports\n";
        echo "3. Check if imports are used in comments/docs\n";
        echo "4. Verify no dynamic usage exists\n\n";
    }
}

// Run the scanner
if (php_sapi_name() === 'cli') {
    $cleaner = new UnusedImportCleaner();
    $cleaner->scan();
}
