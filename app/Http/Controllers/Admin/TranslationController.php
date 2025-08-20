<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\TranslationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class TranslationController extends Controller
{
    /**
     * Display translations list
     */
    public function index(Request $request)
    {
        $query = Translation::with(['creator', 'updater']);

        // Filters
        if ($request->filled('locale')) {
            $query->forLocale($request->locale);
        }

        if ($request->filled('group')) {
            $query->forGroup($request->group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $translations = $query->orderBy('group_name')
                             ->orderBy('key')
                             ->paginate(50);

        // Get available groups and locales
        $groups = Translation::distinct()->pluck('group_name')->sort();
        $locales = Translation::distinct()->pluck('locale')->sort();

        return view('admin.translations.index', compact('translations', 'groups', 'locales'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $groups = Translation::distinct()->pluck('group_name')->sort();
        $locales = ['vi', 'en']; // Add more as needed

        return view('admin.translations.create', compact('groups', 'locales'));
    }

    /**
     * Store new translation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'content' => 'required|string',
            'locale' => 'required|string|max:10',
            'group_name' => 'required|string|max:100',
            'namespace' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if translation already exists
        $exists = Translation::where('key', $request->key)
                            ->where('locale', $request->locale)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['key' => 'Translation already exists for this key and locale.'])->withInput();
        }

        $translation = Translation::create([
            'key' => $request->key,
            'content' => $request->content,
            'locale' => $request->locale,
            'group_name' => $request->group_name,
            'namespace' => $request->namespace,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Record history
        $translation->recordHistory(null, $request->content, 'Initial creation');

        return redirect()->route('admin.translations.index')
                        ->with('success', 'Translation created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Translation $translation)
    {
        $groups = Translation::distinct()->pluck('group_name')->sort();
        $locales = ['vi', 'en'];

        return view('admin.translations.edit', compact('translation', 'groups', 'locales'));
    }

    /**
     * Update translation
     */
    public function update(Request $request, Translation $translation)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'group_name' => 'required|string|max:100',
            'namespace' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'change_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldContent = $translation->content;
        
        $translation->update([
            'content' => $request->content,
            'group_name' => $request->group_name,
            'namespace' => $request->namespace,
            'is_active' => $request->boolean('is_active', true),
            'updated_by' => auth()->id(),
        ]);

        // Record history if content changed
        if ($oldContent !== $request->content) {
            $translation->recordHistory($oldContent, $request->content, $request->change_reason);
        }

        return redirect()->route('admin.translations.index')
                        ->with('success', 'Translation updated successfully.');
    }

    /**
     * Delete translation
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();

        return redirect()->route('admin.translations.index')
                        ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Show translation history
     */
    public function history(Translation $translation)
    {
        $history = $translation->history()
                              ->with('changedBy')
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);

        return view('admin.translations.history', compact('translation', 'history'));
    }

    /**
     * Bulk operations
     */
    public function bulk(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return back()->with('error', 'No translations selected.');
        }

        switch ($action) {
            case 'activate':
                Translation::whereIn('id', $ids)->update(['is_active' => true]);
                return back()->with('success', 'Translations activated successfully.');

            case 'deactivate':
                Translation::whereIn('id', $ids)->update(['is_active' => false]);
                return back()->with('success', 'Translations deactivated successfully.');

            case 'delete':
                Translation::whereIn('id', $ids)->delete();
                return back()->with('success', 'Translations deleted successfully.');

            default:
                return back()->with('error', 'Invalid action.');
        }
    }

    /**
     * Import translations from file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json,csv',
            'locale' => 'required|string|max:10',
            'group' => 'nullable|string|max:100',
            'overwrite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $file = $request->file('file');
        $locale = $request->locale;
        $group = $request->group;
        $overwrite = $request->boolean('overwrite', false);

        try {
            $content = file_get_contents($file->getPathname());
            
            if ($file->getClientOriginalExtension() === 'json') {
                $translations = json_decode($content, true);
            } else {
                // Handle CSV import
                $translations = $this->parseCsvTranslations($content);
            }

            if (!is_array($translations)) {
                throw new \Exception('Invalid file format.');
            }

            $result = $this->importTranslations($translations, $locale, $group, $overwrite);

            return back()->with('success', "Import completed. {$result['imported']} imported, {$result['failed']} failed.");

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export translations
     */
    public function export(Request $request)
    {
        $locale = $request->locale ?? 'vi';
        $group = $request->group;
        $format = $request->format ?? 'json';

        $translations = Translation::exportTranslations($locale, $group);

        $filename = "translations_{$locale}" . ($group ? "_{$group}" : '') . ".{$format}";

        if ($format === 'json') {
            return response()->json($translations)
                          ->header('Content-Disposition', "attachment; filename={$filename}");
        } else {
            // CSV export
            $csv = $this->generateCsv($translations);
            return response($csv)
                          ->header('Content-Type', 'text/csv')
                          ->header('Content-Disposition', "attachment; filename={$filename}");
        }
    }

    /**
     * Sync from files
     */
    public function syncFromFiles()
    {
        try {
            $result = Translation::syncFromFiles();
            
            return back()->with('success', "Sync completed. {$result['synced']} translations synced.");
        } catch (\Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        Cache::tags(['translations'])->flush();
        
        return back()->with('success', 'Translation cache cleared successfully.');
    }

    /**
     * Import translations helper
     */
    private function importTranslations(array $translations, string $locale, ?string $group, bool $overwrite): array
    {
        $imported = 0;
        $failed = 0;

        foreach ($translations as $key => $content) {
            try {
                if ($group && !str_starts_with($key, $group . '.')) {
                    $key = "{$group}.{$key}";
                }

                $exists = Translation::where('key', $key)->where('locale', $locale)->exists();

                if ($exists && !$overwrite) {
                    continue;
                }

                Translation::setTranslation($key, $content, $locale);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return ['imported' => $imported, 'failed' => $failed];
    }

    /**
     * Parse CSV translations
     */
    private function parseCsvTranslations(string $content): array
    {
        $lines = str_getcsv($content, "\n");
        $translations = [];

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) >= 2) {
                $translations[$data[0]] = $data[1];
            }
        }

        return $translations;
    }

    /**
     * Generate CSV content
     */
    private function generateCsv(array $translations): string
    {
        $csv = "Key,Content\n";
        
        foreach ($translations as $key => $content) {
            $csv .= '"' . str_replace('"', '""', $key) . '","' . str_replace('"', '""', $content) . "\"\n";
        }

        return $csv;
    }
}
