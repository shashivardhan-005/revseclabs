<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizTopics extends Migration
{
    public function up()
    {
        // 1. Create the quiz_topics table
        $this->forge->addField([
            'quiz_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
            ],
            'topic_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
            ],
        ]);
        $this->forge->addKey(['quiz_id', 'topic_id'], true);
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('topic_id', 'topics', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_topics');

        // 2. Migrate existing data from quizzes.topic_id to quiz_topics
        $db = \Config\Database::connect();
        $quizzes = $db->table('quizzes')->select('id, topic_id')->get()->getResultArray();
        
        foreach ($quizzes as $quiz) {
            if ($quiz['topic_id']) {
                $db->table('quiz_topics')->insert([
                    'quiz_id' => $quiz['id'],
                    'topic_id' => $quiz['topic_id']
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('quiz_topics');
    }
}
