<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use App\Models\AttemptModel;
use App\Models\QuestionModel;
use App\Models\OptionModel;
use App\Models\ResponseModel;
use App\Models\AuditModel;
use App\Models\UserModel;
use App\Models\ProfileChangeRequestModel;
use CodeIgniter\Controller;

class Quiz extends BaseController
{
    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('id');
        $assignmentModel = new AssignmentModel();
        
        $assignments = $assignmentModel->getAssignmentsByUser($userId);
        
        $assigned_quizzes = [];
        $completed_quizzes = [];
        $progress_stats = [];

        $now = time();
        foreach ($assignments as $asm) {
            $endTime = strtotime($asm['end_time']);
            
            if ($asm['status'] === 'COMPLETED' || $now > $endTime) {
                $completed_quizzes[] = $asm;
                $percent = ($asm['status'] === 'COMPLETED') ? 100 : 0;
            } else {
                $assigned_quizzes[] = $asm;
                $percent = ($asm['status'] === 'STARTED') ? 30 : 0;
            }

            $progress_stats[] = [
                'label' => $asm['topic_name'] ?: $asm['quiz_name'],
                'sublabel' => $asm['difficulty'],
                'percent' => $percent,
                'color_class' => ($percent == 100) ? 'bg-success' : (($percent > 0) ? 'bg-info' : 'bg-secondary')
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
        $quizModel = new \App\Models\QuizModel();
        
        $assignment = $assignmentModel->find($assignmentId);

        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        $now = time();
        $startTime = strtotime($quiz['start_time']);
        $endTime = strtotime($quiz['end_time']);

        if ($now < $startTime) {
            return redirect()->to('/dashboard')->with('error', 'This quiz has not started yet. It will be available on ' . date('M d, Y H:i', $startTime));
        }

        if ($now > $endTime) {
            return redirect()->to('/dashboard')->with('error', 'This quiz has expired and can no longer be started.');
        }

        return view('quiz/start', ['assignment' => $assignment, 'quiz' => $quiz]);
    }

    public function take($assignmentId)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to('/login');

        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $questionModel = new QuestionModel();
        $optionModel = new OptionModel();
        $responseModel = new ResponseModel();
        $auditModel = new AuditModel();
        $quizModel = new \App\Models\QuizModel();

        $assignment = $assignmentModel->find($assignmentId);
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        $quiz = $quizModel->find($assignment['quiz_id']);
        $now = time();
        $startTime = strtotime($quiz['start_time']);
        $endTime = strtotime($quiz['end_time']);

        if ($now < $startTime) {
            return redirect()->to('/dashboard')->with('error', 'This quiz has not started yet.');
        }

        if ($now > $endTime) {
            return redirect()->to('/dashboard')->with('error', 'This quiz has expired.');
        }

        // Check for existing attempt or create new
        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();
        
        if (! $attempt) {
            $attemptIid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // Use current server time for start_time
            $startTimeStr = date('Y-m-d H:i:s');

            $attemptModel->insert([
                'id' => $attemptIid,
                'assignment_id' => $assignmentId,
                'start_time' => $startTimeStr
            ]);
            $attempt = $attemptModel->find($attemptIid);

            // Randomize Questions (Reuse $quiz from line 102)
            $questions = $questionModel->where('topic_id', $quiz['topic_id'])->orderBy('id', 'RANDOM')->findAll($quiz['total_questions']);
            
            // Fallback if not enough questions in topic
            if (count($questions) < $quiz['total_questions']) {
                $extra = $questionModel->where('topic_id !=', $quiz['topic_id'])->orderBy('id', 'RANDOM')->findAll($quiz['total_questions'] - count($questions));
                $questions = array_merge($questions, $extra);
            }

            foreach ($questions as $q) {
                $responseModel->insert([
                    'attempt_id' => $attemptIid,
                    'question_id' => $q['id']
                ]);
            }

            $assignmentModel->update($assignmentId, ['status' => 'STARTED']);

            $auditModel->insert([
                'user_id' => session()->get('id'),
                'action' => 'QUIZ_START',
                'details' => "Started quiz '{$quiz['name']}' (Assignment #$assignmentId)"
            ]);

            // For a fresh attempt, remaining time is full duration
            $remaining = $quiz['duration_minutes'] * 60;

        } else {
            // Even if attempt exists, ensure status is STARTED if it was stuck
            if ($assignment['status'] === 'ASSIGNED') {
                $assignmentModel->update($assignmentId, ['status' => 'STARTED']);
            }

            // Calculate remaining for existing attempt
            $startTime = strtotime($attempt['start_time']);
            $duration = $quiz['duration_minutes'] * 60;
            $elapsed = time() - $startTime;
            $remaining = max(0, $duration - $elapsed);
        }

        // Load responses and questions
        $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
        $questions_data = [];
        foreach ($responses as $resp) {
            $q = $questionModel->find($resp['question_id']);
            $options = $optionModel->where('question_id', $q['id'])->findAll();
            shuffle($options);

            $questions_data[] = [
                'response_id' => $resp['id'],
                'question_id' => $q['id'],
                'text' => $q['text'],
                'type' => $q['question_type'],
                'image_url' => $q['image_base64'],
                'options' => $options
            ];
        }

        return view('quiz/take', [
            'assignment' => $assignment,
            'quiz' => $quiz,
            'questions' => $questions_data,
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

        $assignment = $assignmentModel->find($assignmentId);
        
        if (! $assignment || $assignment['user_id'] != session()->get('id')) {
            return redirect()->to('/dashboard');
        }

        // IMPORTANT: Prevent resubmission of already completed quizzes
        // This avoids resetting the score to 0 if the user refreshes or clicks back
        if ($assignment['status'] === 'COMPLETED') {
            return redirect()->to('/quiz/success');
        }

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->where('assignment_id', $assignmentId)->first();

        $responses = $responseModel->where('attempt_id', $attempt['id'])->findAll();
        $scoreCount = 0;
        $totalQuestions = count($responses);

        foreach ($responses as $resp) {
            $selectedOptionId = $this->request->getPost('response_' . $resp['id']);
            if ($selectedOptionId) {
                $option = $optionModel->find($selectedOptionId);
                $isCorrect = $option['is_correct'];
                if ($isCorrect) $scoreCount++;

                $responseModel->update($resp['id'], [
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect
                ]);
            }
        }

        $finalScore = ($totalQuestions > 0) ? ($scoreCount / $totalQuestions) * 100 : 0;

        $assignmentModel->update($assignmentId, [
            'status' => 'COMPLETED',
            'completed_at' => date('Y-m-d H:i:s'),
            'score' => $finalScore
        ]);

        $attemptModel->update($attempt['id'], [
            'end_time' => date('Y-m-d H:i:s'),
            'score' => $finalScore
        ]);

        // Certificate Logic
        $quizModel = new \App\Models\QuizModel();
        $quiz = $quizModel->find($assignment['quiz_id']);
        
        $passThreshold = (int)($quiz['pass_score'] ?: 70);
        
        // Use a small epsilon for float comparison safety, though round() is generally better for display scores
        if (round((float)$finalScore, 2) >= $passThreshold) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find(session()->get('id'));
            
            $emailLib = new \App\Libraries\EmailLibrary();
            // Refresh assignment data to ensure we have the saved score and flags
            $assignmentData = $assignmentModel->find($assignmentId);
            
            // Only send if not already sent for this assignment
            if (!$assignmentData['certificate_sent']) {
                if ($emailLib->sendCertificate($user, $quiz, $assignmentData)) {
                    $assignmentModel->update($assignmentId, ['certificate_sent' => true]);
                }
            }
        }

        $auditModel = new \App\Models\AuditModel();
        $auditModel->insert([
            'user_id' => session()->get('id'),
            'action' => 'QUIZ_SUBMIT',
            'details' => "Submitted quiz '{$quiz['name']}' (Assignment #$assignmentId) with score: " . round($finalScore, 2) . "%"
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
        foreach ($assignments as $asm) {
            $data[] = [
                'id' => $asm['id'],
                'quiz_name' => $asm['quiz_name'],
                'topic_name' => $asm['topic_name'] ?: 'General',
                'duration_minutes' => $asm['duration_minutes'],
                'start_time' => $asm['start_time'],
                'end_time' => $asm['end_time'],
                'status' => (time() > strtotime($asm['end_time']) && $asm['status'] !== 'COMPLETED') ? 'EXPIRED' : $asm['status'],
                'start_timestamp' => strtotime($asm['start_time']),
                'end_timestamp' => strtotime($asm['end_time'])
            ];
        }

        return $this->response->setJSON($data);
    }
}
