<?php
/**
 * Migration Validation Script
 * 
 * Purpose: Validate language file migration and detect issues
 * Author: MechaMap Development Team
 * Date: 2025-07-12
 * 
 * Usage: php validate-migration.php [--verbose] [--fix-missing]
 */

class MigrationValidator
{
    private $basePath;
    private $verbose = false;
    private $fixMissing = false;
    private $issues = [];

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: realpath(__DIR__ . '/../../resources/lang');
        
        if (!$this->basePath) {
            throw new Exception("Language files path not found");
        }
    }

    public function run($options = [])
    {
        $this->verbose = $options['verbose'] ?? false;
        $this->fixMissing = $options['fix-missing'] ?? false;

        $this->log("🔍 Starting Migration Validation");
        $this->log("Base Path: {$this->basePath}");

        try {
            // Validation steps
            $this->validateFileStructure();
            $this->validateKeyConsistency();
            $this->validateViewReferences();
            $this->validateDuplicates();
            $this->validatePerformance();

            $this->generateReport();

        } catch (Exception $e) {
            $this->log("❌ Validation failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function validateFileStructure()
    {
        $this->log("📁 Validating file structure...");

        $requiredFiles = [
            'vi/nav.php',
            'vi/ui.php', 
            'vi/auth.php',
            'vi/marketplace.php',
            'vi/forum.php',
            'vi/common.php',
            'en/nav.php',
            'en/ui.php',
            'en/auth.php', 
            'en/marketplace.php',
            'en/forum.php',
            'en/common.php',
        ];

        foreach ($requiredFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            
            if (!file_exists($fullPath)) {
                $this->addIssue('missing_file', "Required file missing: {$file}");
                continue;
            }

            // Validate PHP syntax
            $content = file_get_contents($fullPath);
            if (!$this->isValidPhp($content)) {
                $this->addIssue('invalid_php', "Invalid PHP syntax: {$file}");
                continue;
            }

            // Validate file size
            $lines = count(file($fullPath));
            if ($lines > 150) {
                $this->addIssue('oversized_file', "File too large ({$lines} lines): {$file}");
            }

            // Validate structure
            $data = include $fullPath;
            if (!is_array($data)) {
                $this->addIssue('invalid_structure', "File doesn't return array: {$file}");
            }

            if ($this->verbose) {
                $keyCount = $this->countKeys($data);
                $this->log("  ✅ {$file}: {$lines} lines, {$keyCount} keys");
            }
        }
    }

    private function validateKeyConsistency()
    {
        $this->log("🔑 Validating key consistency...");

        $locales = ['vi', 'en'];
        $files = ['nav.php', 'ui.php', 'auth.php', 'marketplace.php', 'forum.php', 'common.php'];

        foreach ($files as $file) {
            $keys = [];
            
            foreach ($locales as $locale) {
                $fullPath = $this->basePath . "/{$locale}/{$file}";
                if (file_exists($fullPath)) {
                    $data = include $fullPath;
                    $keys[$locale] = $this->flattenKeys($data);
                }
            }

            // Compare keys between locales
            if (isset($keys['vi']) && isset($keys['en'])) {
                $viKeys = array_keys($keys['vi']);
                $enKeys = array_keys($keys['en']);
                
                $missingInEn = array_diff($viKeys, $enKeys);
                $missingInVi = array_diff($enKeys, $viKeys);
                
                foreach ($missingInEn as $key) {
                    $this->addIssue('missing_translation', "Missing English translation in {$file}: {$key}");
                }
                
                foreach ($missingInVi as $key) {
                    $this->addIssue('missing_translation', "Missing Vietnamese translation in {$file}: {$key}");
                }
            }
        }
    }

    private function validateViewReferences()
    {
        $this->log("👁️ Validating view references...");

        $viewsPath = dirname($this->basePath) . '/views';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath)
        );

        $missingKeys = [];
        $oldReferences = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Check for old messages.* references
                if (preg_match_all("/__\('messages\.([^']+)'\)/", $content, $matches)) {
                    foreach ($matches[0] as $match) {
                        $oldReferences[] = [
                            'file' => str_replace($viewsPath . '/', '', $file->getPathname()),
                            'reference' => $match
                        ];
                    }
                }

                // Check for new references and validate they exist
                if (preg_match_all("/__\('([^']+)'\)/", $content, $matches)) {
                    foreach ($matches[1] as $key) {
                        if (!str_starts_with($key, 'messages.') && !$this->keyExists($key)) {
                            $missingKeys[] = [
                                'file' => str_replace($viewsPath . '/', '', $file->getPathname()),
                                'key' => $key
                            ];
                        }
                    }
                }
            }
        }

        foreach ($oldReferences as $ref) {
            $this->addIssue('old_reference', "Old messages.* reference in {$ref['file']}: {$ref['reference']}");
        }

        foreach ($missingKeys as $missing) {
            $this->addIssue('missing_key', "Missing translation key in {$missing['file']}: {$missing['key']}");
        }
    }

    private function validateDuplicates()
    {
        $this->log("🔍 Checking for remaining duplicates...");

        $allKeys = [];
        $files = glob($this->basePath . '/*/*.php');

        foreach ($files as $file) {
            $relativePath = str_replace($this->basePath . '/', '', $file);
            $data = include $file;
            $flatKeys = $this->flattenKeys($data);

            foreach ($flatKeys as $key => $value) {
                if (!isset($allKeys[$key])) {
                    $allKeys[$key] = [];
                }
                $allKeys[$key][] = $relativePath;
            }
        }

        foreach ($allKeys as $key => $files) {
            if (count($files) > 1) {
                $this->addIssue('duplicate_key', "Duplicate key '{$key}' found in: " . implode(', ', $files));
            }
        }
    }

    private function validatePerformance()
    {
        $this->log("⚡ Validating performance...");

        // Measure loading time
        $start = microtime(true);
        
        $files = ['nav.php', 'ui.php', 'auth.php', 'marketplace.php', 'forum.php', 'common.php'];
        foreach ($files as $file) {
            $viPath = $this->basePath . "/vi/{$file}";
            $enPath = $this->basePath . "/en/{$file}";
            
            if (file_exists($viPath)) include $viPath;
            if (file_exists($enPath)) include $enPath;
        }
        
        $loadTime = microtime(true) - $start;
        
        if ($loadTime > 0.1) {
            $this->addIssue('performance', "Slow loading time: {$loadTime}s (should be < 0.1s)");
        }

        if ($this->verbose) {
            $this->log("  ⚡ Loading time: " . number_format($loadTime * 1000, 2) . "ms");
        }
    }

    private function keyExists($key)
    {
        $keyParts = explode('.', $key);
        if (count($keyParts) < 2) return false;

        $file = $keyParts[0];
        $filePath = $this->basePath . "/vi/{$file}.php";

        if (!file_exists($filePath)) return false;

        $data = include $filePath;
        $flatKeys = $this->flattenKeys($data);

        return isset($flatKeys[$key]);
    }

    private function flattenKeys($array, $prefix = '')
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenKeys($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }

    private function countKeys($array)
    {
        $count = 0;
        foreach ($array as $value) {
            if (is_array($value)) {
                $count += $this->countKeys($value);
            } else {
                $count++;
            }
        }
        return $count;
    }

    private function isValidPhp($content)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'php_syntax_check');
        file_put_contents($tempFile, $content);
        
        exec("php -l {$tempFile} 2>&1", $output, $returnCode);
        unlink($tempFile);
        
        return $returnCode === 0;
    }

    private function addIssue($type, $message)
    {
        $this->issues[] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => date('H:i:s')
        ];

        if ($this->verbose) {
            $this->log("  ⚠️ {$type}: {$message}");
        }
    }

    private function generateReport()
    {
        $this->log("\n📊 VALIDATION REPORT");
        $this->log("====================");

        if (empty($this->issues)) {
            $this->log("✅ All validations passed! Migration is successful.");
            return;
        }

        $issuesByType = [];
        foreach ($this->issues as $issue) {
            $type = $issue['type'];
            if (!isset($issuesByType[$type])) {
                $issuesByType[$type] = [];
            }
            $issuesByType[$type][] = $issue['message'];
        }

        foreach ($issuesByType as $type => $messages) {
            $count = count($messages);
            $this->log("\n🚨 {$type} ({$count} issues):");
            
            foreach ($messages as $message) {
                $this->log("  - {$message}");
            }
        }

        $totalIssues = count($this->issues);
        $this->log("\n📈 Summary: {$totalIssues} issues found");

        if ($totalIssues > 0) {
            $this->log("\n🔧 Recommended actions:");
            $this->log("1. Fix missing files and invalid syntax");
            $this->log("2. Add missing translation keys");
            $this->log("3. Update remaining old references");
            $this->log("4. Remove duplicate keys");
        }
    }

    private function log($message)
    {
        echo "[" . date('H:i:s') . "] {$message}\n";
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $options = [];
    
    foreach ($argv as $arg) {
        if ($arg === '--verbose') {
            $options['verbose'] = true;
        }
        if ($arg === '--fix-missing') {
            $options['fix-missing'] = true;
        }
    }

    try {
        $validator = new MigrationValidator();
        $validator->run($options);
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
