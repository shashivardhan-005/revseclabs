<?php

namespace App\Models;

use CodeIgniter\Model;

class AttemptModel extends Model
{
    protected $table            = 'quiz_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // UUID
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id', 'assignment_id', 'start_time', 'end_time', 
        'score', 'full_screen_violations', 'tab_switch_violations',
        'violation_auto_submitted'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'start_time';
    protected $updatedField  = '';
}
