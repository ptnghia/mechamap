<?php

if (!function_exists('get_image_url')) {
    /**
     * Get image URL with fallback to no-image.svg
     *
     * @param string|null $path The image path
     * @param string $default The default image path
     * @return string
     */
    function get_image_url($path = null, $default = 'images/no-image.svg')
    {
        // If path is empty or null, return default image
        if (empty($path)) {
            return asset($default);
        }

        // Check if path is a URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Check if path is a storage path
        if (strpos($path, 'storage/') === 0) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
            return asset($default);
        }

        // Check if file exists in public directory
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset($default);
    }
}

if (!function_exists('get_avatar_url')) {
    /**
     * Get user avatar URL with fallback to no-image.svg
     *
     * @param mixed $user User object or URL
     * @param string $default The default image path
     * @return string
     */
    function get_avatar_url($user, $default = 'images/no-image.svg')
    {
        if (is_string($user)) {
            return get_image_url($user, $default);
        }

        if (is_object($user)) {
            if (method_exists($user, 'getAvatarUrl')) {
                return $user->getAvatarUrl();
            }

            if (isset($user->profile_photo_url)) {
                return $user->profile_photo_url;
            }

            if (isset($user->avatar)) {
                return get_image_url($user->avatar, $default);
            }
        }

        return asset($default);
    }
}
