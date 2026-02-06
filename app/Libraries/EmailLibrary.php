<?php

namespace App\Libraries;

use Config\Email;

class EmailLibrary
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    /**
     * Send a templated email.
     */
    public function send($to, $subject, $template, $data = [])
    {
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        
        // Add common data
        $data['base_url'] = base_url();
        
        $message = view('emails/' . $template, $data);
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return true;
        } else {
            $debugger = $this->email->printDebugger(['headers']);
            log_message('error', 'Email failed to send to ' . $to . '. Debugger: ' . $debugger);
            return false;
        }
    }

    /**
     * Safely get a value from a user array or object.
     */
    protected function getUserValue($user, $key, $default = '')
    {
        if (is_array($user)) {
            return $user[$key] ?? $default;
        }
        if (is_object($user)) {
            return $user->$key ?? $default;
        }
        return $default;
    }

    public function sendWelcome($user, $password)
    {
        $email = $this->getUserValue($user, 'email');
        if (empty($email)) {
            log_message('error', 'EmailLibrary: Attempted to send welcome email to user with no email address.');
            return false;
        }

        return $this->send($email, 'Welcome to RevSecLabs Quiz Platform', 'welcome', [
            'first_name' => $this->getUserValue($user, 'first_name'),
            'email' => $email,
            'password' => $password
        ]);
    }

    public function sendQuizAssigned($user, $quiz)
    {
        $email = $this->getUserValue($user, 'email');
        if (empty($email)) return false;

        return $this->send($email, 'New Quiz Assigned: ' . $quiz['name'], 'quiz_assigned', [
            'first_name' => $this->getUserValue($user, 'first_name'),
            'quiz' => $quiz
        ]);
    }

    public function sendPasswordReset($user, $token)
    {
        $email = $this->getUserValue($user, 'email');
        if (empty($email)) return false;

        return $this->send($email, 'Password Reset Request', 'password_reset', [
            'first_name' => $this->getUserValue($user, 'first_name'),
            'reset_url' => base_url('reset-password/' . $token)
        ]);
    }

    public function sendRequestStatus($user, $request, $isApproved)
    {
        $email = $this->getUserValue($user, 'email');
        if (empty($email)) return false;

        $status = $isApproved ? 'Approved' : 'Rejected';
        return $this->send($email, 'Profile Change Request ' . $status, 'request_status', [
            'first_name' => $this->getUserValue($user, 'first_name'),
            'is_approved' => $isApproved,
            'comment' => $request['admin_comment'] ?? ''
        ]);
    }
}
