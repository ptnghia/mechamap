<?php

/**
 * Language Structure Validation Script
 * Validates new internationalization file structure and detects issues
 */

require_once __DIR__ . '/../vendor/autoload.php';

class LanguageStructureValidator
{
    private $basePath;
    private $languages = ['vi', 'en'];
    private $issues = [];
    private $stats = [];

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__);
    }

    /**
     * Run complete validation
     */
    public function validate(): array
    {
        echo "🔍 Starting Language Structure Validation...\n\n";

        $this->validateFileStructure();
        $this->validateKeyConsistency();
        $this->detectOrphanedKeys();
        $this->validateSyntax();
        $this->generateStats();

        $this->printResults();
        return $this->issues;
    }

    /**
     * Validate file structure consistency
     */
    private function validateFileStructure(): void
    {
        echo "📁 Validating file structure...\n";

        $viFiles = $this->getLanguageFiles('vi');
        $enFiles = $this->getLanguageFiles('en');

        // Check if both languages have same files
        $viFileNames = array_map('basename', $viFiles);
        $enFileNames = array_map('basename', $enFiles);

        $missingInEn = array_diff($viFileNames, $enFileNames);
        $missingInVi = array_diff($enFileNames, $viFileNames);

        if (!empty($missingInEn)) {
            $this->issues[] = [
                'type' => 'missing_files',
                'severity' => 'high',
                'message' => 'Files missing in English: ' . implode(', ', $missingInEn)
            ];
        }

        if (!empty($missingInVi)) {
            $this->issues[] = [
                'type' => 'missing_files',
                'severity' => 'high',
                'message' => 'Files missing in Vietnamese: ' . implode(', ', $missingInVi)
            ];
        }

        echo "   ✅ File structure check completed\n";
    }

    /**
     * Validate key consistency between languages
     */
    private function validateKeyConsistency(): void
    {
        echo "🔑 Validating key consistency...\n";

        $commonFiles = $this->getCommonFiles();

        foreach ($commonFiles as $file) {
            $viKeys = $this->getKeysFromFile('vi', $file);
            $enKeys = $this->getKeysFromFile('en', $file);

            $missingInEn = array_diff($viKeys, $enKeys);
            $missingInVi = array_diff($enKeys, $viKeys);

            if (!empty($missingInEn)) {
                $this->issues[] = [
                    'type' => 'missing_keys',
                    'severity' => 'high',
                    'file' => $file,
                    'language' => 'en',
                    'keys' => $missingInEn,
                    'message' => "Missing keys in {$file} (EN): " . implode(', ', array_slice($missingInEn, 0, 5)) . (count($missingInEn) > 5 ? '...' : '')
                ];
            }

            if (!empty($missingInVi)) {
                $this->issues[] = [
                    'type' => 'missing_keys',
                    'severity' => 'high',
                    'file' => $file,
                    'language' => 'vi',
                    'keys' => $missingInVi,
                    'message' => "Missing keys in {$file} (VI): " . implode(', ', array_slice($missingInVi, 0, 5)) . (count($missingInVi) > 5 ? '...' : '')
                ];
            }
        }

        echo "   ✅ Key consistency check completed\n";
    }

    /**
     * Detect orphaned keys (keys not used in views)
     */
    private function detectOrphanedKeys(): void
    {
        echo "🔍 Detecting orphaned keys...\n";

        $allKeys = $this->getAllTranslationKeys();
        $usedKeys = $this->getUsedKeysInViews();

        $orphanedKeys = array_diff($allKeys, $usedKeys);

        if (!empty($orphanedKeys)) {
            $this->issues[] = [
                'type' => 'orphaned_keys',
                'severity' => 'medium',
                'count' => count($orphanedKeys),
                'keys' => array_slice($orphanedKeys, 0, 10),
                'message' => count($orphanedKeys) . " orphaned keys found (showing first 10): " . implode(', ', array_slice($orphanedKeys, 0, 10))
            ];
        }

        echo "   ✅ Orphaned keys detection completed\n";
    }

    /**
     * Validate PHP syntax in language files
     */
    private function validateSyntax(): void
    {
        echo "🔧 Validating PHP syntax...\n";

        foreach ($this->languages as $lang) {
            $files = $this->getLanguageFiles($lang);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                
                // Check for syntax errors
                $result = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
                
                if (strpos($result, 'No syntax errors') === false) {
                    $this->issues[] = [
                        'type' => 'syntax_error',
                        'severity' => 'critical',
                        'file' => basename($file),
                        'language' => $lang,
                        'message' => "Syntax error in {$lang}/" . basename($file) . ": " . trim($result)
                    ];
                }

                // Check for proper array structure
                if (!$this->validateArrayStructure($file)) {
                    $this->issues[] = [
                        'type' => 'structure_error',
                        'severity' => 'high',
                        'file' => basename($file),
                        'language' => $lang,
                        'message' => "Invalid array structure in {$lang}/" . basename($file)
                    ];
                }
            }
        }

        echo "   ✅ Syntax validation completed\n";
    }

    /**
     * Generate statistics
     */
    private function generateStats(): void
    {
        $this->stats = [
            'total_files' => 0,
            'total_keys' => 0,
            'files_per_language' => [],
            'keys_per_file' => [],
            'issues_by_severity' => [
                'critical' => 0,
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ]
        ];

        foreach ($this->languages as $lang) {
            $files = $this->getLanguageFiles($lang);
            $this->stats['files_per_language'][$lang] = count($files);
            $this->stats['total_files'] += count($files);

            foreach ($files as $file) {
                $keys = $this->getKeysFromFile($lang, basename($file));
                $fileName = basename($file);
                $this->stats['keys_per_file'][$lang][$fileName] = count($keys);
                $this->stats['total_keys'] += count($keys);
            }
        }

        foreach ($this->issues as $issue) {
            $severity = $issue['severity'] ?? 'medium';
            $this->stats['issues_by_severity'][$severity]++;
        }
    }

    /**
     * Get language files for a specific language
     */
    private function getLanguageFiles(string $lang): array
    {
        $langPath = $this->basePath . "/resources/lang/{$lang}";
        return glob($langPath . "/*.php");
    }

    /**
     * Get common files between languages
     */
    private function getCommonFiles(): array
    {
        $viFiles = array_map('basename', $this->getLanguageFiles('vi'));
        $enFiles = array_map('basename', $this->getLanguageFiles('en'));
        return array_intersect($viFiles, $enFiles);
    }

    /**
     * Get keys from a language file
     */
    private function getKeysFromFile(string $lang, string $file): array
    {
        $filePath = $this->basePath . "/resources/lang/{$lang}/{$file}";
        
        if (!file_exists($filePath)) {
            return [];
        }

        $content = include $filePath;
        
        if (!is_array($content)) {
            return [];
        }

        return $this->flattenArray($content);
    }

    /**
     * Flatten nested array to dot notation keys
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[] = $newKey;
            }
        }
        
        return $result;
    }

    /**
     * Get all translation keys from all files
     */
    private function getAllTranslationKeys(): array
    {
        $allKeys = [];
        
        foreach ($this->getCommonFiles() as $file) {
            $keys = $this->getKeysFromFile('vi', $file);
            $filePrefix = pathinfo($file, PATHINFO_FILENAME);
            
            foreach ($keys as $key) {
                $allKeys[] = $filePrefix . '.' . $key;
            }
        }
        
        return array_unique($allKeys);
    }

    /**
     * Get used keys in views (simplified version)
     */
    private function getUsedKeysInViews(): array
    {
        $usedKeys = [];
        $viewsPath = $this->basePath . "/resources/views";
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Match __('key') and @lang('key') patterns
                preg_match_all("/__\('([^']+)'\)/", $content, $matches1);
                preg_match_all("/@lang\('([^']+)'\)/", $content, $matches2);
                
                $usedKeys = array_merge($usedKeys, $matches1[1], $matches2[1]);
            }
        }
        
        return array_unique($usedKeys);
    }

    /**
     * Validate array structure of a file
     */
    private function validateArrayStructure(string $filePath): bool
    {
        try {
            $content = include $filePath;
            return is_array($content);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Print validation results
     */
    private function printResults(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 VALIDATION RESULTS\n";
        echo str_repeat("=", 60) . "\n\n";

        // Print statistics
        echo "📈 STATISTICS:\n";
        echo "   Total Files: {$this->stats['total_files']}\n";
        echo "   Total Keys: {$this->stats['total_keys']}\n";
        
        foreach ($this->stats['files_per_language'] as $lang => $count) {
            echo "   Files ({$lang}): {$count}\n";
        }
        
        echo "\n";

        // Print issues by severity
        echo "🚨 ISSUES BY SEVERITY:\n";
        foreach ($this->stats['issues_by_severity'] as $severity => $count) {
            $icon = $this->getSeverityIcon($severity);
            echo "   {$icon} {$severity}: {$count}\n";
        }
        
        echo "\n";

        // Print detailed issues
        if (!empty($this->issues)) {
            echo "📋 DETAILED ISSUES:\n";
            foreach ($this->issues as $i => $issue) {
                $icon = $this->getSeverityIcon($issue['severity']);
                echo "   {$icon} " . ($i + 1) . ". {$issue['message']}\n";
            }
        } else {
            echo "✅ No issues found! Language structure is valid.\n";
        }

        echo "\n" . str_repeat("=", 60) . "\n";
    }

    /**
     * Get icon for severity level
     */
    private function getSeverityIcon(string $severity): string
    {
        return match($severity) {
            'critical' => '🔴',
            'high' => '🟠',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪'
        };
    }
}

// Run validation if script is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $validator = new LanguageStructureValidator();
    $issues = $validator->validate();
    
    // Exit with error code if critical issues found
    $criticalIssues = array_filter($issues, fn($issue) => $issue['severity'] === 'critical');
    exit(empty($criticalIssues) ? 0 : 1);
}
