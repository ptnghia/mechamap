<?php

namespace App\Services;

use App\Models\ShowcaseSetting;
use Illuminate\Support\Facades\Cache;

class ShowcaseSettingService
{
    /**
     * Get all search filters for showcase filtering.
     */
    public function getSearchFilters(): array
    {
        return Cache::remember('showcase_search_filters_formatted', 3600, function () {
            $settings = ShowcaseSetting::searchable()->ordered()->get();
            
            $filters = [];
            
            foreach ($settings as $setting) {
                $options = $this->formatOptionsForFilter($setting);
                
                $filters[$setting->key] = [
                    'name' => $setting->name,
                    'options' => $options,
                    'input_type' => $setting->input_type,
                    'is_multiple' => $setting->is_multiple,
                    'group' => $setting->group,
                    'icon' => $setting->icon,
                ];
            }
            
            return $filters;
        });
    }

    /**
     * Format options for filter dropdown.
     */
    private function formatOptionsForFilter(ShowcaseSetting $setting): array
    {
        $options = [
            [
                'value' => '',
                'label' => __('showcase.all_' . str_replace('_', '', $setting->key))
            ]
        ];
        
        foreach ($setting->getTranslatedOptions() as $option) {
            $options[] = [
                'value' => $option['value'],
                'label' => $option['label'],
                'icon' => $option['icon'] ?? null,
            ];
        }
        
        return $options;
    }

    /**
     * Get form fields for showcase creation/editing.
     */
    public function getFormFields(): array
    {
        return Cache::remember('showcase_form_fields', 3600, function () {
            $settings = ShowcaseSetting::active()->ordered()->get();
            
            $fields = [];
            
            foreach ($settings as $setting) {
                $fields[$setting->key] = [
                    'name' => $setting->name,
                    'description' => $setting->description,
                    'options' => $setting->getTranslatedOptions(),
                    'input_type' => $setting->input_type,
                    'is_multiple' => $setting->is_multiple,
                    'is_required' => $setting->is_required,
                    'default_value' => $setting->getDefaultValue(),
                    'group' => $setting->group,
                    'icon' => $setting->icon,
                ];
            }
            
            return $fields;
        });
    }

    /**
     * Get grouped form fields.
     */
    public function getGroupedFormFields(): array
    {
        $fields = $this->getFormFields();
        $grouped = [];
        
        foreach ($fields as $key => $field) {
            $group = $field['group'] ?? 'general';
            $grouped[$group][$key] = $field;
        }
        
        return $grouped;
    }

    /**
     * Validate showcase data against settings.
     */
    public function validateShowcaseData(array $data): array
    {
        $errors = [];
        $settings = ShowcaseSetting::active()->get()->keyBy('key');
        
        foreach ($settings as $key => $setting) {
            $value = $data[$key] ?? null;
            
            // Check required fields
            if ($setting->is_required && empty($value)) {
                $errors[$key] = "Field {$setting->name} is required.";
                continue;
            }
            
            // Validate value against options
            if (!empty($value) && !$setting->validateValue($value)) {
                $errors[$key] = "Invalid value for {$setting->name}.";
            }
        }
        
        return $errors;
    }

    /**
     * Process showcase data before saving.
     */
    public function processShowcaseData(array $data): array
    {
        $settings = ShowcaseSetting::active()->get()->keyBy('key');
        $processed = [];
        
        foreach ($settings as $key => $setting) {
            if (isset($data[$key])) {
                $value = $data[$key];
                
                // Handle multiple values
                if ($setting->is_multiple && !is_array($value)) {
                    $value = $value ? [$value] : [];
                }
                
                // Handle single values
                if (!$setting->is_multiple && is_array($value)) {
                    $value = !empty($value) ? $value[0] : null;
                }
                
                $processed[$key] = $value;
            } else if ($setting->is_required) {
                $processed[$key] = $setting->getDefaultValue();
            }
        }
        
        return $processed;
    }

    /**
     * Get options for a specific setting key.
     */
    public function getOptionsForKey(string $key): array
    {
        return ShowcaseSetting::getOptionsForKey($key);
    }

    /**
     * Get setting by key.
     */
    public function getSettingByKey(string $key): ?ShowcaseSetting
    {
        return ShowcaseSetting::getByKey($key);
    }

    /**
     * Clear all caches.
     */
    public function clearCache(): void
    {
        Cache::forget('showcase_settings');
        Cache::forget('showcase_search_filters');
        Cache::forget('showcase_search_filters_formatted');
        Cache::forget('showcase_form_fields');
    }

    /**
     * Get statistics about settings usage.
     */
    public function getUsageStatistics(): array
    {
        // This would require analyzing actual showcase data
        // For now, return basic info
        return [
            'total_settings' => ShowcaseSetting::count(),
            'active_settings' => ShowcaseSetting::active()->count(),
            'searchable_settings' => ShowcaseSetting::searchable()->count(),
            'groups' => ShowcaseSetting::getGroups(),
        ];
    }

    /**
     * Export settings for backup or migration.
     */
    public function exportSettings(): array
    {
        return ShowcaseSetting::all()->toArray();
    }

    /**
     * Import settings from array.
     */
    public function importSettings(array $settings): void
    {
        foreach ($settings as $settingData) {
            ShowcaseSetting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
        
        $this->clearCache();
    }

    /**
     * Create or update a setting.
     */
    public function createOrUpdateSetting(array $data): ShowcaseSetting
    {
        $setting = ShowcaseSetting::updateOrCreate(
            ['key' => $data['key']],
            $data
        );
        
        $this->clearCache();
        
        return $setting;
    }

    /**
     * Delete a setting.
     */
    public function deleteSetting(string $key): bool
    {
        $setting = ShowcaseSetting::where('key', $key)->first();
        
        if ($setting) {
            $setting->delete();
            $this->clearCache();
            return true;
        }
        
        return false;
    }

    /**
     * Get settings for admin interface.
     */
    public function getAdminSettings(): array
    {
        return ShowcaseSetting::orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group')
            ->toArray();
    }
}
