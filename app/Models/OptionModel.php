<?php

namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table            = 'options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['question_id', 'text', 'is_correct'];

    protected array $casts = [
        'is_correct' => 'boolean',
    ];
}
