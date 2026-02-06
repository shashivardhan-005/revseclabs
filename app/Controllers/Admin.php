<?php

namespace App\Controllers;

use App\Models\QuizModel;
use App\Models\AssignmentModel;
use App\Models\AttemptModel;
use App\Models\UserModel;
use App\Models\AuditModel;
use App\Models\QuestionModel;
use App\Models\TopicModel;
use App\Models\OptionModel;
use App\Models\ProfileChangeRequestModel;
use App\Models\ResponseModel;
use App\Libraries\EmailLibrary;
use CodeIgniter\Controller;

class Admin extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Basic Staff Check
        if (!session()->get('isLoggedIn') || !session()->get('is_staff')) {
            // In a real app, use a Filter. For this migration, we check in controller.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function index()
    {
        $quizModel = new QuizModel();
        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        $auditModel = new AuditModel();

        // Active Quiz Logic
        $now = date('Y-m-d H:i:s');
        $currentQuiz = $quizModel->where('start_time <=', $now)
                                 ->where('end_time >=', $now)
                                 ->first();

        $stats = [
            'current_quiz' => $currentQuiz,
            'assigned_users' => 0,
            'completed_users' => 0,
            'completion_percentage' => 0,
            'fullscreen_exits' => 0,
            'tab_switches' => 0,
            'auto_submits' => 0,
            'recent_activity' => []
        ];

        if ($currentQuiz) {
            $stats['assigned_users'] = $assignmentModel->where('quiz_id', $currentQuiz['id'])->countAllResults();
            $stats['completed_users'] = $assignmentModel->where('quiz_id', $currentQuiz['id'])
                                                       ->where('status', 'COMPLETED')
                                                       ->countAllResults();
            
            if ($stats['assigned_users'] > 0) {
                $stats['completion_percentage'] = round(($stats['completed_users'] / $stats['assigned_users']) * 100);
            }

            // Violations from Attempts
            $attempts = $attemptModel->select('SUM(full_screen_violations) as fs, SUM(tab_switch_violations) as ts, SUM(violation_auto_submitted) as auto')
                                    ->whereIn('assignment_id', function($db) use ($currentQuiz) {
                                        return $db->select('id')->from('quiz_assignments')->where('quiz_id', $currentQuiz['id']);
                                    })->get()->getRow();
            
            if ($attempts) {
                $stats['fullscreen_exits'] = $attempts->fs ?? 0;
                $stats['tab_switches'] = $attempts->ts ?? 0;
                $stats['auto_submits'] = $attempts->auto ?? 0;
            }

            // Time Remaining
            $endTime = strtotime($currentQuiz['end_time']);
            $diff = $endTime - time();
            $stats['time_remaining_d'] = floor($diff / (24 * 3600));
            $stats['time_remaining_h'] = floor(($diff % (24 * 3600)) / 3600);
        }

        // Recent Activity
        $stats['recent_activity'] = $auditModel->select('audit_logs.*, users.first_name, users.last_name')
                                             ->join('users', 'users.id = audit_logs.user_id', 'left')
                                             ->orderBy('timestamp', 'DESC')
                                             ->limit(5)
                                             ->findAll();

        return view('admin/index', ['stats' => $stats]);
    }

    public function analytics()
    {
        $assignmentModel = new AssignmentModel();
        $userModel = new UserModel();
        $auditModel = new AuditModel();

        // 1. Completion Status
        $statusCounts = $assignmentModel->select('status, COUNT(*) as count')
                                       ->groupBy('status')
                                       ->findAll();
        
        $statusData = ['ASSIGNED' => 0, 'STARTED' => 0, 'COMPLETED' => 0];
        foreach ($statusCounts as $row) {
            $statusData[$row['status']] = (int)$row['count'];
        }

        // 2. Department Performance
        $deptPerformance = $userModel->select('department, AVG(quiz_assignments.score) as avg_score')
                                    ->join('quiz_assignments', 'quiz_assignments.user_id = users.id')
                                    ->where('department !=', '')
                                    ->groupBy('department')
                                    ->orderBy('avg_score', 'DESC')
                                    ->limit(5)
                                    ->findAll();

        $deptLabels = [];
        $deptScores = [];
        foreach ($deptPerformance as $item) {
            $deptLabels[] = $item['department'];
            $deptScores[] = round($item['avg_score'] ?: 0, 1);
        }

        // 3. Violations Details
        $violations = $auditModel->where('action', 'CHEAT_VIOLATION')
                                ->select('details, COUNT(*) as count')
                                ->groupBy('details')
                                ->findAll();

        return view('admin/analytics', [
            'status_labels' => array_keys($statusData),
            'status_values' => array_values($statusData),
            'dept_labels' => $deptLabels,
            'dept_scores' => $deptScores,
            'violations' => $violations
        ]);
    }

    public function auditLogs()
    {
        $auditModel = new AuditModel();
        $logs = $auditModel->select('audit_logs.*, users.email')
                          ->join('users', 'users.id = audit_logs.user_id', 'left')
                          ->orderBy('timestamp', 'DESC')
                          ->paginate(20);

        return view('admin/audit_logs', [
            'logs' => $logs,
            'pager' => $auditModel->pager
        ]);
    }

    // --- User Management ---
    public function users()
    {
        $userModel = new UserModel();
        $quizModel = new QuizModel();
        
        $search = $this->request->getGet('search');
        $department = $this->request->getGet('department');
        
        $query = $userModel->orderBy('email', 'ASC');
        
        if (!empty($search)) {
            $query->groupStart()
                  ->like('email', $search)
                  ->orLike('first_name', $search)
                  ->orLike('last_name', $search)
                  ->groupEnd();
        }
        
        if (!empty($department)) {
            $query->where('department', $department);
        }
        
        $users = $query->paginate(20);
        
        // Get unique departments for the filter
        $departments = $userModel->select('department')->distinct()->where('department !=', '')->findAll();

        return view('admin/users/index', [
            'users' => $users,
            'pager' => $userModel->pager,
            'departments' => array_column($departments, 'department'),
            'departments' => array_column($departments, 'department'),
            'quizzes' => $quizModel->where('end_time >=', date('Y-m-d H:i:s'))->findAll(), // Only active/future quizzes
            'filters' => [
                'search' => $search,
                'department' => $department
            ]
        ]);
    }

    public function bulkAssignQuiz()
    {
        $userIds = $this->request->getPost('user_ids');
        $quizId = $this->request->getPost('quiz_id');
        
        if (empty($userIds) || empty($quizId)) {
            return redirect()->back()->with('error', 'Please select users and a quiz.');
        }
        
        $assignmentModel = new AssignmentModel();
        $quizModel = new QuizModel();
        $userModel = new UserModel();
        $emailLib = new EmailLibrary();
        
        $quiz = $quizModel->find($quizId);
        $created = 0;
        $skipped = 0;
        
        foreach ($userIds as $userId) {
            // Check if already assigned
            $exists = $assignmentModel->where('user_id', $userId)->where('quiz_id', $quizId)->first();
            if (!$exists) {
                $assignmentModel->insert([
                    'user_id' => $userId,
                    'quiz_id' => $quizId,
                    'status' => 'ASSIGNED',
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
                $created++;
                
                // Send Email Notification
                $user = $userModel->find($userId);
                if ($user) {
                    $emailLib->sendQuizAssigned($user, $quiz);
                }
            } else {
                $skipped++;
            }
        }
        
        $msg = "Assigned quiz to $created users.";
        if ($skipped > 0) {
            $msg .= " ($skipped users skipped as they already have this quiz assigned)";
        }
        
        return redirect()->to('/admin/users')->with('success', $msg);
    }

    public function bulkDeleteUsers()
    {
        $userIds = $this->request->getPost('user_ids');
        if (empty($userIds)) {
            return redirect()->back()->with('error', 'Please select users to delete.');
        }

        $userModel = new UserModel();
        $deleted = 0;
        $currentUserId = session()->get('user_id');

        foreach ($userIds as $id) {
            if ($id != $currentUserId) {
                $userModel->delete($id);
                $deleted++;
            }
        }

        return redirect()->to('/admin/users')->with('success', "$deleted users deleted successfully.");
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        $userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
    }

    public function createUser()
    {
        return view('admin/users/form', ['user' => null]);
    }

    public function editUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        return view('admin/users/form', ['user' => $user]);
    }

    public function saveUser()
    {
        $userModel = new UserModel();
        $emailLib = new EmailLibrary();
        $data = $this->request->getPost();
        
        $id = $data['id'] ?? null;
        unset($data['id']);

        if ($id) {
            // Update
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = $data['password'];
            }
            $userModel->update($id, $data);
            $message = 'User updated successfully.';
        } else {
            // Create
            $password = $data['password'] ?: bin2hex(random_bytes(4));
            $data['password'] = $password;
            $data['is_staff'] = isset($data['is_staff']);
            $data['is_active'] = true;
            
            $userId = $userModel->insert($data);
            $user = $userModel->find($userId);
            
            // Send Welcome Email
            $emailLib->sendWelcome($user, $password);
            $message = 'User created successfully and welcome email sent.';
        }

        return redirect()->to('/admin/users')->with('success', $message);
    }

    public function importUsers()
    {
        return view('admin/users/import');
    }

    public function processUserImport()
    {
        $file = $this->request->getFile('csv_file');
        if (!$file->isValid() || $file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Please upload a valid CSV file.');
        }

        $userModel = new UserModel();
        $emailLib = new EmailLibrary();
        $createdCount = 0;
        
        $handle = fopen($file->getTempName(), 'r');
        $headers = fgetcsv($handle); // Skip headers

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) < 1) continue;
            
            $email = trim($row[0]);
            if (empty($email) || $userModel->where('email', $email)->first()) continue;

            $firstName = $row[1] ?? '';
            $lastName = $row[2] ?? '';
            $dept = $row[3] ?? '';
            
            // Random password
            $password = bin2hex(random_bytes(6));
            
            $userId = $userModel->insert([
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'department' => $dept,
                'is_staff' => false,
                'is_active' => true,
                'is_password_changed' => false
            ]);
            
            $user = $userModel->find($userId);
            $emailLib->sendWelcome($user, $password);
            
            $createdCount++;
        }
        fclose($handle);

        return redirect()->to('/admin/users')->with('success', "$createdCount users imported and notified via email.");
    }

    // --- Quiz Management ---
    public function quizzes()
    {
        $quizModel = new QuizModel();
        $quizzes = $quizModel->orderBy('start_time', 'DESC')->findAll();
        return view('admin/quizzes/index', ['quizzes' => $quizzes]);
    }

    public function createQuiz()
    {
        $topicModel = new TopicModel();
        return view('admin/quizzes/form', [
            'quiz' => null,
            'topics' => $topicModel->findAll()
        ]);
    }

    public function editQuiz($id)
    {
        $quizModel = new QuizModel();
        $topicModel = new TopicModel();
        $quiz = $quizModel->find($id);
        if (!$quiz) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('admin/quizzes/form', [
            'quiz' => $quiz,
            'topics' => $topicModel->findAll()
        ]);
    }

    public function saveQuiz()
    {
        $quizModel = new QuizModel();
        $data = $this->request->getPost();
        
        // Basic checkboxes manually handled as CodeIgniter doesn't post unchecked boxes
        $data['force_full_screen'] = isset($data['force_full_screen']);
        $data['detect_tab_switch'] = isset($data['detect_tab_switch']);
        $data['disable_copy_paste'] = isset($data['disable_copy_paste']);
        $data['auto_submit_on_violation'] = isset($data['auto_submit_on_violation']);
        $data['results_released'] = isset($data['results_released']);

        if (isset($data['id']) && !empty($data['id'])) {
            $quizModel->update($data['id'], $data);
        } else {
            $quizModel->insert($data);
        }

        return redirect()->to('/admin/quizzes')->with('success', 'Quiz saved successfully.');
    }

    public function deleteQuiz($id)
    {
        $quizModel = new QuizModel();
        $quizModel->delete($id);
        return redirect()->to('/admin/quizzes')->with('success', 'Quiz deleted successfully.');
    }

    // --- Assignment & Retest ---
    public function assignments()
    {
        $assignmentModel = new AssignmentModel();
        
        $status = $this->request->getGet('status');
        $quizId = $this->request->getGet('quiz_id');
        
        $query = $assignmentModel->select('quiz_assignments.*, users.email, quizzes.name as quiz_name')
                                ->join('users', 'users.id = quiz_assignments.user_id')
                                ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
                                ->orderBy('assigned_at', 'DESC');
                                
        if (!empty($status)) {
            $query->where('quiz_assignments.status', $status);
        }
        
        if (!empty($quizId)) {
            $query->where('quiz_assignments.quiz_id', $quizId);
        }
        
        if ($this->request->getGet('retest')) {
            $query->where('retest_requested', true);
        }

        $assignments = $query->paginate(20);
        
        $quizModel = new QuizModel();

        return view('admin/assignments/index', [
            'assignments' => $assignments,
            'pager' => $assignmentModel->pager,
            'quizzes' => $quizModel->findAll(),
            'filters' => [
                'status' => $status,
                'quiz_id' => $quizId,
                'retest' => $this->request->getGet('retest')
            ]
        ]);
    }

    public function approveRetest($id)
    {
        $assignmentModel = new AssignmentModel();
        $attemptModel = new AttemptModel();
        
        $assignment = $assignmentModel->find($id);
        if ($assignment && $assignment['retest_requested']) {
            // Reset assignment
            $assignmentModel->update($id, [
                'status' => 'ASSIGNED',
                'score' => null,
                'completed_at' => null,
                'retest_requested' => false
            ]);
            
            // Delete attempts
            $attemptModel->where('assignment_id', $id)->delete();
        }

        return redirect()->to('/admin/assignments')->with('success', 'Retest approved and progress reset.');
    }

    public function rejectRetest($id)
    {
        $assignmentModel = new AssignmentModel();
        
        $assignment = $assignmentModel->find($id);
        if ($assignment && $assignment['retest_requested']) {
            $assignmentModel->update($id, [
                'retest_requested' => false
            ]);
        }

        return redirect()->to('/admin/assignments')->with('success', 'Retest request rejected.');
    }

    public function deleteAssignment($id)
    {
        $assignmentModel = new AssignmentModel();
        $assignmentModel->delete($id);
        return redirect()->back()->with('success', 'Assignment deleted successfully.');
    }

    public function bulkDeleteAssignments()
    {
        $ids = $this->request->getPost('assignment_ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Please select assignments to delete.');
        }

        $assignmentModel = new AssignmentModel();
        $deleted = 0;
        
        foreach ($ids as $id) {
            $assignmentModel->delete($id);
            $deleted++;
        }

        return redirect()->back()->with('success', "$deleted assignments deleted successfully.");
    }

    // --- Question Bank ---
    public function questions()
    {
        $questionModel = new QuestionModel();
        $questions = $questionModel->select('questions.*, topics.name as topic_name')
                                  ->join('topics', 'topics.id = questions.topic_id', 'left')
                                  ->orderBy('questions.created_at', 'DESC')
                                  ->paginate(20);

        return view('admin/questions/index', [
            'questions' => $questions,
            'pager' => $questionModel->pager
        ]);
    }

    public function createQuestion()
    {
        $topicModel = new TopicModel();
        return view('admin/questions/form', [
            'question' => null,
            'topics' => $topicModel->findAll(),
            'options' => []
        ]);
    }

    public function editQuestion($id)
    {
        $questionModel = new QuestionModel();
        $topicModel = new TopicModel();
        $optionModel = new OptionModel();
        
        $question = $questionModel->find($id);
        if (!$question) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('admin/questions/form', [
            'question' => $question,
            'topics' => $topicModel->findAll(),
            'options' => $optionModel->where('question_id', $id)->findAll()
        ]);
    }

    public function saveQuestion()
    {
        $questionModel = new QuestionModel();
        $optionModel = new OptionModel();
        $db = \Config\Database::connect();
        
        $data = $this->request->getPost();
        $optionsData = $data['options'] ?? [];
        $correctIndex = $data['correct_option'] ?? null;

        unset($data['options'], $data['correct_option']);

        // Handle Image Upload -> Base64
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $data['image_base64'] = 'data:' . $image->getMimeType() . ';base64,' . base64_encode(file_get_contents($image->getTempName()));
        }

        $db->transStart();
        if (isset($data['id']) && !empty($data['id'])) {
            $questionId = $data['id'];
            $questionModel->update($questionId, $data);
            $optionModel->where('question_id', $questionId)->delete();
        } else {
            $questionId = $questionModel->insert($data);
        }

        foreach ($optionsData as $index => $optionText) {
            if (empty($optionText)) continue;
            $optionModel->insert([
                'question_id' => $questionId,
                'text' => $optionText,
                'is_correct' => ($index == $correctIndex)
            ]);
        }
        $db->transComplete();

        return redirect()->to('/admin/questions')->with('success', 'Question saved successfully.');
    }

    public function deleteQuestion($id)
    {
        $questionModel = new QuestionModel();
        $questionModel->delete($id);
        return redirect()->to('/admin/questions')->with('success', 'Question deleted successfully.');
    }

    // --- Topic Management ---
    public function topics()
    {
        $topicModel = new TopicModel();
        return view('admin/topics/index', [
            'topics' => $topicModel->findAll()
        ]);
    }

    public function saveTopic()
    {
        $topicModel = new TopicModel();
        $data = $this->request->getPost();
        
        if (isset($data['id']) && !empty($data['id'])) {
            $topicModel->update($data['id'], $data);
        } else {
            $topicModel->insert($data);
        }

        return redirect()->to('/admin/topics')->with('success', 'Topic saved successfully.');
    }

    public function deleteTopic($id)
    {
        $topicModel = new TopicModel();
        $topicModel->delete($id);
        return redirect()->to('/admin/topics')->with('success', 'Topic deleted successfully.');
    }

    // --- Export ---
    public function exportResults($quizId)
    {
        $assignmentModel = new AssignmentModel();
        $quizModel = new QuizModel();
        
        $quiz = $quizModel->find($quizId);
        if (!$quiz) return redirect()->back()->with('error', 'Quiz not found.');

        $results = $assignmentModel->select('users.email, quiz_assignments.status, quiz_assignments.score, quiz_assignments.completed_at')
                                   ->join('users', 'users.id = quiz_assignments.user_id')
                                   ->where('quiz_id', $quizId)
                                   ->findAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="quiz_results_'.$quizId.'.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['User Email', 'Status', 'Score', 'Date Completed']);
        
        foreach ($results as $row) {
            fputcsv($output, [
                $row['email'],
                $row['status'],
                $row['score'] !== null ? $row['score'] . '%' : 'N/A',
                $row['completed_at'] ?: 'N/A'
            ]);
        }
        fclose($output);
        exit;
    }

    // --- Profile Change Requests ---
    public function profileRequests()
    {
        $requestModel = new ProfileChangeRequestModel();
        $requests = $requestModel->select('profile_change_requests.*, users.first_name, users.last_name, users.department as current_dept')
                                ->join('users', 'users.id = profile_change_requests.user_id')
                                ->where('is_approved', false)
                                ->where('is_rejected', false)
                                ->findAll();

        return view('admin/requests/profile', ['requests' => $requests]);
    }

    public function approveProfileRequest($id)
    {
        $requestModel = new ProfileChangeRequestModel();
        $userModel = new UserModel();
        
        $request = $requestModel->find($id);
        if ($request) {
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Update User
            $userData = [];
            if ($request['new_full_name']) {
                $parts = explode(' ', trim($request['new_full_name']), 2);
                $userData['first_name'] = $parts[0];
                $userData['last_name'] = $parts[1] ?? '';
            }
            if ($request['new_department']) {
                $userData['department'] = $request['new_department'];
            }
            
            if (!empty($userData)) {
                $userModel->update($request['user_id'], $userData);
            }
            
            // Mark Request Approved
            $requestModel->update($id, ['is_approved' => true]);
            
            $db->transComplete();

            // Send Email Notification
            $user = $userModel->find($request['user_id']);
            $emailLib = new EmailLibrary();
            $emailLib->sendRequestStatus($user, $request, true);
        }

        return redirect()->to('/admin/profile-requests')->with('success', 'Profile change approved.');
    }

    public function rejectProfileRequest($id)
    {
        $requestModel = new ProfileChangeRequestModel();
        $userModel = new UserModel();
        $emailLib = new EmailLibrary();

        $request = $requestModel->find($id);
        if ($request) {
            $requestModel->update($id, ['is_rejected' => true]);
            $user = $userModel->find($request['user_id']);
            $emailLib->sendRequestStatus($user, $request, false);
        }
        
        return redirect()->to('/admin/profile-requests')->with('success', 'Profile change rejected.');
    }

    public function toggleResultsRelease($id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($id);
        
        if ($quiz) {
            $newState = ! $quiz['results_released'];
            $quizModel->update($id, ['results_released' => $newState]);
            
            if ($newState) {
                // If we just released them, trigger the email sending for completed assignments
                $this->sendQuizResults($id);
                $message = 'Results released and emails have been dispatched to all completed attempts.';
            } else {
                $message = 'Results locked. No further emails will be sent automatically.';
            }
            
            return redirect()->to('/admin/quizzes')->with('success', $message);
        }
        
        return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
    }

    protected function sendQuizResults($quizId)
    {
        $assignmentModel = new AssignmentModel();
        $userModel = new UserModel();
        $emailLib = new EmailLibrary();
        $quizModel = new QuizModel();
        $attemptModel = new AttemptModel();
        $responseModel = new ResponseModel();
        $questionModel = new QuestionModel();
        $optionModel = new OptionModel();
        
        $quiz = $quizModel->find($quizId);
        $assignments = $assignmentModel->where('quiz_id', $quizId)
                                       ->where('status', 'COMPLETED')
                                       ->where('result_email_sent', false)
                                       ->findAll();
                                       
        foreach ($assignments as $asm) {
            $user = $userModel->find($asm['user_id']);
            if ($user && !empty($user['email'])) {
                $attempt = $attemptModel->where('assignment_id', $asm['id'])->first();
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
                    'score' => $asm['score'],
                    'status' => $asm['status'],
                    'results' => $resultsData
                ]);
                
                if ($sent) {
                    $assignmentModel->update($asm['id'], ['result_email_sent' => true]);
                }
            }
        }
    }
}
