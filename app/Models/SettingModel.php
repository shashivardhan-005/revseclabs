<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'setting_key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['setting_key', 'setting_value', 'setting_group'];

    public function getByKey($key)
    {
        $setting = $this->find($key);
        return $setting ? $setting['setting_value'] : null;
    }

    public function updateByKey($key, $value)
    {
        // Try to find it first, if not found, we'll insert it
        $existing = $this->find($key);
        
        $data = [
            'setting_key' => $key,
            'setting_value' => $value
        ];

        // If it's a new setting, we might want to default the group to 'general'
        if (!$existing) {
            $data['setting_group'] = 'general';
        }

        return $this->save($data);
    }

    public function getAllGrouped()
    {
        $settings = $this->findAll();
        $grouped = [];
        foreach ($settings as $s) {
            $grouped[$s['setting_key']] = $s['setting_value'];
        }
        return $grouped;
    }
}
