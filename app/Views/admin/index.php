<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- KPI CARDS -->
<div class="kpi-row">
    <div class="kpi-card blue">
        <div class="kpi-icon">🛡</div>
        <div class="kpi-content">
            <div class="kpi-label">Active Quiz</div>
            <div class="kpi-value"><?= $stats['current_quiz']['name'] ?? 'No Active Quiz' ?></div>
        </div>
    </div>
    <div class="kpi-card teal">
        <div class="kpi-icon">👥</div>
        <div class="kpi-content">
            <div class="kpi-label">Users Assigned</div>
            <div class="kpi-value text-white"><?= $stats['assigned_users'] ?></div>
        </div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-icon">✅</div>
        <div class="kpi-content">
            <div class="kpi-label">Completed</div>
            <div class="kpi-value text-white"><?= $stats['completed_users'] ?> <small style="font-size: 0.6em;">/ <?= $stats['assigned_users'] ?></small></div>
        </div>
    </div>
    <div class="kpi-card red">
        <div class="kpi-icon">⏱</div>
        <div class="kpi-content">
            <div class="kpi-label">Time Left</div>
            <div class="kpi-value text-white"><?= $stats['time_remaining_d'] ?? 0 ?><small style="font-size: 0.5em;">d</small> <?= $stats['time_remaining_h'] ?? 0 ?><small style="font-size: 0.5em;">h</small></div>
        </div>
    </div>
</div>

<!-- ROW 2: Progress & Security -->
<div class="content-row">
    <div class="content-card">
        <h3 class="card-title">Quiz Completion</h3>
        <div class="progress-container mt-4">
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: <?= $stats['completion_percentage'] ?>%;"></div>
            </div>
            <div class="progress-label mt-2"><?= $stats['completion_percentage'] ?>% Completed</div>
        </div>
    </div>
    <div class="content-card">
        <h3 class="card-title">Security Events</h3>
        <div class="event-item mt-3">
            <div class="event-label">
                <span class="event-icon red">🛡</span>
                Full Screen Exits
            </div>
            <div class="event-value"><?= $stats['fullscreen_exits'] ?></div>
        </div>
        <div class="event-item">
            <div class="event-label">
                <span class="event-icon orange">🔁</span>
                Tab Switch Attempts
            </div>
            <div class="event-value"><?= $stats['tab_switches'] ?></div>
        </div>
        <div class="event-item border-0">
            <div class="event-label">
                <span class="event-icon blue">📤</span>
                Auto Submits
            </div>
            <div class="event-value"><?= $stats['auto_submits'] ?></div>
        </div>
    </div>
</div>

<!-- ROW 3: Details & Activity -->
<div class="content-row">
    <div class="content-card">
        <h3 class="card-title mb-3">Active Quiz Details</h3>
        <?php if ($stats['current_quiz']): ?>
            <div class="detail-item py-2">
                <i class="bi bi-shield-fill text-primary"></i>
                <span class="detail-value ms-2"><?= $stats['current_quiz']['name'] ?></span>
            </div>
            <div class="detail-item py-2">
                <i class="bi bi-calendar3 text-info"></i>
                <span class="detail-label ms-2">Timeline:</span>
                <span class="detail-value"><?= date('M d', strtotime($stats['current_quiz']['start_time'])) ?> - <?= date('M d', strtotime($stats['current_quiz']['end_time'])) ?></span>
            </div>
            <div class="detail-item py-2">
                <i class="bi bi-clock text-warning"></i>
                <span class="detail-label ms-2">Duration:</span>
                <span class="detail-value"><?= $stats['current_quiz']['duration_minutes'] ?> Minutes</span>
            </div>
            <div class="detail-item py-2">
                <i class="bi bi-question-circle text-success"></i>
                <span class="detail-label ms-2">Questions per User:</span>
                <span class="detail-value"><?= $stats['current_quiz']['total_questions'] ?></span>
            </div>
            <div class="detail-item py-2 border-0">
                <i class="bi bi-lock text-danger"></i>
                <span class="detail-label ms-2">Results Status:</span>
                <span class="detail-value"><?= (isset($stats['current_quiz']['results_released']) && $stats['current_quiz']['results_released']) ? 'Released' : 'Locked' ?></span>
            </div>
        <?php else: ?>
            <p class="text-muted">No active quiz found for the current date.</p>
        <?php endif; ?>
    </div>
    <div class="content-card">
        <h3 class="card-title mb-3">Recent Activity</h3>
        <div class="activity-list">
            <?php foreach ($stats['recent_activity'] as $log): ?>
                <div class="activity-item py-2 d-flex flex-column border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="activity-content d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <span class="activity-dot me-2" style="background: <?= 
                                ($log['action'] == 'QUIZ_SUBMIT') ? '#10b981' : 
                                (($log['action'] == 'CHEAT_VIOLATION') ? '#ef4444' : '#3b82f6') 
                            ?>; width: 8px; height: 8px; border-radius: 50%;"></span>
                            <span class="activity-text small fw-bold text-dark"><?= $log['first_name'] ?> <?= $log['last_name'] ?></span>
                        </div>
                        <span class="activity-time text-muted x-small"><?= date('H:i', strtotime($log['timestamp'])) ?></span>
                    </div>
                    <div class="activity-detail text-muted ps-3 mt-1" style="font-size: 0.75rem; line-height: 1.2;">
                        <?= $log['details'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($stats['recent_activity'])): ?>
                <p class="text-muted small">No recent activity.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ACTION BUTTONS -->
<div class="action-row mt-4 d-flex gap-3">
    <a href="<?= base_url('admin/users') ?>" class="action-btn teal btn px-4 py-2 text-white" style="background: #0d9488; border-radius: 8px; text-decoration: none;">
        <i class="bi bi-people-fill me-2"></i> Manage Users
    </a>
    <a href="<?= base_url('admin/analytics') ?>" class="action-btn blue btn px-4 py-2 text-white" style="background: #1e3a8a; border-radius: 8px; text-decoration: none;">
        <i class="bi bi-file-earmark-bar-graph me-2"></i> Analytics & Reports
    </a>
    <a href="<?= base_url('admin/assignments') ?>" class="action-btn primary btn px-4 py-2 text-white" style="background: #3b82f6; border-radius: 8px; text-decoration: none;">
        <i class="bi bi-clipboard-check-fill me-2"></i> Manage Assignments
    </a>
</div>
<?= $this->endSection() ?>
