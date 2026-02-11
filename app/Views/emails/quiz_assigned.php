<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">New Quiz Assigned</h2>
    <p style="font-size: 16px; font-family: sans-serif;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px; font-family: sans-serif;">A new cybersecurity awareness quiz has been assigned to you. To ensure compliance and verify your skills, please complete it before the deadline.</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
        <tr>
            <td align="center" style="padding: 25px;">
                <div style="margin-bottom: 20px;">
                    <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Quiz Name</span>
                    <div style="font-size: 20px; font-weight: 700; color: #1e293b; font-family: sans-serif;">
                        <?= esc($quiz['name']) ?>
                    </div>
                </div>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #e2e8f0;">
                    <tr>
                        <td align="center" width="49%" style="padding-top: 20px;">
                            <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Duration</span>
                            <div style="font-weight: 600; color: #334155; font-family: sans-serif;"><?= esc($quiz['duration_minutes']) ?> Min</div>
                        </td>
                        <td width="2%" style="padding-top: 20px; border-left: 1px solid #e2e8f0;">&nbsp;</td>
                        <td align="center" width="49%" style="padding-top: 20px;">
                            <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Deadline</span>
                            <div style="font-weight: 600; color: #334155; font-family: sans-serif;"><?= date('M d, Y, h:i A', strtotime($quiz['end_time'])) ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding-top: 10px;">
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 50px; background-color: #4f46e5;">
                            <a href="<?= $base_url ?>/dashboard" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 32px; font-family: sans-serif; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 50px; border: 1px solid #4f46e5;">Start Assessment</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= $this->endSection() ?>
