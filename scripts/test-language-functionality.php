<?php

/**
 * Language Functionality Testing Script
 * Tests language switching, translation loading, and functionality
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;

class LanguageFunctionalityTester
{
    private $basePath;
    private $languages = ['vi', 'en'];
    private $testResults = [];
    private $translator;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__);
        $this->setupTranslator();
    }

    /**
     * Setup translator for testing
     */
    private function setupTranslator(): void
    {
        $filesystem = new Filesystem();
        $loader = new FileLoader($filesystem, $this->basePath . '/resources/lang');
        $this->translator = new Translator($loader, 'vi');
    }

    /**
     * Run all tests
     */
    public function runTests(): array
    {
        echo "🧪 Starting Language Functionality Tests...\n\n";

        $this->testLanguageLoading();
        $this->testKeyTranslation();
        $this->testLanguageSwitching();
        $this->testCommonTranslations();
        $this->testNestedKeys();
        $this->testParameterReplacement();
        $this->testFallbackBehavior();

        $this->printResults();
        return $this->testResults;
    }

    /**
     * Test language file loading
     */
    private function testLanguageLoading(): void
    {
        echo "📁 Testing language file loading...\n";

        foreach ($this->languages as $lang) {
            $langPath = $this->basePath . "/resources/lang/{$lang}";
            $files = glob($langPath . "/*.php");

            foreach ($files as $file) {
                $fileName = basename($file, '.php');
                
                try {
                    $content = include $file;
                    
                    if (!is_array($content)) {
                        $this->addResult('language_loading', 'error', 
                            "File {$lang}/{$fileName}.php does not return array");
                        continue;
                    }

                    if (empty($content)) {
                        $this->addResult('language_loading', 'warning', 
                            "File {$lang}/{$fileName}.php is empty");
                        continue;
                    }

                    $this->addResult('language_loading', 'success', 
                        "File {$lang}/{$fileName}.php loaded successfully (" . count($content, COUNT_RECURSIVE) . " keys)");

                } catch (Exception $e) {
                    $this->addResult('language_loading', 'error', 
                        "Failed to load {$lang}/{$fileName}.php: " . $e->getMessage());
                }
            }
        }

        echo "   ✅ Language loading test completed\n";
    }

    /**
     * Test key translation
     */
    private function testKeyTranslation(): void
    {
        echo "🔑 Testing key translation...\n";

        $testKeys = [
            'common.save',
            'common.cancel',
            'common.delete',
            'nav.home',
            'nav.marketplace',
            'buttons.login',
            'buttons.search',
            'forum.threads',
            'forum.posts',
            'search.search_results'
        ];

        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);

            foreach ($testKeys as $key) {
                $translation = $this->translator->get($key);
                
                if ($translation === $key) {
                    $this->addResult('key_translation', 'warning', 
                        "Key '{$key}' not found in {$lang}");
                } else {
                    $this->addResult('key_translation', 'success', 
                        "Key '{$key}' translated in {$lang}: '{$translation}'");
                }
            }
        }

        echo "   ✅ Key translation test completed\n";
    }

    /**
     * Test language switching
     */
    private function testLanguageSwitching(): void
    {
        echo "🔄 Testing language switching...\n";

        $testKey = 'common.save';

        // Test VI
        $this->translator->setLocale('vi');
        $viTranslation = $this->translator->get($testKey);

        // Test EN
        $this->translator->setLocale('en');
        $enTranslation = $this->translator->get($testKey);

        if ($viTranslation !== $enTranslation) {
            $this->addResult('language_switching', 'success', 
                "Language switching works: VI='{$viTranslation}', EN='{$enTranslation}'");
        } else {
            $this->addResult('language_switching', 'warning', 
                "Language switching may not work properly - same translation for both languages");
        }

        echo "   ✅ Language switching test completed\n";
    }

    /**
     * Test common translations
     */
    private function testCommonTranslations(): void
    {
        echo "🌐 Testing common translations...\n";

        $commonKeys = [
            'buttons.save' => ['vi' => 'Lưu', 'en' => 'Save'],
            'buttons.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
            'buttons.delete' => ['vi' => 'Xóa', 'en' => 'Delete'],
            'buttons.edit' => ['vi' => 'Sửa', 'en' => 'Edit'],
            'nav.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        ];

        foreach ($commonKeys as $key => $expectedTranslations) {
            foreach ($expectedTranslations as $lang => $expected) {
                $this->translator->setLocale($lang);
                $actual = $this->translator->get($key);

                if ($actual === $expected) {
                    $this->addResult('common_translations', 'success', 
                        "Key '{$key}' correctly translated in {$lang}: '{$actual}'");
                } else {
                    $this->addResult('common_translations', 'warning', 
                        "Key '{$key}' translation mismatch in {$lang}: expected '{$expected}', got '{$actual}'");
                }
            }
        }

        echo "   ✅ Common translations test completed\n";
    }

    /**
     * Test nested keys
     */
    private function testNestedKeys(): void
    {
        echo "🏗️ Testing nested keys...\n";

        $nestedKeys = [
            'nav.marketplace',
            'forum.search.advanced',
            'marketplace.categories',
            'ui.actions.save',
            'messages.nav.home'
        ];

        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);

            foreach ($nestedKeys as $key) {
                $translation = $this->translator->get($key);
                
                if ($translation !== $key) {
                    $this->addResult('nested_keys', 'success', 
                        "Nested key '{$key}' found in {$lang}: '{$translation}'");
                } else {
                    $this->addResult('nested_keys', 'info', 
                        "Nested key '{$key}' not found in {$lang}");
                }
            }
        }

        echo "   ✅ Nested keys test completed\n";
    }

    /**
     * Test parameter replacement
     */
    private function testParameterReplacement(): void
    {
        echo "🔧 Testing parameter replacement...\n";

        $this->translator->setLocale('vi');

        // Test simple parameter
        $translation = $this->translator->get('search.found_results', ['count' => 5, 'query' => 'test']);
        
        if (strpos($translation, '5') !== false && strpos($translation, 'test') !== false) {
            $this->addResult('parameter_replacement', 'success', 
                "Parameter replacement works: '{$translation}'");
        } else {
            $this->addResult('parameter_replacement', 'warning', 
                "Parameter replacement may not work: '{$translation}'");
        }

        echo "   ✅ Parameter replacement test completed\n";
    }

    /**
     * Test fallback behavior
     */
    private function testFallbackBehavior(): void
    {
        echo "🔄 Testing fallback behavior...\n";

        $this->translator->setLocale('vi');
        
        // Test non-existent key
        $nonExistentKey = 'non.existent.key.test.123';
        $translation = $this->translator->get($nonExistentKey);

        if ($translation === $nonExistentKey) {
            $this->addResult('fallback_behavior', 'success', 
                "Fallback behavior works correctly - returns key when translation not found");
        } else {
            $this->addResult('fallback_behavior', 'warning', 
                "Unexpected fallback behavior: '{$translation}'");
        }

        echo "   ✅ Fallback behavior test completed\n";
    }

    /**
     * Add test result
     */
    private function addResult(string $category, string $type, string $message): void
    {
        $this->testResults[] = [
            'category' => $category,
            'type' => $type,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Print test results
     */
    private function printResults(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🧪 LANGUAGE FUNCTIONALITY TEST RESULTS\n";
        echo str_repeat("=", 60) . "\n\n";

        $categories = array_unique(array_column($this->testResults, 'category'));
        $stats = ['success' => 0, 'warning' => 0, 'error' => 0, 'info' => 0];

        foreach ($categories as $category) {
            echo "📋 " . strtoupper(str_replace('_', ' ', $category)) . ":\n";
            
            $categoryResults = array_filter($this->testResults, 
                fn($result) => $result['category'] === $category);

            foreach ($categoryResults as $result) {
                $icon = $this->getTypeIcon($result['type']);
                echo "   {$icon} {$result['message']}\n";
                $stats[$result['type']]++;
            }
            echo "\n";
        }

        // Print summary
        echo "📊 SUMMARY:\n";
        foreach ($stats as $type => $count) {
            $icon = $this->getTypeIcon($type);
            echo "   {$icon} " . ucfirst($type) . ": {$count}\n";
        }

        $totalTests = array_sum($stats);
        $successRate = $totalTests > 0 ? round(($stats['success'] / $totalTests) * 100, 1) : 0;
        
        echo "\n🎯 SUCCESS RATE: {$successRate}% ({$stats['success']}/{$totalTests})\n";

        if ($stats['error'] > 0) {
            echo "❌ CRITICAL ISSUES FOUND - Please fix errors before proceeding\n";
        } elseif ($stats['warning'] > 0) {
            echo "⚠️ WARNINGS FOUND - Review and fix if necessary\n";
        } else {
            echo "✅ ALL TESTS PASSED - Language system is working perfectly!\n";
        }

        echo "\n" . str_repeat("=", 60) . "\n";
    }

    /**
     * Get icon for result type
     */
    private function getTypeIcon(string $type): string
    {
        return match($type) {
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            'info' => 'ℹ️',
            default => '⚪'
        };
    }
}

// Run tests if script is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $tester = new LanguageFunctionalityTester();
    $results = $tester->runTests();
    
    // Exit with error code if critical issues found
    $errors = array_filter($results, fn($result) => $result['type'] === 'error');
    exit(empty($errors) ? 0 : 1);
}
