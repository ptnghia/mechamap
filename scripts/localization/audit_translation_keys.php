<?php
/**
 * Translation Keys Audit Script
 * Scans all view files and extracts translation keys usage
 *
 * Usage: php scripts/localization/audit_translation_keys.php
 * Output: storage/localization/audit_results.json
 */

require_once __DIR__ . '/../../vendor/autoload.php';

class TranslationKeysAuditor
{
    private $results = [];
    private $keyUsage = [];
    private $duplicateKeys = [];
    private $missingKeys = [];

    public function __construct()
    {
        $this->ensureOutputDirectory();
    }

    /**
     * Main audit process
     */
    public function audit()
    {
        echo "🔍 Starting Translation Keys Audit...\n";

        // 1. Scan view files for translation keys
        $this->scanViewFiles();

        // 2. Scan existing language files
        $this->scanLanguageFiles();

        // 3. Analyze usage patterns
        $this->analyzeUsagePatterns();

        // 4. Generate reports
        $this->generateReports();

        echo "✅ Audit completed successfully!\n";
        echo "📊 Results saved to: storage/localization/\n";
    }

    /**
     * Scan all view files for translation keys
     */
    private function scanViewFiles()
    {
        echo "📁 Scanning view files and PHP files...\n";

        $scanPaths = [
            'resources/views',
            'app/Http/Controllers',
            'app/Services',
            'app/Models',
            'app/Http/Middleware',
            'app/Providers'
        ];

        foreach ($scanPaths as $path) {
            if (is_dir($path)) {
                echo "   Scanning: {$path}\n";
                $this->scanDirectory($path);
            }
        }

        echo "   Found " . count($this->keyUsage) . " unique translation keys\n";
        echo "   Total usages: " . array_sum(array_map('count', $this->keyUsage)) . "\n";
    }

