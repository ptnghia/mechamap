<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;
use App\Services\TranslationVersionService;
use Illuminate\Support\Facades\Cache;

class TranslationsWarm extends Command
{
    protected $signature = 'translations:warm {locale?} {--groups=* : Specific groups to warm}';
    protected $description = 'Warm translation cache (group + version/manifest)';

    public function handle(): int
    {
        $locale = $this->argument('locale');
        $locales = $locale ? [$locale] : Translation::distinct()->pluck('locale')->filter()->values()->all();
        $groupsOpt = $this->option('groups');
        foreach ($locales as $loc) {
            $manifest = TranslationVersionService::getManifest($loc); // caches version + group hashes
            $groups = $groupsOpt ?: array_keys($manifest['groups']);
            $this->info("Warming locale {$loc}: groups=" . implode(',', $groups));
            foreach ($groups as $g) {
                $cacheKey = "js_group_translations.{$loc}.{$g}";
                Cache::remember($cacheKey, config('translation.cache_ttl'), function () use ($loc, $g) {
                    $items = Translation::active()->forLocale($loc)->forGroup($g)->pluck('content','key')->toArray();
                    $result = [];
                    foreach ($items as $key => $content) {
                        $parts = explode('.', $key);
                        $cursor =& $result;
                        foreach ($parts as $p) {
                            if (!isset($cursor[$p]) || !is_array($cursor[$p])) $cursor[$p] = [];
                            $cursor =& $cursor[$p];
                        }
                        $cursor = $content;
                    }
                    return $result[$g] ?? ($result ?: []);
                });
            }
        }
        $this->info('Warm complete');
        return Command::SUCCESS;
    }
}
