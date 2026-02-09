<?php

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        $settingModel = new \App\Models\SettingModel();
        return $settingModel->getByKey($key) ?? $default;
    }
}
