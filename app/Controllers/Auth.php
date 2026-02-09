<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\EmailLibrary;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return session()->get('is_staff') ? redirect()->to('/admin') : redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        
        $user = $model->where('email', $email)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $ses_data = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'is_staff' => $user['is_staff'],
                    'isLoggedIn' => true,
                ];
                $session->set($ses_data);

                $auditModel = new \App\Models\AuditModel();
                $auditModel->insert([
                    'user_id' => $user['id'],
                    'action' => 'LOGIN',
                    'details' => "User logged in successfully"
                ]);

                if (! $user['is_password_changed']) {
                    $session->setFlashdata('warning', 'You must change your password before proceeding.');
                    return redirect()->to('/password/change');
                }

                if ($user['is_staff']) {
                    return redirect()->to('/admin');
                }

                return redirect()->to('/dashboard');
            } else {
                $auditModel = new \App\Models\AuditModel();
                $auditModel->insert([
                    'user_id' => $user['id'],
                    'action' => 'LOGIN_FAILED',
                    'details' => "Failed login attempt: Invalid password"
                ]);
                $session->setFlashdata('error', 'Invalid password.');
                return redirect()->to('/login');
            }
        } else {
            $auditModel = new \App\Models\AuditModel();
            $auditModel->insert([
                'user_id' => null,
                'action' => 'LOGIN_FAILED',
                'details' => "Failed login attempt for non-existent email: $email"
            ]);
            $session->setFlashdata('error', 'Email not found.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $userId = session()->get('id');
        if ($userId) {
            $auditModel = new \App\Models\AuditModel();
            $auditModel->insert([
                'user_id' => $userId,
                'action' => 'LOGOUT',
                'details' => "User logged out"
            ]);
        }
        session()->destroy();
        return redirect()->to('/login');
    }

    public function changePassword()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return view('auth/password_change');
    }

    public function updatePassword()
    {
        $session = session();
        $model = new UserModel();
        $id = $session->get('id');
        $newPassword = $this->request->getVar('new_password');
        $confirmPassword = $this->request->getVar('confirm_password');

        if ($newPassword !== $confirmPassword) {
            $session->setFlashdata('error', 'Passwords do not match.');
            return redirect()->to('/password/change');
        }

        $model->update($id, [
            'password' => $newPassword,
            'is_password_changed' => true
        ]);

        $auditModel = new \App\Models\AuditModel();
        $auditModel->insert([
            'user_id' => $id,
            'action' => 'PASSWORD_CHANGE',
            'details' => "User changed their password"
        ]);

        $session->setFlashdata('success', 'Password changed successfully.');
        return redirect()->to('/dashboard');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        $model = new UserModel();
        $user = $model->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $model->update($user['id'], [
                'reset_token' => $token,
                'reset_expires' => $expires
            ]);

            $emailLib = new EmailLibrary();
            $emailLib->sendPasswordReset($user, $token);
        }

        // Always show success to prevent email enumeration
        session()->setFlashdata('success', 'If your email is in our system, you will receive a reset link shortly.');
        return redirect()->to('/forgot-password');
    }

    public function resetPassword($token)
    {
        $model = new UserModel();
        $user = $model->where('reset_token', $token)
                      ->where('reset_expires >=', date('Y-m-d H:i:s'))
                      ->first();

        if (!$user) {
            session()->setFlashdata('error', 'Invalid or expired reset link.');
            return redirect()->to('/forgot-password');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function updatePasswordWithToken()
    {
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            session()->setFlashdata('error', 'Passwords do not match.');
            return redirect()->back();
        }

        $model = new UserModel();
        $user = $model->where('reset_token', $token)
                      ->where('reset_expires >=', date('Y-m-d H:i:s'))
                      ->first();

        if (!$user) {
            session()->setFlashdata('error', 'Invalid or expired session.');
            return redirect()->to('/forgot-password');
        }

        $model->update($user['id'], [
            'password' => $newPassword,
            'reset_token' => null,
            'reset_expires' => null,
            'is_password_changed' => true
        ]);

        $auditModel = new \App\Models\AuditModel();
        $auditModel->insert([
            'user_id' => $user['id'],
            'action' => 'PASSWORD_RESET',
            'details' => "User reset their password via token"
        ]);

        session()->setFlashdata('success', 'Password reset successfully. Please log in.');
        return redirect()->to('/login');
    }
}
