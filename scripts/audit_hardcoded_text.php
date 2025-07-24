<?php

/**
 * 🔍 MechaMap Hardcoded Text Audit Script
 * 
 * Comprehensive audit of hardcoded Vietnamese and English text in Blade templates
 * Excludes admin/ directory as per requirements
 * 
 * Usage: php scripts/audit_hardcoded_text.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

class HardcodedTextAuditor
{
    private $viewsPath;
    private $results = [];
    private $stats = [
        'total_files' => 0,
        'files_with_hardcoded' => 0,
        'total_hardcoded_strings' => 0,
        'vietnamese_strings' => 0,
        'english_strings' => 0,
        'translation_coverage' => 0
    ];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../resources/views';
    }

    public function audit()
    {
        echo "🔍 Starting MechaMap Hardcoded Text Audit...\n";
        echo "===========================================\n\n";

        $this->scanDirectory($this->viewsPath);
        $this->generateReport();
        $this->saveResults();
    }

    private function scanDirectory($directory, $relativePath = '')
    {
        $items = scandir($directory);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $directory . '/' . $item;
            $currentRelativePath = $relativePath ? $relativePath . '/' . $item : $item;
            
            // Skip admin directory
            if ($item === 'admin' && $relativePath === '') {
                echo "⏭️  Skipping admin directory as requested\n";
                continue;
            }
            
            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $currentRelativePath);
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'php' && 
                     str_contains($item, '.blade.')) {
                $this->auditFile($fullPath, $currentRelativePath);
            }
        }
    }

    private function auditFile($filePath, $relativePath)
    {
        $this->stats['total_files']++;
        $content = file_get_contents($filePath);
        
        $hardcodedStrings = $this->findHardcodedText($content);
        
        if (!empty($hardcodedStrings)) {
            $this->stats['files_with_hardcoded']++;
            $this->results[$relativePath] = [
                'file_path' => $relativePath,
                'hardcoded_strings' => $hardcodedStrings,
                'vietnamese_count' => 0,
                'english_count' => 0,
                'total_count' => count($hardcodedStrings)
            ];
            
            // Categorize strings
            foreach ($hardcodedStrings as $string) {
                if ($this->isVietnamese($string['text'])) {
                    $this->results[$relativePath]['vietnamese_count']++;
                    $this->stats['vietnamese_strings']++;
                } else {
                    $this->results[$relativePath]['english_count']++;
                    $this->stats['english_strings']++;
                }
            }
            
            $this->stats['total_hardcoded_strings'] += count($hardcodedStrings);
        }
    }

    private function findHardcodedText($content)
    {
        $hardcodedStrings = [];
        $lineNumber = 0;
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $lineNumber++;
            
            // Skip lines with translation functions
            if (preg_match('/(__\(|t_\w+\(|@\w+\()/', $line)) {
                continue;
            }
            
            // Pattern 1: Vietnamese text in quotes
            if (preg_match_all('/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match[0]);
                    if ($this->isValidHardcodedText($text)) {
                        $hardcodedStrings[] = [
                            'text' => $text,
                            'line' => $lineNumber,
                            'type' => 'vietnamese',
                            'context' => trim($line)
                        ];
                    }
                }
            }
            
            // Pattern 2: English sentences (meaningful text)
            if (preg_match_all('/["\']([A-Z][a-zA-Z\s]{5,50})["\']/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match[0]);
                    if ($this->isValidHardcodedText($text) && $this->isEnglishSentence($text)) {
                        $hardcodedStrings[] = [
                            'text' => $text,
                            'line' => $lineNumber,
                            'type' => 'english',
                            'context' => trim($line)
                        ];
                    }
                }
            }
            
            // Pattern 3: Common UI text
            if (preg_match_all('/["\']([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)["\']/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match[0]);
                    if ($this->isCommonUIText($text) && $this->isValidHardcodedText($text)) {
                        $hardcodedStrings[] = [
                            'text' => $text,
                            'line' => $lineNumber,
                            'type' => 'ui_text',
                            'context' => trim($line)
                        ];
                    }
                }
            }
        }
        
        return $hardcodedStrings;
    }

    private function isVietnamese($text)
    {
        return preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text);
    }

    private function isEnglishSentence($text)
    {
        // Check if it's a meaningful English sentence
        $words = explode(' ', $text);
        return count($words) >= 2 && 
               !preg_match('/^[A-Z_]+$/', $text) && // Not all caps
               !preg_match('/^\d+$/', $text) && // Not just numbers
               strlen($text) > 5;
    }

    private function isCommonUIText($text)
    {
        $commonUIWords = [
            'Save', 'Cancel', 'Delete', 'Edit', 'Create', 'Add', 'Remove', 'Submit',
            'Login', 'Register', 'Logout', 'Profile', 'Settings', 'Dashboard',
            'Search', 'Filter', 'Sort', 'View', 'Details', 'Back', 'Next', 'Previous',
            'Home', 'About', 'Contact', 'Help', 'Support', 'Terms', 'Privacy',
            'Loading', 'Error', 'Success', 'Warning', 'Info', 'Close', 'Open'
        ];
        
        return in_array($text, $commonUIWords) || 
               preg_match('/^(View|Edit|Delete|Create|Add|Remove)\s+\w+$/', $text);
    }

    private function isValidHardcodedText($text)
    {
        // Filter out invalid strings
        if (strlen($text) < 2 || strlen($text) > 100) return false;
        if (preg_match('/^[\d\s\-_\.]+$/', $text)) return false; // Numbers, spaces, dashes only
        if (preg_match('/^[a-z\-_]+$/', $text)) return false; // CSS classes, IDs
        if (preg_match('/\.(css|js|php|html)$/', $text)) return false; // File extensions
        if (preg_match('/^(http|https|mailto|tel):/', $text)) return false; // URLs
        if (preg_match('/^#[a-fA-F0-9]{3,6}$/', $text)) return false; // Color codes
        if (preg_match('/^\$\{.*\}$/', $text)) return false; // Template variables
        
        return true;
    }

    private function generateReport()
    {
        echo "\n📊 AUDIT RESULTS SUMMARY\n";
        echo "========================\n";
        echo "Total files scanned: {$this->stats['total_files']}\n";
        echo "Files with hardcoded text: {$this->stats['files_with_hardcoded']}\n";
        echo "Total hardcoded strings: {$this->stats['total_hardcoded_strings']}\n";
        echo "Vietnamese strings: {$this->stats['vietnamese_strings']}\n";
        echo "English strings: {$this->stats['english_strings']}\n";
        
        $coverage = $this->stats['total_files'] > 0 ? 
            round((($this->stats['total_files'] - $this->stats['files_with_hardcoded']) / $this->stats['total_files']) * 100, 2) : 0;
        echo "Translation coverage: {$coverage}%\n\n";
        
        // Top files with most hardcoded text
        echo "🔥 TOP FILES WITH MOST HARDCODED TEXT\n";
        echo "=====================================\n";
        
        uasort($this->results, function($a, $b) {
            return $b['total_count'] - $a['total_count'];
        });
        
        $topFiles = array_slice($this->results, 0, 10, true);
        foreach ($topFiles as $file => $data) {
            echo sprintf("%-50s %3d strings (%d VI, %d EN)\n", 
                $file, 
                $data['total_count'], 
                $data['vietnamese_count'], 
                $data['english_count']
            );
        }
        
        echo "\n📝 EXAMPLES OF HARDCODED TEXT\n";
        echo "=============================\n";
        
        $exampleCount = 0;
        foreach ($this->results as $file => $data) {
            if ($exampleCount >= 20) break;
            
            foreach ($data['hardcoded_strings'] as $string) {
                if ($exampleCount >= 20) break;
                
                echo sprintf("File: %s (Line %d)\n", $file, $string['line']);
                echo sprintf("Text: \"%s\" [%s]\n", $string['text'], $string['type']);
                echo sprintf("Context: %s\n", substr($string['context'], 0, 80) . '...');
                echo "---\n";
                $exampleCount++;
            }
        }
    }

    private function saveResults()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $reportFile = __DIR__ . "/../storage/localization/hardcoded_text_audit_{$timestamp}.json";
        
        $report = [
            'timestamp' => $timestamp,
            'stats' => $this->stats,
            'results' => $this->results
        ];
        
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0755, true);
        }
        
        file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "\n💾 Report saved to: {$reportFile}\n";
        echo "\n✅ Audit completed successfully!\n";
    }
}

// Run the audit
$auditor = new HardcodedTextAuditor();
$auditor->audit();
