<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\LanguageService;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test default locale is Vietnamese
     */
    public function test_default_locale_is_vietnamese()
    {
        $response = $this->get('/');
        
        $this->assertEquals('vi', App::getLocale());
        $response->assertStatus(200);
    }

    /**
     * Test language switching to English
     */
    public function test_language_switch_to_english()
    {
        $response = $this->get('/language/switch/en');
        
        $this->assertEquals('en', Session::get('locale'));
        $response->assertRedirect();
    }

    /**
     * Test language switching to Vietnamese
     */
    public function test_language_switch_to_vietnamese()
    {
        $response = $this->get('/language/switch/vi');
        
        $this->assertEquals('vi', Session::get('locale'));
        $response->assertRedirect();
    }

    /**
     * Test invalid language returns error
     */
    public function test_invalid_language_returns_error()
    {
        $response = $this->get('/language/switch/invalid');
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test LanguageService methods
     */
    public function test_language_service_methods()
    {
        // Test supported locales
        $supportedLocales = LanguageService::getSupportedLocales();
        $this->assertArrayHasKey('vi', $supportedLocales);
        $this->assertArrayHasKey('en', $supportedLocales);

        // Test is supported
        $this->assertTrue(LanguageService::isSupported('vi'));
        $this->assertTrue(LanguageService::isSupported('en'));
        $this->assertFalse(LanguageService::isSupported('fr'));

        // Test set locale
        $this->assertTrue(LanguageService::setLocale('en'));
        $this->assertEquals('en', LanguageService::getCurrentLocale());

        $this->assertFalse(LanguageService::setLocale('invalid'));
    }

    /**
     * Test language API endpoints
     */
    public function test_language_api_endpoints()
    {
        // Test current language endpoint
        $response = $this->getJson('/language/current');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'current_locale',
                    'current_language',
                    'supported_languages',
                    'other_languages'
                ]);

        // Test supported languages endpoint
        $response = $this->getJson('/language/supported');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'supported_languages'
                ]);
    }

    /**
     * Test auto-detect language
     */
    public function test_auto_detect_language()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9,vi;q=0.8'
        ])->postJson('/language/auto-detect');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }

    /**
     * Test middleware applies locale correctly
     */
    public function test_middleware_applies_locale()
    {
        // Set session locale to English
        Session::put('locale', 'en');
        
        $response = $this->get('/');
        
        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Test translation files exist
     */
    public function test_translation_files_exist()
    {
        $this->assertFileExists(lang_path('vi/messages.php'));
        $this->assertFileExists(lang_path('en/messages.php'));
        $this->assertFileExists(lang_path('vi/auth.php'));
        $this->assertFileExists(lang_path('en/auth.php'));
    }

    /**
     * Test translation keys work
     */
    public function test_translation_keys_work()
    {
        App::setLocale('vi');
        $this->assertEquals('Chuyển đổi ngôn ngữ', __('messages.language.switch_language'));

        App::setLocale('en');
        $this->assertEquals('Switch Language', __('messages.language.switch_language'));
    }

    /**
     * Test language switcher component renders
     */
    public function test_language_switcher_component_renders()
    {
        $response = $this->get('/');
        
        $response->assertSee('language-switcher');
        $response->assertSee('languageDropdown');
    }
}
