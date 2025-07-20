<?php

/**
 * Translation Helper Functions
 * Generated: 2025-07-20 12:58:20
 */

if (!function_exists('trans_key')) {
    /**
     * Translate using key mapping
     * 
     * @param string $key Original key (e.g., 'UI.COMMON.TRENDING')
     * @param array $replace
     * @param string $locale
     * @return string
     */
    function trans_key($key, $replace = [], $locale = null) {
        $mapping = include resource_path('lang/key_mapping.php');
        
        if (isset($mapping[$key])) {
            return __($mapping[$key], $replace, $locale);
        }
        
        // Fallback to original key
        return __($key, $replace, $locale);
    }
}

if (!function_exists('has_trans_key')) {
    /**
     * Check if translation key exists
     * 
     * @param string $key
     * @return bool
     */
    function has_trans_key($key) {
        $mapping = include resource_path('lang/key_mapping.php');
        
        if (isset($mapping[$key])) {
            $result = __($mapping[$key]);
            return $result !== $mapping[$key];
        }
        
        $result = __($key);
        return $result !== $key;
    }
}
