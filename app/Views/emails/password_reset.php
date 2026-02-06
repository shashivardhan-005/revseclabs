<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0;">Password Reset Request</h2>
    <p style="font-size: 16px;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px;">We received a request to reset the password for your account. If you made this request, please click the button below to securely set a new password.</p>
    
    <div style="text-align: center; margin: 40px 0;">
        <a href="<?= $reset_url ?>" style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);">Reset Password</a>
    </div>

    <div style="background-color: #fefce8; border: 1px solid #fef08a; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <p style="margin: 0; color: #854d0e; font-size: 13px;"><strong>Security Note:</strong> This link is valid for 1 hour. If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
    </div>
<?= $this->endSection() ?>
