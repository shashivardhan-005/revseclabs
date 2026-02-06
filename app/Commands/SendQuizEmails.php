<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AssignmentModel;
use App\Models\QuizModel;
use App\Models\UserModel;
use App\Libraries\EmailLibrary;

class SendQuizEmails extends BaseCommand
{
    protected $group       = 'Quiz';
    protected $name        = 'quiz:send-results';
    protected $description = 'Sends result emails for released quizzes.';

    public function run(array $params)
    {
        $quizModel = new QuizModel();
        $assignmentModel = new AssignmentModel();
        $userModel = new UserModel();
        $emailLib = new EmailLibrary();

        // Get quizzes where results are released
        $quizzes = $quizModel->where('results_released', true)->findAll();

        if (empty($quizzes)) {
            CLI::write('No quizzes found with released results.', 'yellow');
            return;
        }

        foreach ($quizzes as $quiz) {
            CLI::write('Processing Quiz: ' . $quiz['name'], 'cyan');
            
            // Get pending assignments
            $assignments = $assignmentModel->where('quiz_id', $quiz['id'])
                                           ->where('result_email_sent', false)
                                           ->where('status', 'COMPLETED')
                                           ->findAll();

            if (empty($assignments)) {
                CLI::write('  - No pending result emails for this quiz.', 'white');
                continue;
            }

            // NEW: Adding detailed result fetching
            $attemptModel = new \App\Models\AttemptModel();
            $responseModel = new \App\Models\ResponseModel();
            $questionModel = new \App\Models\QuestionModel();
            $optionModel = new \App\Models\OptionModel();

            foreach ($assignments as $assignment) {
                $user = $userModel->find($assignment['user_id']);
                if (!$user) continue;

                CLI::print('  - Sending to ' . $user['email'] . '... ');
                
                $attempt = $attemptModel->where('assignment_id', $assignment['id'])->first();
                $resultsData = [];
                
                if ($attempt) {
                    $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
                    foreach ($responses as $resp) {
                        $question = $questionModel->find($resp['question_id']);
                        $options = $optionModel->where('question_id', $resp['question_id'])->findAll();
                        $resultsData[] = [
                            'question' => $question,
                            'options' => $options,
                            'selected_option_id' => $resp['selected_option_id'],
                            'is_correct' => $resp['is_correct']
                        ];
                    }
                }

                $sent = $emailLib->send($user['email'], 'Quiz Results: ' . $quiz['name'], 'quiz_results', [
                    'first_name' => $user['first_name'],
                    'quiz_name' => $quiz['name'],
                    'score' => $assignment['score'],
                    'status' => $assignment['status'],
                    'results' => $resultsData
                ]);

                if ($sent) {
                    $assignmentModel->update($assignment['id'], ['result_email_sent' => true]);
                    CLI::write('Success', 'green');
                } else {
                    CLI::write('Failed', 'red');
                }
            }
        }

        CLI::write('Email processing complete.', 'green');
    }
}
