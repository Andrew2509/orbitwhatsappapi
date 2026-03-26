<?php

use App\Models\SiteSetting;

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
            return SiteSetting::get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
