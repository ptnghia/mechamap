<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Services\LanguageService;

class TestLocalization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:test {--locale=vi : Test specific locale}';

    /**
     * The console description of the console command.
     *
     * @var string
     */
    protected $description = 'Test localization configuration and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌍 Testing MechaMap Localization Configuration');
        $this->newLine();

        // Test 1: Check configuration
        $this->testConfiguration();

        // Test 2: Check language files
        $this->testLanguageFiles();

        // Test 3: Check LanguageService
        $this->testLanguageService();

        // Test 4: Test translations
        $this->testTranslations();

        // Test 5: Test specific locale if provided
        if ($this->option('locale')) {
            $this->testSpecificLocale($this->option('locale'));
        }

        $this->newLine();
        $this->info('✅ Localization test completed!');
    }

    /**
     * Test basic configuration
     */
    private function testConfiguration()
    {
        $this->info('📋 Testing Configuration...');

        $defaultLocale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');

        $this->line("Default Locale: {$defaultLocale}");
        $this->line("Fallback Locale: {$fallbackLocale}");

        if ($defaultLocale === 'vi') {
            $this->info('✅ Default locale is correctly set to Vietnamese');
        } else {
            $this->error('❌ Default locale should be Vietnamese (vi)');
        }

        if ($fallbackLocale === 'en') {
            $this->info('✅ Fallback locale is correctly set to English');
        } else {
            $this->error('❌ Fallback locale should be English (en)');
        }

        $this->newLine();
    }

    /**
     * Test language files
     */
    private function testLanguageFiles()
    {
        $this->info('📁 Testing Language Files...');

        $requiredFiles = [
            'vi/messages.php',
            'vi/auth.php',
            'vi/validation.php',
            'vi/passwords.php',
            'vi/pagination.php',
            'en/messages.php',
            'en/auth.php',
            'en/validation.php',
            'en/passwords.php',
            'en/pagination.php',
        ];

        foreach ($requiredFiles as $file) {
            $path = lang_path($file);
            if (file_exists($path)) {
                $this->info("✅ {$file}");
            } else {
                $this->error("❌ {$file} - Missing");
            }
        }

        $this->newLine();
    }

    /**
     * Test LanguageService
     */
    private function testLanguageService()
    {
        $this->info('🔧 Testing LanguageService...');

        try {
            $supportedLocales = LanguageService::getSupportedLocales();
            $this->info('✅ LanguageService::getSupportedLocales() works');
            $this->line('Supported locales: ' . implode(', ', array_keys($supportedLocales)));

            $currentLocale = LanguageService::getCurrentLocale();
            $this->info("✅ Current locale: {$currentLocale}");

            $isViSupported = LanguageService::isSupported('vi');
            $isEnSupported = LanguageService::isSupported('en');
            $isFrSupported = LanguageService::isSupported('fr');

            $this->info($isViSupported ? '✅ Vietnamese is supported' : '❌ Vietnamese not supported');
            $this->info($isEnSupported ? '✅ English is supported' : '❌ English not supported');
            $this->info(!$isFrSupported ? '✅ French correctly not supported' : '❌ French should not be supported');

        } catch (\Exception $e) {
            $this->error("❌ LanguageService error: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test translations
     */
    private function testTranslations()
    {
        $this->info('🔤 Testing Translations...');

        $testKeys = [
            'messages.language.switch_language',
            'messages.language.select_language',
            'messages.welcome',
            'auth.failed',
            'validation.required',
        ];

        foreach (['vi', 'en'] as $locale) {
            $this->line("Testing {$locale} translations:");
            App::setLocale($locale);

            foreach ($testKeys as $key) {
                $translation = __($key);
                if ($translation !== $key) {
                    $this->info("  ✅ {$key}: {$translation}");
                } else {
                    $this->warn("  ⚠️  {$key}: Translation missing");
                }
            }
            $this->newLine();
        }
    }

    /**
     * Test specific locale
     */
    private function testSpecificLocale($locale)
    {
        $this->info("🎯 Testing Specific Locale: {$locale}");

        if (!LanguageService::isSupported($locale)) {
            $this->error("❌ Locale {$locale} is not supported");
            return;
        }

        $success = LanguageService::setLocale($locale);
        if ($success) {
            $this->info("✅ Successfully set locale to {$locale}");
            $currentLocale = LanguageService::getCurrentLocale();
            $this->info("Current locale: {$currentLocale}");

            $languageInfo = LanguageService::getCurrentLanguageInfo();
            $this->info("Language name: {$languageInfo['name']}");
            $this->info("Language flag: {$languageInfo['flag']}");

            // Test some translations
            $this->line("Sample translations:");
            $this->line("  Welcome: " . __('messages.welcome'));
            $this->line("  Switch Language: " . __('messages.language.switch_language'));
        } else {
            $this->error("❌ Failed to set locale to {$locale}");
        }

        $this->newLine();
    }
}
