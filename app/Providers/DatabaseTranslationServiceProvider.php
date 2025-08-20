<?php

namespace App\Providers;

use App\Models\Translation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;

class DatabaseTranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Override the default translator with our database translator
        $this->app->extend('translator', function ($translator, $app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new DatabaseTranslator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

/**
 * Custom Database Translator
 */
class DatabaseTranslator extends Translator
{
    /**
     * Get the translation for the given key.
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // Try to get from database first
        $translation = Translation::getTranslation($key, $locale);

        if ($translation !== null) {
            return $this->makeReplacements($translation, $replace);
        }

        // Fallback to file-based translations
        return parent::get($key, $replace, $locale, $fallback);
    }

    /**
     * Get translations for a group
     */
    protected function loadGroup($namespace, $group, $locale)
    {
        // Try to load from database first
        $dbTranslations = Translation::getGroupTranslations($group, $locale);

        if (!empty($dbTranslations)) {
            return $dbTranslations;
        }

        // Fallback to file-based translations
        return parent::loadGroup($namespace, $group, $locale);
    }

    /**
     * Check if translation exists in database
     */
    public function hasInDatabase($key, $locale = null): bool
    {
        $locale = $locale ?: $this->locale;
        return Translation::getTranslation($key, $locale) !== null;
    }

    /**
     * Set translation in database
     */
    public function setInDatabase($key, $value, $locale = null): void
    {
        $locale = $locale ?: $this->locale;
        Translation::setTranslation($key, $value, $locale);
    }
}
