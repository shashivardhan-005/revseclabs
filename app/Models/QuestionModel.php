<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table            = 'questions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'text', 'question_type', 'explanation', 'image_base64', 
        'topic_id', 'difficulty'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getQuestionsByTopic($topicId, $limit = 10)
    {
        return $this->where('topic_id', $topicId)
                    ->orderBy('id', 'RANDOM')
                    ->findAll($limit);
    }
}
