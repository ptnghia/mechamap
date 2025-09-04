<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;

class TranslationsDetectConflicts extends Command
{
    protected $signature = 'translations:detect-conflicts {locale? : Locale to scan (default: all)}';
    protected $description = 'Detect key conflicts where a key is both a node (prefix) and a leaf value (scalar)';

    public function handle(): int
    {
        $localeArg = $this->argument('locale');
        $locales = $localeArg ? [$localeArg] : Translation::query()->distinct()->pluck('locale')->toArray();
        $conflicts = [];
        foreach ($locales as $locale) {
            $keys = Translation::query()->where('locale', $locale)->pluck('key')->toArray();
            $set = array_fill_keys($keys, true);
            foreach ($keys as $key) {
                $parts = explode('.', $key);
                $prefix = '';
                for ($i=0; $i < count($parts)-1; $i++) {
                    $prefix = $prefix ? $prefix . '.' . $parts[$i] : $parts[$i];
                    if (isset($set[$prefix])) {
                        $conflicts[$locale][] = [$prefix, $key];
                    }
                }
            }
        }
        if (empty($conflicts)) {
            $this->info('No conflicts detected.');
            return self::SUCCESS;
        }
        foreach ($conflicts as $locale => $pairs) {
            $this->warn("Locale: $locale");
            foreach ($pairs as [$prefix, $leaf]) {
                $this->line("  - Node/Leaf collision: '$prefix' conflicts with descendant '$leaf'");
            }
        }
        $this->line('Total locales with conflicts: '.count($conflicts));
        return self::SUCCESS;
    }
}
