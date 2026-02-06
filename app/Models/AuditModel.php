<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'action', 'timestamp', 'ip_address', 'details'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'timestamp';
    protected $updatedField  = '';
}
