<?php

namespace App\Libraries;

use Config\Email;

class EmailLibrary
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        
        // Load dynamic config from database via setting helper
        $config = [
            'fromEmail'  => get_setting('email_sender_email', 'revseclabs@gmail.com'),
            'fromName'   => get_setting('email_sender_name', 'RevSecLabs Admin'),
            'SMTPHost'   => get_setting('email_smtp_host', 'smtp.gmail.com'),
            'SMTPUser'   => get_setting('email_smtp_user', 'revseclabs@gmail.com'),
            'SMTPPass'   => get_setting('email_smtp_pass', 'qjcjuhtnxvxuaucu'),
            'SMTPPort'   => (int)get_setting('email_smtp_port', 587),
            'SMTPCrypto' => get_setting('email_smtp_crypto', 'tls'),
            'protocol'   => get_setting('email_service_mode', 'smtp') === 'api' ? 'mail' : 'smtp',
        ];
        
        $this->email->initialize($config);
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

    public function sendCertificate($user, $quiz, $assignment)
    {
        $email = $this->getUserValue($user, 'email');
        if (empty($email)) return false;

        return $this->send($email, 'Congratulations! Your Certificate for ' . $quiz['name'], 'certificate_email', [
            'first_name' => $this->getUserValue($user, 'first_name'),
            'quiz_name' => $quiz['name'],
            'score' => round($assignment['score'], 1),
            'certificate_url' => base_url('quiz/certificate/' . $assignment['id'])
        ]);
    }
}
