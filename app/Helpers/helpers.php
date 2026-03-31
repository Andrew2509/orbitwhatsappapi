<?php

use App\Models\SiteSetting;
use App\Helpers\ImageHelper;

if (!function_exists('get_setting')) {
    /**
     * Get a site setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        try {
            $value = SiteSetting::get($key, $default);
            
            // If it's a branding/file key, ensure it's displayed correctly
            if (in_array($key, ['site_logo', 'site_logo_white', 'site_favicon'])) {
                return ImageHelper::display($value, $default);
            }
            
            return $value;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
