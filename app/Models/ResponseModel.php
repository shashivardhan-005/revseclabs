<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table            = 'user_responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'attempt_id', 'question_id', 'selected_option_id', 'is_correct'
    ];

    protected array $casts = [
        'is_correct' => 'boolean',
    ];
}
