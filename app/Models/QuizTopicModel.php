<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizTopicModel extends Model
{
    protected $table            = 'quiz_topics';
    protected $allowedFields    = ['quiz_id', 'topic_id'];
    protected $useTimestamps    = false;

    public function getTopicsForQuiz($quizId)
    {
        return $this->where('quiz_id', $quizId)->findAll();
    }

    public function syncTopics($quizId, array $topicIds)
    {
        $this->where('quiz_id', $quizId)->delete();
        foreach ($topicIds as $topicId) {
            $this->insert([
                'quiz_id' => $quizId,
                'topic_id' => $topicId
            ]);
        }
    }
}
