<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileChangeRequestModel extends Model
{
    protected $table            = 'profile_change_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'new_full_name', 'new_department', 
        'requested_at', 'is_approved', 'is_rejected', 'admin_comment'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'requested_at';
    protected $updatedField  = '';
}
