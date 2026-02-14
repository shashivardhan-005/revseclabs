<?php

if (!function_exists('validate_password_complexity')) {
    /**
     * Validate password against configured complexity requirements
     * 
     * @param string $password The password to validate
     * @return array ['valid' => bool, 'errors' => array, 'requirements' => array]
     */
    function validate_password_complexity($password)
    {
        $settings = model('SettingModel')->getAllGrouped();
        
        $minLength = $settings['password_min_length'] ?? 8;
        $requireUppercase = ($settings['password_require_uppercase'] ?? '1') === '1';
        $requireLowercase = ($settings['password_require_lowercase'] ?? '1') === '1';
        $requireNumbers = ($settings['password_require_numbers'] ?? '1') === '1';
        $requireSpecial = ($settings['password_require_special'] ?? '0') === '1';
        
        $errors = [];
        $requirements = [];
        
        // Check minimum length
        $requirements['length'] = "At least {$minLength} characters";
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long";
        }
        
        // Check uppercase
        if ($requireUppercase) {
            $requirements['uppercase'] = "At least one uppercase letter (A-Z)";
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Password must contain at least one uppercase letter";
            }
        }
        
        // Check lowercase
        if ($requireLowercase) {
            $requirements['lowercase'] = "At least one lowercase letter (a-z)";
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = "Password must contain at least one lowercase letter";
            }
        }
        
        // Check numbers
        if ($requireNumbers) {
            $requirements['numbers'] = "At least one number (0-9)";
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Password must contain at least one number";
            }
        }
        
        // Check special characters
        if ($requireSpecial) {
            $requirements['special'] = "At least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?)";
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
                $errors[] = "Password must contain at least one special character";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'requirements' => $requirements,
            'settings' => [
                'min_length' => $minLength,
                'require_uppercase' => $requireUppercase,
                'require_lowercase' => $requireLowercase,
                'require_numbers' => $requireNumbers,
                'require_special' => $requireSpecial,
            ]
        ];
    }
}

if (!function_exists('get_password_requirements')) {
    /**
     * Get current password requirements as array
     * 
     * @return array
     */
    function get_password_requirements()
    {
        $validation = validate_password_complexity('');
        return $validation['requirements'];
    }
}

if (!function_exists('get_password_requirements_text')) {
    /**
     * Get human-readable password requirements text
     * 
     * @return string
     */
    function get_password_requirements_text()
    {
        $requirements = get_password_requirements();
        
        if (empty($requirements)) {
            return 'No specific requirements';
        }
        
        return 'Password must contain: ' . implode(', ', $requirements);
    }
}

if (!function_exists('get_password_settings')) {
    /**
     * Get password complexity settings
     * 
     * @return array
     */
    function get_password_settings()
    {
        $validation = validate_password_complexity('');
        return $validation['settings'];
    }
}
