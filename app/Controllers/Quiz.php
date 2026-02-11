<?php

namespace App\Controllers;

use App\Models\QuizModel;
use App\Models\UserModel;
use App\Models\AssignmentModel;
use App\Models\AttemptModel;
use App\Models\ResponseModel;
use App\Models\QuestionModel;
use App\Models\OptionModel;
use App\Models\AuditModel;
use App\Models\ProfileChangeRequestModel;
use App\Models\QuizTopicModel;

class Quiz extends BaseController
{
    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $responseModel = new ResponseModel();
        $questionModel = new QuestionModel();
        
        $userId = session()->get('id');
        $assignments = $assignmentModel->getAssignmentsByUser($userId);

        $now = date('Y-m-d H:i:s');
        $assigned_quizzes = [];
        $completed_quizzes = [];
        $progress_stats = [];

        // 1. Organize Quizzes into Categories (Assigned vs Released Results)
        foreach ($assignments as $asm) {
            $isReleased = ($now >= $asm['end_time'] || (bool)$asm['results_released']);
            
            if ($asm['status'] === 'COMPLETED' && $isReleased) {
                $completed_quizzes[] = $asm;
            } else if ($asm['status'] !== 'COMPLETED' || !$isReleased) {
                // Keep in assigned if it's not completed OR if it is completed but results are NOT released yet
                $assigned_quizzes[] = $asm;
            }
        }

        // 2. Aggregate Performance Tracking by Topic
        $topicPerformance = [];

        foreach ($assignments as $asm) {
            $attempt = $attemptModel->where('assignment_id', $asm['id'])->first();
            if (!$attempt) continue;

            $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
            foreach ($responses as $resp) {
                $q = $questionModel->find($resp['question_id']);
                if (!$q) continue;

                $topicId = $q['topic_id'];
                if (!isset($topicPerformance[$topicId])) {
                    $topicModel = new \App\Models\TopicModel();
                    $topic = $topicModel->find($topicId);
                    $topicPerformance[$topicId] = [
                        'label' => $topic['name'] ?? 'General',
                        'earned_weight' => 0,
                        'total_weight' => 0
                    ];
                }

                $weight = ($q['difficulty'] === 'HARD') ? 3 : (($q['difficulty'] === 'MEDIUM') ? 2 : 1);
                $topicPerformance[$topicId]['total_weight'] += $weight;

                if ($resp['is_correct']) {
                    $topicPerformance[$topicId]['earned_weight'] += $weight;
                }
            }
        }

        foreach ($topicPerformance as $perf) {
            $percent = ($perf['total_weight'] > 0) ? ($perf['earned_weight'] / $perf['total_weight']) * 100 : 0;
            $progress_stats[] = [
                'label' => $perf['label'],
                'sublabel' => 'Topic Proficiency',
                'percent' => round($percent),
                'display_percent' => round($percent) . '%',
                'color_class' => ($percent >= 70) ? 'bg-success' : (($percent >= 40) ? 'bg-warning' : 'bg-danger')
            ];
        }

