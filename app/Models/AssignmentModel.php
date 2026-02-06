<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table            = 'quiz_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'quiz_id', 'status', 'assigned_at', 
        'completed_at', 'score', 'result_email_sent', 'certificate_sent', 'retest_requested'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'assigned_at';
    protected $updatedField  = '';

    public function getAssignmentsByUser($userId)
    {
        return $this->select('quiz_assignments.*, quizzes.name as quiz_name, quizzes.duration_minutes, quizzes.start_time, quizzes.end_time, quizzes.pass_score, quizzes.results_released, topics.name as topic_name, quizzes.difficulty')
                    ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
                    ->join('topics', 'topics.id = quizzes.topic_id', 'left')
                    ->where('user_id', $userId)
                    ->findAll();
    }
}
