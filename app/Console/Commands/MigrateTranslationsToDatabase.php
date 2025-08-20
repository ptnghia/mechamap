<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateTranslationsToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:migrate-to-db 
                            {--locale= : Specific locale to migrate (default: all)}
                            {--group= : Specific group to migrate (default: all)}
                            {--overwrite : Overwrite existing translations}
                            {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate translations from PHP files to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 MechaMap Translation Migration Tool');
        $this->info('=====================================');

        $dryRun = $this->option('dry-run');
        $overwrite = $this->option('overwrite');
        $targetLocale = $this->option('locale');
        $targetGroup = $this->option('group');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $langPath = resource_path('lang');
        
        if (!File::exists($langPath)) {
            $this->error('❌ Language directory not found: ' . $langPath);
            return 1;
        }

        $locales = $targetLocale ? [$targetLocale] : $this->getAvailableLocales($langPath);
        
        $totalMigrated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($locales as $locale) {
            $this->info("\n📁 Processing locale: {$locale}");
            
            $localePath = "{$langPath}/{$locale}";
            
            if (!File::exists($localePath)) {
                $this->warn("⚠️  Locale directory not found: {$localePath}");
                continue;
            }

            $files = File::glob("{$localePath}/*.php");
            
            foreach ($files as $file) {
                $group = basename($file, '.php');
                
                if ($targetGroup && $group !== $targetGroup) {
                    continue;
                }

                $this->line("  📄 Processing group: {$group}");
                
                try {
                    $translations = include $file;
                    
                    if (!is_array($translations)) {
                        $this->warn("    ⚠️  Invalid translation file: {$file}");
                        $totalErrors++;
                        continue;
                    }

                    $result = $this->migrateGroup($translations, $locale, $group, $overwrite, $dryRun);
                    
                    $totalMigrated += $result['migrated'];
                    $totalSkipped += $result['skipped'];
                    $totalErrors += $result['errors'];

                    $this->line("    ✅ Migrated: {$result['migrated']}, Skipped: {$result['skipped']}, Errors: {$result['errors']}");

                } catch (\Exception $e) {
                    $this->error("    ❌ Error processing {$file}: " . $e->getMessage());
                    $totalErrors++;
                }
            }
        }

        $this->info("\n📊 Migration Summary:");
        $this->table(['Metric', 'Count'], [
            ['Total Migrated', $totalMigrated],
            ['Total Skipped', $totalSkipped],
            ['Total Errors', $totalErrors],
        ]);

        if ($dryRun) {
            $this->info('✅ Dry run completed - no changes made');
        } else {
            $this->info('✅ Migration completed successfully!');
        }

        return 0;
    }

    /**
     * Get available locales
     */
    private function getAvailableLocales(string $langPath): array
    {
        $locales = [];
        
        foreach (File::directories($langPath) as $dir) {
            $locales[] = basename($dir);
        }

        return $locales;
    }

    /**
     * Migrate a group of translations
     */
    private function migrateGroup(array $translations, string $locale, string $group, bool $overwrite, bool $dryRun): array
    {
        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        $flatTranslations = $this->flattenArray($translations, $group);

        foreach ($flatTranslations as $key => $content) {
            try {
                if ($dryRun) {
                    $this->line("      Would migrate: {$key} = " . substr($content, 0, 50) . '...');
                    $migrated++;
                    continue;
                }

                // Check if translation exists
                $exists = Translation::where('key', $key)
                                   ->where('locale', $locale)
                                   ->exists();

                if ($exists && !$overwrite) {
                    $skipped++;
                    continue;
                }

                // Create or update translation
                Translation::updateOrCreate(
                    ['key' => $key, 'locale' => $locale],
                    [
                        'content' => $content,
                        'group_name' => $group,
                        'is_active' => true,
                        'created_by' => 1, // System user
                        'updated_by' => 1,
                    ]
                );

                $migrated++;

            } catch (\Exception $e) {
                $this->error("      ❌ Error migrating {$key}: " . $e->getMessage());
                $errors++;
            }
        }

        return [
            'migrated' => $migrated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Flatten nested array with dot notation
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = (string) $value;
            }
        }

        return $result;
    }

    /**
     * Show statistics
     */
    private function showStatistics()
    {
        $this->info("\n📊 Current Database Statistics:");
        
        $stats = [
            ['Total Translations', Translation::count()],
            ['Active Translations', Translation::where('is_active', true)->count()],
            ['Vietnamese Translations', Translation::where('locale', 'vi')->count()],
            ['English Translations', Translation::where('locale', 'en')->count()],
            ['Unique Groups', Translation::distinct('group_name')->count()],
        ];

        $this->table(['Metric', 'Count'], $stats);

        // Show groups
        $groups = Translation::select('group_name', \DB::raw('count(*) as count'))
                           ->groupBy('group_name')
                           ->orderBy('count', 'desc')
                           ->get();

        if ($groups->isNotEmpty()) {
            $this->info("\n📁 Groups:");
            $groupData = $groups->map(function ($group) {
                return [$group->group_name, $group->count];
            })->toArray();

            $this->table(['Group', 'Count'], $groupData);
        }
    }
}
