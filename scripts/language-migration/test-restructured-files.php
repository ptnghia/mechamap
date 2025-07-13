<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;

class LanguageRestructureTest
{
    private $basePath;
    private $translator;
    public $results = [];

    public function __construct()
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/lang');

        // Initialize translator
        $loader = new FileLoader(new Filesystem(), $this->basePath);
        $this->translator = new Translator($loader, 'vi');
    }

    public function runTests()
    {
        echo "🧪 Testing Restructured Language Files\n";
        echo "=====================================\n\n";

        $this->testFileStructure();
        $this->testKeyTranslations();
        $this->testLanguageSwitching();
        $this->testUpdatedReferences();
        $this->testMissingKeys();

        $this->printSummary();
    }

    private function testFileStructure()
    {
        echo "📁 Testing File Structure...\n";

        $requiredFiles = [
            'vi/nav.php', 'en/nav.php',
            'vi/ui.php', 'en/ui.php',
            'vi/auth.php', 'en/auth.php',
            'vi/marketplace.php', 'en/marketplace.php',
            'vi/forum.php', 'en/forum.php',
            'vi/common.php', 'en/common.php'
        ];

        foreach ($requiredFiles as $file) {
            $path = $this->basePath . '/' . $file;
            if (file_exists($path)) {
                echo "  ✅ $file exists\n";
                $this->results['file_structure']['passed']++;
            } else {
                echo "  ❌ $file missing\n";
                $this->results['file_structure']['failed']++;
            }
        }
        echo "\n";
    }

    private function testKeyTranslations()
    {
        echo "🔑 Testing Key Translations...\n";

        $testKeys = [
            'nav.main.home' => 'Trang chủ',
            'nav.main.marketplace' => 'Thị trường',
            'nav.main.forums' => 'Diễn đàn',
            'ui.actions.search' => 'Tìm kiếm',
            'ui.actions.save' => 'Lưu',
            'auth.login.title' => 'Đăng nhập',
            'auth.login.welcome_back' => 'Chào mừng trở lại',
            'forum.threads.create' => 'Tạo chủ đề',
            'forum.forums.high_activity' => 'Hoạt động cao',
            'marketplace.products.title' => 'Sản phẩm',
            'common.site.name' => 'MechaMap'
        ];

        foreach ($testKeys as $key => $expected) {
            $actual = $this->translator->get($key);
            if ($actual === $expected) {
                echo "  ✅ $key = '$actual'\n";
                $this->results['translations']['passed']++;
            } else {
                echo "  ❌ $key = '$actual' (expected '$expected')\n";
                $this->results['translations']['failed']++;
            }
        }
        echo "\n";
    }

    private function testLanguageSwitching()
    {
        echo "🌐 Testing Language Switching...\n";

        $this->translator->setLocale('en');

        $testKeys = [
            'nav.main.home' => 'Home',
            'ui.actions.search' => 'Search',
            'auth.login.title' => 'Login',
            'forum.threads.create' => 'Create Thread',
            'marketplace.products.title' => 'Products'
        ];

        foreach ($testKeys as $key => $expected) {
            $actual = $this->translator->get($key);
            if ($actual === $expected) {
                echo "  ✅ EN: $key = '$actual'\n";
                $this->results['language_switching']['passed']++;
            } else {
                echo "  ❌ EN: $key = '$actual' (expected '$expected')\n";
                $this->results['language_switching']['failed']++;
            }
        }

        // Switch back to Vietnamese
        $this->translator->setLocale('vi');
        echo "\n";
    }

    private function testUpdatedReferences()
    {
        echo "🔄 Testing Updated References...\n";

        // Check if old messages.* references still exist in updated files
        $updatedFiles = [
            'resources/views/components/auth-modal.blade.php',
            'resources/views/threads/index.blade.php',
            'resources/views/whats-new/index.blade.php'
        ];

        foreach ($updatedFiles as $file) {
            $fullPath = realpath(__DIR__ . '/../../' . $file);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $oldReferences = preg_match_all("/__\('messages\./", $content);

                if ($oldReferences === 0) {
                    echo "  ✅ $file: No old references found\n";
                    $this->results['updated_references']['passed']++;
                } else {
                    echo "  ⚠️  $file: $oldReferences old references still exist\n";
                    $this->results['updated_references']['warning']++;
                }
            }
        }
        echo "\n";
    }

    private function testMissingKeys()
    {
        echo "🔍 Testing for Missing Keys...\n";

        $criticalKeys = [
            'nav.main.home',
            'nav.main.marketplace',
            'nav.main.forums',
            'ui.actions.search',
            'auth.login.title',
            'forum.threads.create',
            'marketplace.products.title'
        ];

        foreach ($criticalKeys as $key) {
            $value = $this->translator->get($key);
            if ($value !== $key) { // If translation exists, it won't return the key itself
                echo "  ✅ $key: Found\n";
                $this->results['missing_keys']['passed']++;
            } else {
                echo "  ❌ $key: Missing\n";
                $this->results['missing_keys']['failed']++;
            }
        }
        echo "\n";
    }

    private function printSummary()
    {
        echo "📊 TEST SUMMARY\n";
        echo "===============\n\n";

        $totalPassed = 0;
        $totalFailed = 0;
        $totalWarning = 0;

        foreach ($this->results as $category => $results) {
            $passed = $results['passed'] ?? 0;
            $failed = $results['failed'] ?? 0;
            $warning = $results['warning'] ?? 0;

            echo "📋 " . ucwords(str_replace('_', ' ', $category)) . ":\n";
            echo "   ✅ Passed: $passed\n";
            if ($failed > 0) echo "   ❌ Failed: $failed\n";
            if ($warning > 0) echo "   ⚠️  Warning: $warning\n";
            echo "\n";

            $totalPassed += $passed;
            $totalFailed += $failed;
            $totalWarning += $warning;
        }

        echo "🎯 OVERALL RESULTS:\n";
        echo "   ✅ Total Passed: $totalPassed\n";
        echo "   ❌ Total Failed: $totalFailed\n";
        echo "   ⚠️  Total Warnings: $totalWarning\n\n";

        if ($totalFailed === 0) {
            echo "🎉 ALL TESTS PASSED! Language restructure is working correctly.\n";
        } else {
            echo "⚠️  Some tests failed. Please review the issues above.\n";
        }
    }
}

// Initialize results array
$test = new LanguageRestructureTest();
$test->results = [
    'file_structure' => ['passed' => 0, 'failed' => 0],
    'translations' => ['passed' => 0, 'failed' => 0],
    'language_switching' => ['passed' => 0, 'failed' => 0],
    'updated_references' => ['passed' => 0, 'failed' => 0, 'warning' => 0],
    'missing_keys' => ['passed' => 0, 'failed' => 0]
];

$test->runTests();
