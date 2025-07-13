<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;

class EnglishKeyValidator
{
    private $basePath;
    private $viTranslator;
    private $enTranslator;
    private $missingKeys = [];
    private $rawKeys = [];

    public function __construct()
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/lang');
        
        // Initialize translators
        $loader = new FileLoader(new Filesystem(), $this->basePath);
        $this->viTranslator = new Translator($loader, 'vi');
        $this->enTranslator = new Translator($loader, 'en');
    }

    public function validate()
    {
        echo "🔍 Validating English Language Keys\n";
        echo "===================================\n\n";

        $this->checkCriticalKeys();
        $this->checkViewFiles();
        $this->printSummary();
    }

    private function checkCriticalKeys()
    {
        echo "📋 Checking Critical Keys...\n";
        
        $criticalKeys = [
            // Navigation
            'nav.main.home',
            'nav.main.marketplace', 
            'nav.main.community',
            'nav.main.whats_new',
            'nav.auth.login',
            'nav.auth.register',
            
            // UI Common
            'ui.common.community',
            'ui.common.showcase',
            'ui.common.marketplace',
            'ui.common.more',
            'ui.actions.search',
            'ui.pagination.load_more',
            
            // Auth
            'auth.login.welcome_back',
            'auth.login.or_login_with',
            'auth.register.create_business_account',
            
            // Forum
            'forum.threads.pinned',
            'forum.threads.locked',
            'forum.forums.high_activity',
            
            // Home
            'home.featured_showcases',
            'home.featured_showcases_desc',
            
            // Buttons
            'buttons.view_details',
            
            // Common
            'common.language.switch',
        ];

        foreach ($criticalKeys as $key) {
            $enValue = $this->enTranslator->get($key);
            $viValue = $this->viTranslator->get($key);
            
            if ($enValue === $key) {
                echo "  ❌ Missing: $key\n";
                $this->missingKeys[] = $key;
            } else {
                echo "  ✅ Found: $key = '$enValue'\n";
            }
        }
        echo "\n";
    }

    private function checkViewFiles()
    {
        echo "📄 Checking View Files for Raw Keys...\n";
        
        $viewFiles = [
            'resources/views/components/header.blade.php',
            'resources/views/components/auth-modal.blade.php',
            'resources/views/home.blade.php',
            'resources/views/threads/index.blade.php',
        ];

        foreach ($viewFiles as $file) {
            $fullPath = realpath(__DIR__ . '/../../' . $file);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                
                // Check for raw keys (keys that start with lowercase and contain dots)
                preg_match_all("/__\('([a-z][a-z_]*\.[a-z_\.]+)'\)/", $content, $matches);
                
                if (!empty($matches[1])) {
                    echo "  📁 $file:\n";
                    foreach ($matches[1] as $rawKey) {
                        $enValue = $this->enTranslator->get($rawKey);
                        if ($enValue === $rawKey) {
                            echo "    ❌ Raw key: $rawKey\n";
                            $this->rawKeys[] = $rawKey;
                        } else {
                            echo "    ✅ Translated: $rawKey = '$enValue'\n";
                        }
                    }
                    echo "\n";
                }
            }
        }
    }

    private function printSummary()
    {
        echo "📊 VALIDATION SUMMARY\n";
        echo "====================\n\n";

        $totalMissing = count($this->missingKeys);
        $totalRaw = count(array_unique($this->rawKeys));

        echo "❌ Missing Keys: $totalMissing\n";
        echo "❌ Raw Keys in Views: $totalRaw\n\n";

        if ($totalMissing > 0) {
            echo "🔧 MISSING KEYS TO ADD:\n";
            foreach ($this->missingKeys as $key) {
                $viValue = $this->viTranslator->get($key);
                echo "  - $key (VI: '$viValue')\n";
            }
            echo "\n";
        }

        if ($totalRaw > 0) {
            echo "🔧 RAW KEYS TO TRANSLATE:\n";
            foreach (array_unique($this->rawKeys) as $key) {
                echo "  - $key\n";
            }
            echo "\n";
        }

        if ($totalMissing === 0 && $totalRaw === 0) {
            echo "🎉 ALL ENGLISH KEYS ARE PROPERLY TRANSLATED!\n";
        } else {
            echo "⚠️  English translation needs attention.\n";
        }
    }
}

$validator = new EnglishKeyValidator();
$validator->validate();
