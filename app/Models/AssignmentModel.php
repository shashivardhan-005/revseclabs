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
        'completed_at', 'score', 'result_email_sent', 'certificate_sent', 'retest_requested',
        'retest_count', 'retest_rejected'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'assigned_at';
    protected $updatedField  = '';

    public function getAssignmentsByUser($userId)
    {
        return $this->select('quiz_assignments.*, quizzes.name as quiz_name, quizzes.total_questions, quizzes.duration_minutes, quizzes.start_time, quizzes.end_time, quizzes.pass_score, quizzes.results_released, quizzes.difficulty, GROUP_CONCAT(topics.name SEPARATOR ", ") as topic_display')
                    ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
                    ->join('quiz_topics', 'quiz_topics.quiz_id = quizzes.id', 'left')
                    ->join('topics', 'topics.id = quiz_topics.topic_id', 'left')
                    ->where('quiz_assignments.user_id', $userId)
                    ->groupBy('quiz_assignments.id')
                    ->orderBy('quiz_assignments.assigned_at', 'DESC')
                    ->findAll();
    }
}
