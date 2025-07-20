<?php

/**
 * Translation Helper Functions
 * Add these to app/Helpers/TranslationHelper.php
 */

if (!function_exists('t_core')) {
    function t_core($key, $replace = [], $locale = null) {
        return __('core.' . $key, $replace, $locale);
    }
}

if (!function_exists('t_ui')) {
    function t_ui($key, $replace = [], $locale = null) {
        return __('ui.' . $key, $replace, $locale);
    }
}

if (!function_exists('t_content')) {
    function t_content($key, $replace = [], $locale = null) {
        return __('content.' . $key, $replace, $locale);
    }
}

if (!function_exists('t_feature')) {
    function t_feature($key, $replace = [], $locale = null) {
        return __('features.' . $key, $replace, $locale);
    }
}

if (!function_exists('t_user')) {
    function t_user($key, $replace = [], $locale = null) {
        return __('user.' . $key, $replace, $locale);
    }
}

if (!function_exists('t_admin')) {
    function t_admin($key, $replace = [], $locale = null) {
        return __('admin.' . $key, $replace, $locale);
    }
}
