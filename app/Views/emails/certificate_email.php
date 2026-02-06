<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">Congratulations, <?= esc($first_name) ?>!</h2>
    <p style="font-size: 16px; font-family: sans-serif; color: #334155;">Excellent work! You have successfully passed the assessment:</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0fdf4; border: 1px solid #bcf0da; border-radius: 12px; margin-bottom: 30px;">
        <tr>
            <td align="center" style="padding: 25px;">
                <div style="font-size: 14px; font-family: sans-serif; color: #166534; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">Assessment Completed</div>
                <div style="font-size: 24px; font-family: sans-serif; font-weight: 800; color: #15803d; margin-bottom: 10px;"><?= esc($quiz_name) ?></div>
                <div style="font-size: 18px; font-family: sans-serif; color: #16a34a;">Score: <strong><?= $score ?>%</strong></div>
            </td>
        </tr>
    </table>

    <p style="color: #64748b; font-size: 16px; font-family: sans-serif; margin-bottom: 30px;">Your achievement has been recorded. You can now view and download your official certificate of completion.</p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 8px; background-color: #10b981;">
                            <a href="<?= $certificate_url ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-family: sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: 600;">View My Certificate</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="color: #94a3b8; font-size: 13px; font-family: sans-serif; margin-top: 40px; text-align: center;">If the button above doesn't work, copy and paste this link into your browser:<br>
    <a href="<?= $certificate_url ?>" style="color: #3b82f6;"><?= $certificate_url ?></a></p>
<?= $this->endSection() ?>
