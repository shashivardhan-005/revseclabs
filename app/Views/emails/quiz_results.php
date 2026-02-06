<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0;">Your Assessment Results</h2>
    <p style="font-size: 16px;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 25px;">Here is the performance summary for your recent assessment: <strong><?= esc($quiz_name) ?></strong>.</p>
    
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
        <div style="margin-bottom: 20px;">
            <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Final Score</span>
            <div style="font-size: 42px; font-weight: 800; line-height: 1; color: <?= $score >= 70 ? '#10b981' : '#ef4444' ?>;">
                <?= esc($score) ?>%
            </div>
        </div>
        
        <div style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Certification Status</span>
            <div style="font-weight: 700; font-size: 18px; color: #334155;"><?= $score >= 70 ? 'SUCCESSFULLY CERTIFIED' : 'NOT CERTIFIED' ?></div>
        </div>
    </div>

    <?php if (!empty($results)): ?>
    <h3 style="color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">Detailed Analysis</h3>
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px;">
        <tbody>
            <?php foreach ($results as $index => $res): ?>
            <tr>
                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; background-color: #ffffff;">
                    <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Q<?= $index + 1 ?>. <?= esc($res['question']['text']) ?></div>
                    <?php if (!empty($res['question']['explanation'])): ?>
                    <div style="font-size: 13px; color: #475569; background-color: #ffffff; border-left: 3px solid #3b82f6; padding: 8px 12px; margin-top: 8px;">
                        <em><?= esc($res['question']['explanation']) ?></em>
                    </div>
                    <?php endif; ?>
                </td>
                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right; vertical-align: top; width: 100px;">
                    <?php if ($res['is_correct']): ?>
                        <span style="display: inline-block; background-color: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;">Correct</span>
                    <?php else: ?>
                        <span style="display: inline-block; background-color: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;">Incorrect</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Log in to the portal for a comprehensive review.</p>
        <a href="<?= $base_url ?>/dashboard" style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);">Access Dashboard</a>
    </div>
<?= $this->endSection() ?>
