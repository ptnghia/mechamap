<?php

/**
 * 🔍 MechaMap Translation Conversion Validator
 * 
 * Validates the quality and completeness of translation key conversion
 * for the 5 priority files after manual Blade template editing.
 * 
 * Validation checks:
 * - Translation key resolution
 * - Missing translations
 * - Blade syntax errors
 * - Functionality preservation
 * - Performance impact
 * 
 * Usage: php scripts/validate_translation_conversion.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

class TranslationConversionValidator
{
    private $priorityFiles = [
        'threads/partials/showcase.blade.php',
        'threads/create.blade.php',
        'showcase/show.blade.php',
        'devices/index.blade.php',
        'layouts/app.blade.php'
    ];

    private $viewsPath;
    private $langPath;
    private $results = [];
    private $errors = [];
    private $warnings = [];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../resources/views';
        $this->langPath = __DIR__ . '/../resources/lang';
    }

    public function validate()
    {
        echo "🔍 Starting Translation Conversion Validation...\n";
        echo "===============================================\n\n";

        foreach ($this->priorityFiles as $file) {
            echo "📁 Validating: {$file}\n";
            $this->validateFile($file);
            echo "\n";
        }

        $this->generateValidationReport();
        $this->createFixingSuggestions();
    }

    private function validateFile($relativePath)
    {
        $fullPath = $this->viewsPath . '/' . $relativePath;
        
        if (!file_exists($fullPath)) {
            $this->errors[] = "File not found: {$fullPath}";
            return;
        }

        $content = file_get_contents($fullPath);
        
        $validation = [
            'file' => $relativePath,
            'syntax_check' => $this->validateBladeSyntax($content, $relativePath),
            'translation_keys' => $this->validateTranslationKeys($content, $relativePath),
            'hardcoded_remaining' => $this->findRemainingHardcodedText($content),
            'functionality_check' => $this->validateFunctionality($content, $relativePath),
            'performance_check' => $this->checkPerformanceImpact($content, $relativePath)
        ];

        $this->results[$relativePath] = $validation;
        $this->displayFileResults($validation);
    }

    private function validateBladeSyntax($content, $file)
    {
        $errors = [];
        $warnings = [];
        
        // Check for common Blade syntax issues
        $lines = explode("\n", $content);
        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;
            
            // Check for unmatched quotes in translation calls
            if (preg_match('/__\([^)]*$/', $line)) {
                $errors[] = "Line {$lineNum}: Unmatched parentheses in translation call";
            }
            
            // Check for malformed translation keys
            if (preg_match('/__\(["\'][^"\']*[^a-zA-Z0-9\._][^"\']*["\']/', $line)) {
                $warnings[] = "Line {$lineNum}: Potentially malformed translation key";
            }
            
            // Check for nested quotes issues
            if (preg_match('/__\(["\'][^"\']*["\'][^"\']*["\']/', $line)) {
                $errors[] = "Line {$lineNum}: Nested quotes in translation call";
            }
            
            // Check for missing translation function
            if (preg_match('/["\'][^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*["\']/', $line) && 
                !preg_match('/(__\(|t_\w+\(|@\w+\()/', $line)) {
                $warnings[] = "Line {$lineNum}: Possible remaining Vietnamese hardcoded text";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    private function validateTranslationKeys($content, $file)
    {
        $keys = [];
        $missing = [];
        $working = [];
        
        // Extract all translation keys from the file
        if (preg_match_all('/__\(["\']([^"\']+)["\']/', $content, $matches)) {
            $keys = array_unique($matches[1]);
        }

        // Check if each key exists and resolves
        foreach ($keys as $key) {
            $resolved = $this->resolveTranslationKey($key);
            if ($resolved === $key) {
                $missing[] = $key;
            } else {
                $working[] = $key;
            }
        }

        return [
            'total_keys' => count($keys),
            'working_keys' => count($working),
            'missing_keys' => count($missing),
            'missing_list' => $missing,
            'working_list' => $working,
            'coverage' => count($keys) > 0 ? round((count($working) / count($keys)) * 100, 2) : 100
        ];
    }

    private function resolveTranslationKey($key)
    {
        // Simulate Laravel's translation resolution
        $keyParts = explode('.', $key);
        
        if (count($keyParts) < 2) {
            return $key; // Invalid key format
        }

        $file = $keyParts[0];
        $nestedKey = implode('.', array_slice($keyParts, 1));
        
        // Check Vietnamese translation file
        $viFile = $this->langPath . '/vi/' . $file . '.php';
        if (file_exists($viFile)) {
            $translations = include $viFile;
            $value = $this->getNestedValue($translations, $nestedKey);
            if ($value !== null) {
                return $value;
            }
        }

        // Check English fallback
        $enFile = $this->langPath . '/en/' . $file . '.php';
        if (file_exists($enFile)) {
            $translations = include $enFile;
            $value = $this->getNestedValue($translations, $nestedKey);
            if ($value !== null) {
                return $value;
            }
        }

        return $key; // Key not found
    }

    private function getNestedValue($array, $key)
    {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    private function findRemainingHardcodedText($content)
    {
        $remaining = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $lineNumber => $line) {
            // Skip lines with translation functions
            if (preg_match('/(__\(|t_\w+\(|@\w+\()/', $line)) {
                continue;
            }

            // Find Vietnamese text
            if (preg_match_all('/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/', $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match);
                    if ($this->isValidHardcodedText($text)) {
                        $remaining[] = [
                            'text' => $text,
                            'line' => $lineNumber + 1,
                            'type' => 'vietnamese',
                            'context' => trim($line)
                        ];
                    }
                }
            }

            // Find English text
            if (preg_match_all('/["\']([A-Z][a-zA-Z\s]{3,50})["\']/', $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match);
                    if ($this->isValidHardcodedText($text) && $this->isEnglishText($text)) {
                        $remaining[] = [
                            'text' => $text,
                            'line' => $lineNumber + 1,
                            'type' => 'english',
                            'context' => trim($line)
                        ];
                    }
                }
            }
        }

        return [
            'count' => count($remaining),
            'strings' => $remaining
        ];
    }

    private function isValidHardcodedText($text)
    {
        if (strlen($text) < 2 || strlen($text) > 100) return false;
        if (preg_match('/^[\d\s\-_\.]+$/', $text)) return false;
        if (preg_match('/^[a-z\-_]+$/', $text)) return false;
        if (preg_match('/\.(css|js|php|html)$/', $text)) return false;
        if (preg_match('/^(http|https|mailto|tel):/', $text)) return false;
        return true;
    }

    private function isEnglishText($text)
    {
        return !preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text);
    }

    private function validateFunctionality($content, $file)
    {
        $issues = [];
        
        // Check for JavaScript integration issues
        if (strpos($content, 'data-') !== false && strpos($content, '__') !== false) {
            if (preg_match('/data-[^=]*=["\']\s*__\(/', $content)) {
                $issues[] = "Translation keys in data attributes may cause JavaScript issues";
            }
        }

        // Check for form validation integration
        if (strpos($content, 'validation') !== false || strpos($content, 'error') !== false) {
            if (preg_match('/@error.*__\(/', $content)) {
                $issues[] = "Translation keys in validation directives need testing";
            }
        }

        // Check for CKEditor configuration
        if (strpos($content, 'ckeditor') !== false || strpos($content, 'CKEditor') !== false) {
            if (preg_match('/ckeditor.*__\(/', $content)) {
                $issues[] = "CKEditor configuration with translation keys needs validation";
            }
        }

        return [
            'potential_issues' => count($issues),
            'issues' => $issues
        ];
    }

    private function checkPerformanceImpact($content, $file)
    {
        $translationCalls = substr_count($content, '__');
        $helperCalls = preg_match_all('/t_\w+\(/', $content);
        
        $impact = 'low';
        if ($translationCalls > 50) {
            $impact = 'high';
        } elseif ($translationCalls > 20) {
            $impact = 'medium';
        }

        return [
            'translation_calls' => $translationCalls,
            'helper_calls' => $helperCalls,
            'impact_level' => $impact,
            'recommendations' => $this->getPerformanceRecommendations($translationCalls)
        ];
    }

    private function getPerformanceRecommendations($callCount)
    {
        $recommendations = [];
        
        if ($callCount > 50) {
            $recommendations[] = "Consider caching translations for this view";
            $recommendations[] = "Group related translations to reduce file I/O";
        }
        
        if ($callCount > 20) {
            $recommendations[] = "Monitor page load time impact";
        }

        return $recommendations;
    }

    private function displayFileResults($validation)
    {
        $file = $validation['file'];
        
        // Syntax check
        if ($validation['syntax_check']['valid']) {
            echo "   ✅ Blade syntax: Valid\n";
        } else {
            echo "   ❌ Blade syntax: " . count($validation['syntax_check']['errors']) . " errors\n";
            foreach ($validation['syntax_check']['errors'] as $error) {
                echo "      - {$error}\n";
            }
        }

        if (!empty($validation['syntax_check']['warnings'])) {
            echo "   ⚠️  Warnings: " . count($validation['syntax_check']['warnings']) . "\n";
        }

        // Translation keys
        $keys = $validation['translation_keys'];
        echo "   📊 Translation keys: {$keys['working_keys']}/{$keys['total_keys']} working ({$keys['coverage']}%)\n";
        
        if (!empty($keys['missing_list'])) {
            echo "   ❌ Missing keys: " . implode(', ', array_slice($keys['missing_list'], 0, 3));
            if (count($keys['missing_list']) > 3) {
                echo " (+" . (count($keys['missing_list']) - 3) . " more)";
            }
            echo "\n";
        }

        // Remaining hardcoded text
        $remaining = $validation['hardcoded_remaining']['count'];
        if ($remaining > 0) {
            echo "   ⚠️  Remaining hardcoded: {$remaining} strings\n";
        } else {
            echo "   ✅ No hardcoded text remaining\n";
        }

        // Performance impact
        $perf = $validation['performance_check'];
        echo "   ⚡ Performance impact: {$perf['impact_level']} ({$perf['translation_calls']} calls)\n";
    }

    private function generateValidationReport()
    {
        echo "\n📊 VALIDATION SUMMARY\n";
        echo "====================\n";

        $totalFiles = count($this->results);
        $validFiles = 0;
        $totalKeys = 0;
        $workingKeys = 0;
        $totalRemaining = 0;

        foreach ($this->results as $file => $result) {
            if ($result['syntax_check']['valid'] && $result['translation_keys']['coverage'] > 90) {
                $validFiles++;
            }
            
            $totalKeys += $result['translation_keys']['total_keys'];
            $workingKeys += $result['translation_keys']['working_keys'];
            $totalRemaining += $result['hardcoded_remaining']['count'];
        }

        $overallCoverage = $totalKeys > 0 ? round(($workingKeys / $totalKeys) * 100, 2) : 100;

        echo "Files processed: {$totalFiles}\n";
        echo "Files passing validation: {$validFiles}\n";
        echo "Overall translation coverage: {$overallCoverage}%\n";
        echo "Total translation keys: {$totalKeys}\n";
        echo "Working translation keys: {$workingKeys}\n";
        echo "Remaining hardcoded strings: {$totalRemaining}\n";

        // Save detailed report
        $reportFile = __DIR__ . "/../storage/localization/validation_report_" . date('Y-m-d_H-i-s') . ".json";
        
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0755, true);
        }
        
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_files' => $totalFiles,
                'valid_files' => $validFiles,
                'overall_coverage' => $overallCoverage,
                'total_keys' => $totalKeys,
                'working_keys' => $workingKeys,
                'remaining_hardcoded' => $totalRemaining
            ],
            'results' => $this->results
        ];
        
        file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "\n💾 Detailed report saved to: " . basename($reportFile) . "\n";
    }

    private function createFixingSuggestions()
    {
        $suggestions = "# 🔧 Translation Conversion Fixing Suggestions\n\n";
        
        foreach ($this->results as $file => $result) {
            if (!$result['syntax_check']['valid'] || $result['translation_keys']['coverage'] < 100 || $result['hardcoded_remaining']['count'] > 0) {
                $suggestions .= "## File: {$file}\n\n";
                
                // Syntax errors
                if (!empty($result['syntax_check']['errors'])) {
                    $suggestions .= "### Syntax Errors to Fix:\n";
                    foreach ($result['syntax_check']['errors'] as $error) {
                        $suggestions .= "- {$error}\n";
                    }
                    $suggestions .= "\n";
                }
                
                // Missing translation keys
                if (!empty($result['translation_keys']['missing_list'])) {
                    $suggestions .= "### Missing Translation Keys:\n";
                    foreach ($result['translation_keys']['missing_list'] as $key) {
                        $suggestions .= "- `{$key}` - Create this key in appropriate translation file\n";
                    }
                    $suggestions .= "\n";
                }
                
                // Remaining hardcoded text
                if ($result['hardcoded_remaining']['count'] > 0) {
                    $suggestions .= "### Remaining Hardcoded Text:\n";
                    foreach ($result['hardcoded_remaining']['strings'] as $string) {
                        $suggestions .= "- Line {$string['line']}: \"{$string['text']}\" ({$string['type']})\n";
                    }
                    $suggestions .= "\n";
                }
            }
        }
        
        $suggestionsFile = __DIR__ . "/../storage/localization/fixing_suggestions.md";
        file_put_contents($suggestionsFile, $suggestions);
        echo "📝 Fixing suggestions saved to: " . basename($suggestionsFile) . "\n";
        echo "\n✅ Validation completed!\n";
    }
}

// Run the validator
$validator = new TranslationConversionValidator();
$validator->validate();
