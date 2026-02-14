<?php

namespace App\Controllers;

class Api extends BaseController
{
    /**
     * Get password complexity settings for frontend validation
     */
    public function passwordSettings()
    {
        helper('password');
        $settings = get_password_settings();
        
        return $this->response->setJSON($settings);
    }
}
