<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Create Admin User
        $userData = [
            'email'               => 'admin@revseclabs.in',
            'password'            => password_hash('admin123', PASSWORD_DEFAULT),
            'first_name'          => 'Admin',
            'last_name'           => 'User',
            'is_staff'            => true,
            'is_active'           => true,
            'is_password_changed' => true
        ];
        $db->table('users')->insert($userData);
        $userId = $db->insertID();

        // 2. Create Topic
        $topicData = ['name' => 'Cybersecurity Basics'];
        $db->table('topics')->insert($topicData);
        $topicId = $db->insertID();

        // 3. Create Questions
        $questions = [
            [
                'text'          => 'What does "Phishing" refer to?',
                'question_type' => 'MCQ',
                'explanation'   => 'Phishing is a fraudulent attempt to obtain sensitive information by disguising as a trustworthy entity.',
                'topic_id'      => $topicId,
                'difficulty'    => 'EASY'
            ],
            [
                'text'          => 'Is 123456 a secure password?',
                'question_type' => 'TF',
                'explanation'   => 'No, it is one of the most commonly used and easily cracked passwords.',
                'topic_id'      => $topicId,
                'difficulty'    => 'EASY'
            ]
        ];

        foreach ($questions as $q) {
            $db->table('questions')->insert($q);
            $qId = $db->insertID();

            if ($q['question_type'] == 'MCQ') {
                $options = [
                    ['question_id' => $qId, 'text' => 'A type of fish', 'is_correct' => false],
                    ['question_id' => $qId, 'text' => 'A social engineering attack', 'is_correct' => true],
                    ['question_id' => $qId, 'text' => 'A networking protocol', 'is_correct' => false],
                    ['question_id' => $qId, 'text' => 'A software bug', 'is_correct' => false],
                ];
            } else {
                $options = [
                    ['question_id' => $qId, 'text' => 'True', 'is_correct' => false],
                    ['question_id' => $qId, 'text' => 'False', 'is_correct' => true],
                ];
            }
            $db->table('options')->insertBatch($options);
        }

        // 4. Create Quiz
        $quizData = [
            'name'                     => 'Monthly Awareness Quiz',
            'month'                    => date('Y-m-01'),
            'start_time'               => date('Y-m-d H:i:s', strtotime('-1 day')),
            'end_time'                 => date('Y-m-d H:i:s', strtotime('+30 days')),
            'duration_minutes'         => 15,
            'total_questions'          => 2,
            'topic_id'                 => $topicId,
            'difficulty'               => 'EASY',
            'force_full_screen'        => true,
            'detect_tab_switch'        => true,
            'disable_copy_paste'       => true,
            'auto_submit_on_violation' => false,
            'violation_limit'          => 3,
            'results_released'         => false
        ];
        $db->table('quizzes')->insert($quizData);
        $quizId = $db->insertID();

        // 5. Assign Quiz to User
        $db->table('quiz_assignments')->insert([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'status'  => 'ASSIGNED'
        ]);
    }
}