        return view('quiz/dashboard', [
            'assigned_quizzes' => $assigned_quizzes,
            'completed_quizzes' => $completed_quizzes,
            'progress_stats' => $progress_stats
        ]);
    }

    public function start($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $quizModel = new QuizModel();

        $assignment = $assignmentModel->find($assignmentId);
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        if ($assignment['status'] === 'COMPLETED') {
            return redirect()->to('/quiz/success');
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        
        return view('quiz/start', ['assignment' => $assignment, 'quiz' => $quiz]);
    }

    public function take($assignmentId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $questionModel = new QuestionModel();
        $optionModel = new OptionModel();
        $responseModel = new ResponseModel();
        $auditModel = new AuditModel();
        $quizModel = new \App\Models\QuizModel();
        $quizTopicModel = new QuizTopicModel();

        $assignment = $assignmentModel->find($assignmentId);
        if (!$assignment || $assignment['user_id'] != session()->get('id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        if (!$quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $now = time();
        $startTime = strtotime($quiz['start_time']);
        $endTime = strtotime($quiz['end_time']);

        if ($now < $startTime) {
            return redirect()->to('/dashboard')->with('error', 'This assessment has not started yet.');
        }

        if ($now > $endTime && $assignment['status'] !== 'COMPLETED' && ($assignment['retest_count'] ?? 0) < 1) {
            return redirect()->to('/dashboard')->with('error', 'This assessment has expired.');
        }

        // Check for existing attempt or create new
        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();
        
        if (! $attempt) {
            $attemptId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $attemptModel->insert([
                'id' => $attemptId,
                'assignment_id' => $assignmentId,
                'start_time' => date('Y-m-d H:i:s')
            ]);
            $attempt = $attemptModel->find($attemptId);

            // Fetch selected topics for this quiz
            $quizTopics = $quizTopicModel->where('quiz_id', $quiz['id'])->findAll();
            $topicIds = array_column($quizTopics, 'topic_id');

            // Difficulty Logic: Split questions across selected difficulties
            $qCount = (int)$quiz['total_questions'];
            $selectedDiffs = explode(',', $quiz['difficulty'] ?: 'MEDIUM');
            $diffCount = count($selectedDiffs);
            
            $perLevel = floor($qCount / $diffCount);
            $remainder = $qCount % $diffCount;

            $selectedQuestions = [];
            foreach ($selectedDiffs as $index => $dif) {
                $needed = (int)($perLevel + ($index < $remainder ? 1 : 0));
                
                if ($needed > 0) {
                    $query = $questionModel->where('difficulty', $dif);
                    if (!empty($topicIds)) {
                        $query->whereIn('topic_id', $topicIds);
                    }
                    
                    $levelQuestions = $query->orderBy('id', 'RANDOM')->findAll($needed);
                    $selectedQuestions = array_merge($selectedQuestions, $levelQuestions);
                }
            }

            // Fallback: Fill from any allowed difficulty if not enough found
            if (count($selectedQuestions) < $qCount) {
                $existingIds = array_column($selectedQuestions, 'id');
                $needed = (int)($qCount - count($selectedQuestions));
                
                if ($needed > 0) {
                    $query = $questionModel->whereIn('difficulty', $selectedDiffs);
                    if (!empty($existingIds)) $query->whereNotIn('id', $existingIds);
                    if (!empty($topicIds)) $query->whereIn('topic_id', $topicIds);
                    
                    $extra = $query->orderBy('id', 'RANDOM')->findAll($needed);
                    $selectedQuestions = array_merge($selectedQuestions, $extra);
                }
            }

            shuffle($selectedQuestions);
            foreach ($selectedQuestions as $q) {
                $responseModel->insert([
                    'attempt_id' => $attemptId,
                    'question_id' => $q['id']
                ]);
            }

            $assignmentModel->update($assignmentId, ['status' => 'STARTED']);

            $auditModel->insert([
                'user_id' => session()->get('id'),
                'action' => 'QUIZ_START',
                'details' => "Started quiz '{$quiz['name']}' (Assignment #$assignmentId)"
            ]);
        }

        // Fetch questions and options for display
        $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
        $questionsData = [];
        foreach ($responses as $resp) {
            $q = $questionModel->find($resp['question_id']);
            $options = $optionModel->where('question_id', $resp['question_id'])->findAll();
            
            // Randomize options for fairness
            shuffle($options);

            $questionsData[] = [
                'response_id' => $resp['id'],
                'question_id' => $q['id'],
                'text' => $q['text'],
                'type' => $q['question_type'],
                'image_url' => $q['image_base64'], // Check if image exists
                'options' => $options
            ];
        }

        // Calculate Remaining Time
        $startedAt = strtotime($attempt['start_time']);
        $durationSeconds = (int)$quiz['duration_minutes'] * 60;
        $elapsed = time() - $startedAt;
        $remaining = max(0, $durationSeconds - $elapsed);

        return view('quiz/take', [
            'assignment' => $assignment,
            'quiz' => $quiz,
            'questions' => $questionsData,
            'remaining_seconds' => $remaining
        ]);
    }

    public function submit($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $responseModel = new ResponseModel();
        $optionModel = new OptionModel();
        $questionModel = new QuestionModel();

        $assignment = $assignmentModel->find($assignmentId);
        
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        if ($assignment['status'] === 'COMPLETED') {
            return redirect()->to('/quiz/success');
        }

        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();
        $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
        
        $totalWeight = 0;
        $earnedWeight = 0;
        $resultsData = [];

        foreach ($responses as $resp) {
            $q = $questionModel->find($resp['question_id']);
            $weight = ($q['difficulty'] === 'HARD') ? 3 : (($q['difficulty'] === 'MEDIUM') ? 2 : 1);
            $totalWeight += $weight;

            $selectedOptionId = $this->request->getPost('response_' . $resp['id']);
            $isCorrect = false;
            
            if ($selectedOptionId) {
                $option = $optionModel->find($selectedOptionId);
                $isCorrect = (bool)($option['is_correct'] ?? false);
                if ($isCorrect) $earnedWeight += $weight;

                $responseModel->update($resp['id'], [
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect
                ]);
            }

            $options = $optionModel->where('question_id', $resp['question_id'])->findAll();

            $resultsData[] = [
                'question' => $q,
                'options' => $options,
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect
            ];
        }

        $finalScore = ($totalWeight > 0) ? ($earnedWeight / $totalWeight) * 100 : 0;

        $assignmentModel->update($assignmentId, [
            'status' => 'COMPLETED',
            'completed_at' => date('Y-m-d H:i:s'),
            'score' => $finalScore
        ]);

        $attemptModel->update($attempt['id'], [
            'end_time' => date('Y-m-d H:i:s'),
            'score' => $finalScore
        ]);

        // Emails are now handled by the CLI cron job (SendQuizEmails) 
        // which sends combined results/certificates after the quiz period ends.

        $auditModel = new \App\Models\AuditModel();
        $auditModel->insert([
            'user_id' => session()->get('id'),
            'action' => 'QUIZ_SUBMIT',
            'details' => "Submitted quiz (Assignment #$assignmentId) with score: " . round($finalScore, 2) . "%"
        ]);

        session()->setFlashdata('success', 'Quiz submitted successfully.');
        return redirect()->to('/quiz/success');
    }

    public function success()
    {
        return view('quiz/success');
    }

    public function logViolation($assignmentId)
    {
        $json = $this->request->getJSON();
        $type = $json->type ?? 'UNKNOWN';

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();

        if ($attempt) {
            if ($type === 'TAB_SWITCH') {
                $attemptModel->update($attempt['id'], ['tab_switch_violations' => $attempt['tab_switch_violations'] + 1]);
            } else if ($type === 'FULLSCREEN_EXIT') {
                $attemptModel->update($attempt['id'], ['full_screen_violations' => $attempt['full_screen_violations'] + 1]);
            }

            $quizModel = new \App\Models\QuizModel();
            $assignmentModel = new AssignmentModel();
            $assignment = $assignmentModel->find($assignmentId);
            $quiz = $quizModel->find($assignment['quiz_id']);

            $auditModel = new AuditModel();
            $auditModel->insert([
                'user_id' => session()->get('id'),
                'action' => 'CHEAT_VIOLATION',
                'details' => "$type violation in quiz '{$quiz['name']}' (Assignment #$assignmentId)"
            ]);
        }

        return $this->response->setJSON(['status' => 'logged']);
    }

    public function profile()
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $userModel = new UserModel();
        $requestModel = new ProfileChangeRequestModel();
        
        $userId = session()->get('id');
        $user = $userModel->find($userId);
        
        $activeRequest = $requestModel->where('user_id', $userId)
                                     ->where('is_approved', false)
                                     ->where('is_rejected', false)
                                     ->first();

        return view('quiz/profile', [
            'user' => $user,
            'active_request' => $activeRequest
        ]);
    }

    public function saveProfileRequest()
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $requestModel = new ProfileChangeRequestModel();
        $userId = session()->get('id');

        // Check for existing pending request
        $existing = $requestModel->where('user_id', $userId)
                                ->where('is_approved', false)
                                ->where('is_rejected', false)
                                ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have a pending profile change request.');
        }

        $requestModel->insert([
            'user_id' => $userId,
            'new_full_name' => $this->request->getPost('new_full_name'),
            'new_department' => $this->request->getPost('new_department'),
            'is_approved' => false,
            'is_rejected' => false
        ]);

        return redirect()->back()->with('success', 'Your request has been submitted to the administrator for approval.');
    }

    public function results($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $quizModel = new \App\Models\QuizModel();
        $responseModel = new ResponseModel();
        $questionModel = new QuestionModel();
        $optionModel = new OptionModel();

        $assignment = $assignmentModel->find($assignmentId);
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        
        // Security check: only show if ended OR released
        $now = time();
        $endTime = strtotime($quiz['end_time']);
        if ($now <= $endTime && ! $quiz['results_released']) {
            return redirect()->to('/dashboard')->with('error', 'Results for this assessment are not yet available.');
        }

        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();
        if (! $attempt) {
            return redirect()->to('/dashboard')->with('error', 'No attempt found for this assessment.');
        }

        // Fetch detailed questions, options, and user responses
        $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
        
        $resultsData = [];
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

        return view('quiz/results', [
            'assignment' => $assignment,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'results' => $resultsData
        ]);
    }

    public function requestRetest($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        // Only allow if not already requested
        if ($assignment['retest_requested']) {
            return redirect()->back()->with('error', 'Retest already requested.');
        }

        // Check if retest was previously rejected
        if ($assignment['retest_rejected']) {
            return redirect()->back()->with('error', 'Your retest request was denied. No further attempts allowed.');
        }

        // Check if max retest attempts reached (Allow only 1 retest => total 2 attempts)
        if (($assignment['retest_count'] ?? 0) >= 1) {
            return redirect()->back()->with('error', 'You have already used your allowed retest attempt.');
        }

        $assignmentModel->update($assignmentId, ['retest_requested' => true]);

        return redirect()->back()->with('success', 'Retest request submitted successfully.');
    }

    public function certificate($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new \App\Models\AssignmentModel();
        $quizModel = new \App\Models\QuizModel();
        $userModel = new \App\Models\UserModel();

        $assignment = $assignmentModel->find($assignmentId);
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        if ($assignment['status'] !== 'COMPLETED') {
            return redirect()->to('/dashboard')->with('error', 'Certification is only available for completed assessments.');
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        $passThreshold = (int)($quiz['pass_score'] ?: 70);
        
        // Consistently use rounding for comparison to avoid floating point precision issues
        if (round((float)$assignment['score'], 2) < $passThreshold) {
            return redirect()->to('/dashboard')->with('error', 'You did not achieve the required passing score for this certificate.');
        }

        $user = $userModel->find(session()->get('id'));

        return view('quiz/certificate', [
            'assignment' => $assignment,
            'quiz' => $quiz,
            'user' => $user
        ]);
    }

    public function get_updates()
    {
        if (! session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $userId = session()->get('id');
        $assignmentModel = new \App\Models\AssignmentModel();
        $assignments = $assignmentModel->getAssignmentsByUser($userId);

        $data = [];
        $now = time();
        foreach ($assignments as $asm) {
            $endTime = strtotime($asm['end_time']);
            
            $isReleased = ($now >= $endTime || (bool)$asm['results_released']);
            
            // Logic must match dashboard(): Include if NOT (Taken AND Released)
            // i.e. Include if (Not Taken) OR (Taken but Not Released)
            if ($asm['status'] !== 'COMPLETED' || !$isReleased) {
                $data[] = [
                    'id' => $asm['id'],
                    'quiz_name' => $asm['quiz_name'],
                    'topic_name' => $asm['topic_display'] ?: 'General',
                    'duration_minutes' => $asm['duration_minutes'],
                    'start_time' => $asm['start_time'],
                    'end_time' => $asm['end_time'],
                    'status' => $asm['status'],
                    'retest_count' => $asm['retest_count'],
                    'retest_rejected' => $asm['retest_rejected'],
                    'start_timestamp' => strtotime($asm['start_time']),
                    'end_timestamp' => $endTime
                ];
            }
        }

        return $this->response->setJSON($data);
    }

    public function getCardHtml($id)
    {
        if (! session()->get('isLoggedIn')) return $this->response->setStatusCode(401);

        $assignmentModel = new \App\Models\AssignmentModel();
        $userId = session()->get('id');
        
        $asm = $assignmentModel->getAssignmentDetail($id, $userId);
        
        if (!$asm) {
            return $this->response->setStatusCode(404)->setBody('Assignment not found');
        }

        return view('quiz/partials/quiz_card', ['asm' => $asm, 'now' => time()]);
    }
}
