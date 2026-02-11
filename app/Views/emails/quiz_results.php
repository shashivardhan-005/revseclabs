<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0; font-family: sans-serif;">Your Assessment Results</h2>
    <p style="font-size: 16px; font-family: sans-serif;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 25px; font-family: sans-serif;">Here is the performance summary for your recent assessment: <strong><?= esc($quiz_name) ?></strong>.</p>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
        <tr>
            <td align="center" style="padding: 25px;">
                <div style="margin-bottom: 20px;">
                    <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Final Score</span>
                    <div style="font-size: 42px; font-family: sans-serif; font-weight: 800; line-height: 1; color: <?= $score >= 70 ? '#10b981' : '#ef4444' ?>;">
                        <?= esc($score) ?>%
                    </div>
                </div>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #e2e8f0;">
                    <tr>
                        <td align="center" style="padding-top: 20px;">
                            <span style="display: block; color: #64748b; font-size: 12px; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Certification Status</span>
                            <div style="font-weight: 700; font-size: 18px; font-family: sans-serif; color: #334155; margin-bottom: 15px;"><?= $score >= 70 ? 'SUCCESSFULLY CERTIFIED' : 'NOT CERTIFIED' ?></div>
                            
                            <?php if (!empty($certificate_url)): ?>
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                    <tr>
                                        <td align="center" style="border-radius: 8px; background-color: #10b981;">
                                            <a href="<?= esc($certificate_url) ?>" target="_blank" style="display: inline-block; padding: 12px 24px; font-family: sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: 600;">Download Certificate</a>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if (!empty($results)): ?>
    <h3 style="color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; font-family: sans-serif;">Detailed Analysis</h3>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
        <tbody>
            <?php foreach ($results as $index => $res): ?>
            <tr>
                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; background-color: #ffffff; font-family: sans-serif;">
                    <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Q<?= $index + 1 ?>. <?= esc($res['question']['text']) ?></div>
                    <?php if (!empty($res['question']['explanation'])): ?>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="border-left: 3px solid #3b82f6; padding: 8px 12px; font-size: 13px; color: #475569; font-style: italic;">
                                <?= esc($res['question']['explanation']) ?>
                            </td>
                        </tr>
                    </table>
                    <?php endif; ?>
                </td>
                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right; vertical-align: top; width: 100px; font-family: sans-serif;">
                    <?php if ($res['is_correct']): ?>
                        <span style="display: inline-block; background-color: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Correct</span>
                    <?php else: ?>
                        <span style="display: inline-block; background-color: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Incorrect</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px dashed #e2e8f0;">
        <tr>
            <td align="center" style="padding-top: 40px; padding-bottom: 20px;">
                <p style="color: #64748b; font-size: 14px; font-family: sans-serif; margin-bottom: 20px;">Log in to the portal for a comprehensive review.</p>
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                    <tr>
                        <td align="center" style="border-radius: 50px; background-color: #4f46e5;">
                            <a href="<?= $base_url ?>/dashboard" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 32px; font-family: sans-serif; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 50px; border: 1px solid #4f46e5;">Access Dashboard</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= $this->endSection() ?>
