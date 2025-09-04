<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;
use App\Services\TranslationVersionService;
use Illuminate\Support\Facades\File;

class TranslationsExportStatic extends Command
{
    protected $signature = 'translations:export-static {locale?} {--force : Overwrite existing hashed files}';
    protected $description = 'Export translations to public/translations/{locale}/{group}.{hash}.json for CDN immutable caching';

    public function handle(): int
    {
        $locale = $this->argument('locale');
        $locales = $locale ? [$locale] : Translation::distinct()->pluck('locale')->filter()->values()->all();
        $basePath = public_path('translations');
        File::ensureDirectoryExists($basePath);
        $totalFiles = 0;
        foreach ($locales as $loc) {
            $manifest = TranslationVersionService::getManifest($loc);
            $locDir = $basePath . DIRECTORY_SEPARATOR . $loc;
            File::ensureDirectoryExists($locDir);
            foreach ($manifest['groups'] as $group => $hash) {
                $filename = $group . '.' . $hash . '.json';
                $full = $locDir . DIRECTORY_SEPARATOR . $filename;
                if (File::exists($full) && !$this->option('force')) {
                    $this->line("Skip existing {$loc}/{$filename}");
                    continue;
                }
                $data = Translation::active()->forLocale($loc)->forGroup($group)->pluck('content','key')->toArray();
                // Build nested structure
                $nested = [];
                foreach ($data as $key => $val) {
                    $parts = explode('.', $key);
                    $cursor =& $nested;
                    foreach ($parts as $p) {
                        if (!isset($cursor[$p]) || !is_array($cursor[$p])) {
                            $cursor[$p] = [];
                        }
                        $cursor =& $cursor[$p];
                    }
                    $cursor = $val;
                }
                File::put($full, json_encode([$group => $nested[$group] ?? $nested], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $this->info("Written {$loc}/{$filename}");
                $totalFiles++;
            }
        }
        $this->info("Export complete: {$totalFiles} files");
        return Command::SUCCESS;
    }
}
