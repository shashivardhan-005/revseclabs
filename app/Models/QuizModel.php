<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table            = 'quizzes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'month', 'start_time', 'end_time', 'duration_minutes', 
        'total_questions', 'pass_score', 'topic_id', 'difficulty', 'force_full_screen', 
        'detect_tab_switch', 'disable_copy_paste', 'auto_submit_on_violation', 
        'violation_limit', 'results_released'
    ];

    // Dates
    protected $useTimestamps = false;

    public function getActiveQuizzes()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('start_time <=', $now)
                    ->where('end_time >=', $now)
                    ->findAll();
    }
}
