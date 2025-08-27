<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseSetting;
use App\Services\ShowcaseSettingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ShowcaseSettingController extends Controller
{
    protected ShowcaseSettingService $settingService;

    public function __construct(ShowcaseSettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->middleware(['auth', 'role:super_admin|admin']);
    }

    /**
     * Display a listing of showcase settings.
     */
    public function index(): View
    {
        $settings = $this->settingService->getAdminSettings();
        $statistics = $this->settingService->getUsageStatistics();
        
        return view('admin.showcase-settings.index', compact('settings', 'statistics'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create(): View
    {
        $groups = ShowcaseSetting::getGroups();
        $inputTypes = ['select', 'multiselect', 'checkbox', 'radio', 'tags'];
        
        return view('admin.showcase-settings.create', compact('groups', 'inputTypes'));
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:showcase_settings,key|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:1',
            'options.*.value' => 'required|string',
            'options.*.translations' => 'required|array',
            'options.*.translations.vi' => 'required|string',
            'options.*.translations.en' => 'required|string',
            'options.*.description' => 'nullable|string',
            'options.*.icon' => 'nullable|string',
            'default_value' => 'nullable|array',
            'input_type' => 'required|in:select,multiselect,checkbox,radio,tags',
            'is_multiple' => 'boolean',
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'group' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $this->settingService->createOrUpdateSetting($validated);

        return redirect()->route('admin.showcase-settings.index')
            ->with('success', 'Setting created successfully!');
    }

    /**
     * Display the specified setting.
     */
    public function show(ShowcaseSetting $showcaseSetting): View
    {
        return view('admin.showcase-settings.show', compact('showcaseSetting'));
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit(ShowcaseSetting $showcaseSetting): View
    {
        $groups = ShowcaseSetting::getGroups();
        $inputTypes = ['select', 'multiselect', 'checkbox', 'radio', 'tags'];
        
        return view('admin.showcase-settings.edit', compact('showcaseSetting', 'groups', 'inputTypes'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, ShowcaseSetting $showcaseSetting): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:showcase_settings,key,' . $showcaseSetting->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:1',
            'options.*.value' => 'required|string',
            'options.*.translations' => 'required|array',
            'options.*.translations.vi' => 'required|string',
            'options.*.translations.en' => 'required|string',
            'options.*.description' => 'nullable|string',
            'options.*.icon' => 'nullable|string',
            'default_value' => 'nullable|array',
            'input_type' => 'required|in:select,multiselect,checkbox,radio,tags',
            'is_multiple' => 'boolean',
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'group' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $showcaseSetting->update($validated);
        $this->settingService->clearCache();

        return redirect()->route('admin.showcase-settings.index')
            ->with('success', 'Setting updated successfully!');
    }

    /**
     * Remove the specified setting.
     */
    public function destroy(ShowcaseSetting $showcaseSetting): RedirectResponse
    {
        $showcaseSetting->delete();
        $this->settingService->clearCache();

        return redirect()->route('admin.showcase-settings.index')
            ->with('success', 'Setting deleted successfully!');
    }

    /**
     * Toggle setting active status.
     */
    public function toggleActive(ShowcaseSetting $showcaseSetting): JsonResponse
    {
        $showcaseSetting->update(['is_active' => !$showcaseSetting->is_active]);
        $this->settingService->clearCache();

        return response()->json([
            'success' => true,
            'is_active' => $showcaseSetting->is_active,
            'message' => 'Setting status updated successfully!'
        ]);
    }

    /**
     * Update sort order of settings.
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:showcase_settings,id',
            'settings.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['settings'] as $settingData) {
            ShowcaseSetting::where('id', $settingData['id'])
                ->update(['sort_order' => $settingData['sort_order']]);
        }

        $this->settingService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Sort order updated successfully!'
        ]);
    }

    /**
     * Export settings as JSON.
     */
    public function export(): JsonResponse
    {
        $settings = $this->settingService->exportSettings();
        
        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="showcase-settings-' . date('Y-m-d') . '.json"');
    }

    /**
     * Import settings from JSON.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json|max:2048',
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->getRealPath());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format');
            }

            $this->settingService->importSettings($settings);

            return redirect()->route('admin.showcase-settings.index')
                ->with('success', 'Settings imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.showcase-settings.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear all caches.
     */
    public function clearCache(): JsonResponse
    {
        $this->settingService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully!'
        ]);
    }

    /**
     * Get setting options via AJAX.
     */
    public function getOptions(string $key): JsonResponse
    {
        $options = $this->settingService->getOptionsForKey($key);
        
        return response()->json([
            'success' => true,
            'options' => $options
        ]);
    }

    /**
     * Validate setting value via AJAX.
     */
    public function validateValue(Request $request, string $key): JsonResponse
    {
        $setting = $this->settingService->getSettingByKey($key);
        
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $value = $request->input('value');
        $isValid = $setting->validateValue($value);

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'message' => $isValid ? 'Valid value' : 'Invalid value for this setting'
        ]);
    }
}
