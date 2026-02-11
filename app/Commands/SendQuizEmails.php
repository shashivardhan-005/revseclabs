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

        // Get quizzes that have ended OR are manually released
        $now = date('Y-m-d H:i:s');
        $quizzes = $quizModel->groupStart()
                                ->where('end_time <=', $now)
                                ->orWhere('results_released', true)
                             ->groupEnd()
                             ->findAll();

        if (empty($quizzes)) {
            CLI::write('No quizzes found that need result processing.', 'yellow');
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

                // Check for certificate
                $certificateUrl = null;
                $passThreshold = (int)($quiz['pass_score'] ?: 70);
                if (round((float)$assignment['score'], 2) >= $passThreshold) {
                    $certificateUrl = base_url('quiz/certificate/' . $assignment['id']);
                }

                $sent = $emailLib->sendQuizResults($user, $quiz, $assignment, $resultsData, $certificateUrl);

                if ($sent) {
                    $updateData = ['result_email_sent' => true];
                    if ($certificateUrl) {
                        $updateData['certificate_sent'] = true;
                    }
                    $assignmentModel->update($assignment['id'], $updateData);
                    CLI::write('Success', 'green');
                } else {
                    CLI::write('Failed', 'red');
                }
            }
        }

        CLI::write('Email processing complete.', 'green');
    }
}
