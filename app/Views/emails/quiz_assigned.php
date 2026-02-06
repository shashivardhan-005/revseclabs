<?= $this->extend('emails/layout') ?>

<?= $this->section('content') ?>
    <h2 style="color: #1e293b; margin-top: 0;">New Quiz Assigned</h2>
    <p style="font-size: 16px;">Hi <?= esc($first_name) ?>,</p>
    <p style="color: #64748b; margin-bottom: 30px;">A new cybersecurity awareness quiz has been assigned to you. To ensure compliance and verify your skills, please complete it before the deadline.</p>
    
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
        <div style="margin-bottom: 20px;">
            <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Quiz Name</span>
            <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                <?= esc($quiz['name']) ?>
            </div>
        </div>
        
        <div style="display: flex; justify-content: space-around; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <div>
                <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Duration</span>
                <div style="font-weight: 600; color: #334155;"><?= esc($quiz['duration_minutes']) ?> Min</div>
            </div>
            <div style="border-left: 1px solid #e2e8f0;"></div>
            <div>
                <span style="display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Deadline</span>
                <div style="font-weight: 600; color: #334155;"><?= date('M d, Y', strtotime($quiz['end_time'])) ?></div>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="<?= $base_url ?>/dashboard" style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);">Start Assessment</a>
    </div>
<?= $this->endSection() ?>
