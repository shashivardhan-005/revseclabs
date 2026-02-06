<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0;">Profile Change Update</h2>
    <p style="font-size: 16px;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px;">The status of your profile change request has been updated by an administrator.</p>
    
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
        <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">New Status</span>
        <div style="font-size: 24px; font-weight: 800; text-transform: uppercase; color: <?= $is_approved ? '#10b981' : '#ef4444' ?>;">
            <?= $is_approved ? 'Approved' : 'Rejected' ?>
        </div>
        
        <?php if (!empty($comment)): ?>
        <div style="background-color: #ffffff; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <span style="display: block; color: #64748b; font-size: 11px; text-transform: uppercase; margin-bottom: 5px;">Admin Comment</span>
            <div style="color: #334155; font-style: italic;">"<?= esc($comment) ?>"</div>
        </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center;">
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">You can review your account details in the dashboard.</p>
        <a href="<?= $base_url ?>/dashboard" style="display: inline-block; background-color: #ffffff; color: #3b82f6; border: 1px solid #3b82f6; padding: 10px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">Go to Dashboard</a>
    </div>
<?= $this->endSection() ?>
