<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">Password Reset Request</h2>
    <p style="font-size: 16px; font-family: sans-serif;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px; font-family: sans-serif;">We received a request to reset the password for your account. If you made this request, please click the button below to securely set a new password.</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 6px; background-color: #4f46e5;">
                            <a href="<?= $reset_url ?>" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 32px; font-family: sans-serif; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px; border: 1px solid #4f46e5;">Reset Password</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fefce8; border: 1px solid #fef08a; border-radius: 6px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 15px;">
                <p style="margin: 0; color: #854d0e; font-size: 13px; font-family: sans-serif;"><strong>Security Note:</strong> This link is valid for 1 hour. If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
            </td>
        </tr>
    </table>
<?= $this->endSection() ?>
