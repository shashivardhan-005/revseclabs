<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">Welcome to RevSecLabs!</h2>
    <p style="font-size: 16px; font-family: sans-serif;">Hello <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px; font-family: sans-serif;">Your account has been successfully provisioned. You can now access the platform to participate in ongoing cybersecurity awareness assessments.</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; margin-bottom: 30px;">
        <tr>
            <td style="padding: 25px;">
                <div style="margin-bottom: 20px;">
                    <span style="display: block; color: #6366f1; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Login Email</span>
                    <div style="font-weight: 600; color: #1e293b; font-size: 18px; font-family: sans-serif;"><?= esc($email) ?></div>
                </div>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border-radius: 8px; border: 1px dashed #6366f1;">
                    <tr>
                        <td style="padding: 15px;">
                            <span style="display: block; color: #6366f1; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Temporary Password</span>
                            <div style="font-family: monospace; font-weight: 700; color: #1e293b; font-size: 20px; letter-spacing: 2px;"><?= esc($password) ?></div>
                        </td>
                    </tr>
                </table>
                <p style="margin: 10px 0 0; color: #ef4444; font-size: 12px; font-family: sans-serif;"><strong>Important:</strong> Please change this password immediately after your first login.</p>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding-top: 10px;">
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 6px; background-color: #4f46e5;">
                            <a href="<?= $base_url ?>" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 32px; font-family: sans-serif; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px; border: 1px solid #4f46e5;">Login to Portal</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= $this->endSection() ?>
