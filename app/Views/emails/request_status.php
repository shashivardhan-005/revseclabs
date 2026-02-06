<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">Profile Change Update</h2>
    <p style="font-size: 16px; font-family: sans-serif;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px; font-family: sans-serif;">The status of your profile change request has been updated by an administrator.</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
        <tr>
            <td align="center" style="padding: 25px;">
                <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">New Status</span>
                <div style="font-size: 24px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; color: <?= $is_approved ? '#10b981' : '#ef4444' ?>;">
                    <?= $is_approved ? 'Approved' : 'Rejected' ?>
                </div>
                
                <?php if (!empty($comment)): ?>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 20px; background-color: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px;">
                    <tr>
                        <td style="padding: 15px; text-align: center;">
                            <span style="display: block; color: #64748b; font-size: 11px; font-family: sans-serif; text-transform: uppercase; margin-bottom: 5px;">Admin Comment</span>
                            <div style="color: #334155; font-family: sans-serif; font-style: italic;">"<?= esc($comment) ?>"</div>
                        </td>
                    </tr>
                </table>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <p style="color: #64748b; font-size: 14px; font-family: sans-serif; margin-bottom: 20px;">You can review your account details in the dashboard.</p>
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 6px; border: 1px solid #3b82f6;">
                            <a href="<?= $base_url ?>/dashboard" target="_blank" style="display: inline-block; padding: 10px 24px; font-family: sans-serif; font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 600;">Go to Dashboard</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= $this->endSection() ?>