    /**
     * Recursively scan directory for .blade.php files
     */
    private function scanDirectory($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->scanFile($file->getPathname());
            }
        }
    }

    /**
     * Scan individual file for translation keys
     */
    private function scanFile($filePath)
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(getcwd() . '/', '', $filePath);

        // Enhanced patterns to match various translation key formats
        $patterns = [
            // Basic __() patterns
            '/__(\'([^\']+)\')/m',
            '/__\("([^"]+)"\)/m',

            // Blade template patterns
            '/\{\{\s*__(\'([^\']+)\')\s*\}\}/m',
            '/\{\{\s*__\("([^"]+)"\)\s*\}\}/m',
            '/\{\{\s*__(\'([^\']+)\',\s*[^}]+)\s*\}\}/m',
            '/\{\{\s*__\("([^"]+)",\s*[^}]+)\s*\}\}/m',

            // @section patterns
            '/@section\([^,]+,\s*__(\'([^\']+)\')\)/m',
            '/@section\([^,]+,\s*__\("([^"]+)"\)\)/m',

            // @lang directive
            '/@lang\(\'([^\']+)\'\)/m',
            '/@lang\("([^"]+)"\)/m',

            // PHP echo patterns
            '/echo\s+__(\'([^\']+)\')/m',
            '/echo\s+__\("([^"]+)"\)/m',

            // Variable assignment patterns
            '/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*__(\'([^\']+)\')/m',
            '/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*__\("([^"]+)"\)/m',

            // Function parameter patterns
            '/[a-zA-Z_][a-zA-Z0-9_]*\(\s*__(\'([^\']+)\')/m',
            '/[a-zA-Z_][a-zA-Z0-9_]*\(\s*__\("([^"]+)"\)/m',

            // Array patterns
            '/\[\s*__(\'([^\']+)\')\s*\]/m',
            '/\[\s*__\("([^"]+)"\)\s*\]/m',

            // Conditional patterns
            '/\?\s*__(\'([^\']+)\')/m',
            '/\?\s*__\("([^"]+)"\)/m',
            '/:\s*__(\'([^\']+)\')/m',
            '/:\s*__\("([^"]+)"\)/m',

            // Method chaining patterns
            '/->.*__(\'([^\']+)\')/m',
            '/->.*__\("([^"]+)"\)/m',

            // Return statement patterns
            '/return\s+__(\'([^\']+)\')/m',
            '/return\s+__\("([^"]+)"\)/m'
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

            foreach ($matches as $match) {
                // Extract the key from different capture groups
                $key = '';
                for ($i = 1; $i < count($match); $i++) {
                    if (isset($match[$i][0]) && !empty($match[$i][0]) && !strpos($match[$i][0], '(')) {
                        $key = $match[$i][0];
                        break;
                    }
                }

                if (empty($key)) continue;

                $line = substr_count(substr($content, 0, $match[0][1]), "\n") + 1;

                if (!isset($this->keyUsage[$key])) {
                    $this->keyUsage[$key] = [];
                }

                $this->keyUsage[$key][] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'context' => trim($match[0][0])
                ];
            }
        }
    }

    /**
     * Scan existing language files
     */
    private function scanLanguageFiles()
    {
        echo "📚 Scanning language files...\n";

        $langPaths = ['resources/lang/vi', 'resources/lang/en'];

        foreach ($langPaths as $langPath) {
            if (is_dir($langPath)) {
                $this->scanLanguageDirectory($langPath);
            }
        }
    }

    /**
     * Scan language directory
     */
    private function scanLanguageDirectory($directory)
    {
        $files = glob($directory . '/*.php');
        $locale = basename($directory);

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $keys = $this->extractKeysFromFile($file);

            $this->results['language_files'][$locale][$filename] = [
                'file_path' => str_replace(getcwd() . '/', '', $file),
                'key_count' => count($keys),
                'keys' => $keys
            ];
        }
    }

    /**
     * Extract keys from language file
     */
    private function extractKeysFromFile($filePath)
    {
        $content = include $filePath;
        return $this->flattenArray($content);
    }

    /**
     * Flatten nested array to dot notation
     */
    private function flattenArray($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Analyze usage patterns
     */
    private function analyzeUsagePatterns()
    {
        echo "📊 Analyzing usage patterns...\n";

        // Count usage frequency
        $usageStats = [];
        foreach ($this->keyUsage as $key => $usages) {
            $usageStats[$key] = count($usages);
        }

        // Sort by usage frequency
        arsort($usageStats);

        $this->results['usage_statistics'] = [
            'total_unique_keys' => count($this->keyUsage),
            'total_usages' => array_sum($usageStats),
            'most_used_keys' => array_slice($usageStats, 0, 20, true),
            'unused_keys' => $this->findUnusedKeys(),
            'missing_keys' => $this->findMissingKeys()
        ];

        // Analyze key patterns
        $this->analyzeKeyPatterns();
    }

    /**
     * Find unused keys in language files
     */
    private function findUnusedKeys()
    {
        $unusedKeys = [];

        foreach ($this->results['language_files'] as $locale => $files) {
            foreach ($files as $filename => $fileData) {
                foreach ($fileData['keys'] as $key => $value) {
                    $fullKey = $filename . '.' . $key;
                    if (!isset($this->keyUsage[$fullKey])) {
                        $unusedKeys[] = $fullKey;
                    }
                }
            }
        }

        return $unusedKeys;
    }

    /**
     * Find missing keys (used but not defined)
     */
    private function findMissingKeys()
    {
        $missingKeys = [];
        $definedKeys = [];

        // Collect all defined keys
        foreach ($this->results['language_files'] as $locale => $files) {
            foreach ($files as $filename => $fileData) {
                foreach ($fileData['keys'] as $key => $value) {
                    $fullKey = $filename . '.' . $key;
                    $definedKeys[$fullKey] = true;
                }
            }
        }

        // Check used keys against defined keys
        foreach ($this->keyUsage as $key => $usages) {
            if (!isset($definedKeys[$key])) {
                $missingKeys[] = $key;
            }
        }

        return $missingKeys;
    }

    /**
     * Analyze key naming patterns
     */
    private function analyzeKeyPatterns()
    {
        $patterns = [];

        foreach ($this->keyUsage as $key => $usages) {
            $parts = explode('.', $key);
            $prefix = $parts[0] ?? 'unknown';

            if (!isset($patterns[$prefix])) {
                $patterns[$prefix] = 0;
            }
            $patterns[$prefix]++;
        }

        arsort($patterns);

        $this->results['key_patterns'] = $patterns;
    }

    /**
     * Generate comprehensive reports
     */
    private function generateReports()
    {
        echo "📝 Generating reports...\n";

        // Main audit results
        $this->results['audit_metadata'] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_files_scanned' => $this->countScannedFiles(),
            'key_usage_details' => $this->keyUsage
        ];

        // Save main results
        file_put_contents(
            'storage/localization/audit_results.json',
            json_encode($this->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // Generate CSV for easy analysis
        $this->generateCSVReport();

        // Generate summary report
        $this->generateSummaryReport();
    }

    /**
     * Generate CSV report
     */
    private function generateCSVReport()
    {
        $csvFile = fopen('storage/localization/key_usage_report.csv', 'w');

        // Headers
        fputcsv($csvFile, ['Key', 'Usage Count', 'Files', 'Pattern']);

        foreach ($this->keyUsage as $key => $usages) {
            $files = array_unique(array_column($usages, 'file'));
            $pattern = explode('.', $key)[0];

            fputcsv($csvFile, [
                $key,
                count($usages),
                implode('; ', $files),
                $pattern
            ]);
        }

        fclose($csvFile);
    }

    /**
     * Generate summary report
     */
    private function generateSummaryReport()
    {
        $summary = "# Translation Keys Audit Summary\n\n";
        $summary .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";

        $summary .= "## 📊 Statistics\n\n";
        $summary .= "- **Total unique keys:** " . count($this->keyUsage) . "\n";
        $summary .= "- **Total usages:** " . array_sum(array_map('count', $this->keyUsage)) . "\n";
        $summary .= "- **Files scanned:** " . $this->countScannedFiles() . "\n";
        $summary .= "- **Unused keys:** " . count($this->results['usage_statistics']['unused_keys']) . "\n";
        $summary .= "- **Missing keys:** " . count($this->results['usage_statistics']['missing_keys']) . "\n\n";

        $summary .= "## 🔥 Most Used Keys\n\n";
        foreach (array_slice($this->results['usage_statistics']['most_used_keys'], 0, 10, true) as $key => $count) {
            $summary .= "- `{$key}`: {$count} usages\n";
        }

        $summary .= "\n## 📁 Key Patterns\n\n";
        foreach (array_slice($this->results['key_patterns'], 0, 10, true) as $pattern => $count) {
            $summary .= "- `{$pattern}.*`: {$count} keys\n";
        }

        file_put_contents('storage/localization/audit_summary.md', $summary);
    }

    /**
     * Count total files scanned
     */
    private function countScannedFiles()
    {
        $files = [];
        foreach ($this->keyUsage as $usages) {
            foreach ($usages as $usage) {
                $files[$usage['file']] = true;
            }
        }
        return count($files);
    }

    /**
     * Ensure output directory exists
     */
    private function ensureOutputDirectory()
    {
        $dir = 'storage/localization';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

// Run the audit
$auditor = new TranslationKeysAuditor();
$auditor->audit();
