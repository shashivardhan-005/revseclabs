<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0;">Welcome to RevSecLabs!</h2>
    <p style="font-size: 16px;">Hello <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px;">Your account has been successfully provisioned. You can now access the platform to participate in ongoing cybersecurity awareness assessments.</p>
    
    <div style="background-color: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <div style="margin-bottom: 20px;">
            <span style="display: block; color: #6366f1; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Login Email</span>
            <div style="font-weight: 600; color: #1e293b; font-size: 18px;"><?= esc($email) ?></div>
        </div>
        
        <div style="background-color: #ffffff; padding: 15px; border-radius: 8px; border: 1px dashed #6366f1;">
            <span style="display: block; color: #6366f1; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Temporary Password</span>
            <div style="font-family: monospace; font-weight: 700; color: #1e293b; font-size: 20px; letter-spacing: 2px;"><?= esc($password) ?></div>
        </div>
        <p style="margin: 10px 0 0; color: #ef4444; font-size: 12px;"><strong>Important:</strong> Please change this password immediately after your first login.</p>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="<?= $base_url ?>" style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);">Login to Portal</a>
    </div>
<?= $this->endSection() ?>
