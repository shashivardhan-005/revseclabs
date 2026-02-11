<?= $this->extend('layout/dashboard') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Welcome Banner -->
<div class="welcome-card mb-4">
    <div class="welcome-content-wrapper">
        <div class="welcome-text-side">
            <p class="welcome-greeting">Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?></p>
            <h3 class="welcome-name"><?= session()->get('first_name') ?> <?= session()->get('last_name') ?> 👋</h3>
            <p class="welcome-subtitle">Track your cybersecurity awareness progress and complete your assessments.</p>
        </div>
        <div class="welcome-image-side">
            <div class="welcome-icon-wrapper">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-accent" style="background: var(--dash-blue);"></div>
            <div class="kpi-body">
                <div class="kpi-icon" style="background: #DBEAFE; color: var(--dash-blue);">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-value"><?= $kpi['active_count'] ?></div>
                    <div class="kpi-label">Active Assessments</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-accent" style="background: var(--dash-success);"></div>
            <div class="kpi-body">
                <div class="kpi-icon" style="background: #DCFCE7; color: var(--dash-success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-value"><?= $kpi['completion_rate'] ?><span class="kpi-unit">%</span></div>
                    <div class="kpi-label">Completion Rate</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-accent" style="background: var(--dash-warning);"></div>
            <div class="kpi-body">
                <div class="kpi-icon" style="background: #FEF3C7; color: var(--dash-warning);">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-value"><?= $kpi['avg_score'] ?><span class="kpi-unit">%</span></div>
                    <div class="kpi-label">Average Score</div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Active Assessments -->
<div class="mb-4" id="assessments">
    <div class="section-header">
        <h6><i class="bi bi-lightning-charge-fill text-primary me-2"></i>Active Assessments</h6>
        <?php if (!empty($assigned_quizzes)): ?>
            <span class="section-badge" style="background: #DBEAFE; color: var(--dash-blue);"><?= count($assigned_quizzes) ?></span>
        <?php endif; ?>
    </div>

    <?php if (empty($assigned_quizzes)): ?>
        <div class="empty-state">
            <i class="bi bi-shield-lock"></i>
            <h6>No active assessments</h6>
            <p>Your admin will assign new assessments soon.</p>
        </div>
    <?php else: ?>
        <div class="assessment-list" id="active-quizzes-list">
            <?php foreach ($assigned_quizzes as $asm):
                echo view('quiz/partials/quiz_card', ['asm' => $asm, 'now' => time()]);
            endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Incomplete Assessments -->
<?php if (!empty($incomplete_quizzes)): ?>
<div class="mb-4">
    <div class="section-header">
        <h6><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Incomplete</h6>
        <span class="section-badge" style="background: #FEF3C7; color: #D97706;"><?= count($incomplete_quizzes) ?></span>
    </div>
    <div class="assessment-list" id="incomplete-quizzes-list">
        <?php foreach ($incomplete_quizzes as $asm):
            echo view('quiz/partials/quiz_card', ['asm' => $asm, 'now' => time()]);
        endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Progress & Results Split -->
<div class="row g-4">
    <!-- Left: Topic Proficiency -->
    <div class="col-lg-4">
        <div class="progress-card">
            <h6><i class="bi bi-bar-chart-fill text-primary me-2"></i>Topic Proficiency</h6>
            <?php if (empty($progress_stats)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-graph-up" style="font-size:2rem; color:var(--dash-text-light);"></i>
                    <p class="text-muted small mt-2 mb-0">Complete quizzes to unlock proficiency stats!</p>
                </div>
            <?php else: ?>
                <?php foreach ($progress_stats as $stat): ?>
                <div class="progress-item">
                    <div class="progress-label">
                        <span><?= esc($stat['label']) ?></span>
                        <span><?= $stat['display_percent'] ?></span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="fill <?= $stat['color_class'] ?>" style="width: <?= $stat['percent'] ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Assessment History Table -->
    <div class="col-lg-8" id="results">
        <div class="results-card">
            <div class="card-header-custom">
                <h6><i class="bi bi-award-fill text-success me-2"></i>Assessment Results</h6>
                <?php if (!empty($completed_quizzes)): ?>
                    <span class="badge bg-light text-muted" style="font-size:0.7rem;"><?= count($completed_quizzes) ?> Records</span>
                <?php endif; ?>
            </div>

            <?php if (empty($completed_quizzes)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open" style="font-size:2rem; color:var(--dash-text-light);"></i>
                    <h6 class="text-muted mt-2 mb-1" style="font-size:0.88rem;">No Records Found</h6>
                    <p class="text-muted small mb-0">Your assessment results will appear here once released.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Assessment</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_quizzes as $cq):
                                $nowStr = date('Y-m-d H:i:s');
                                $canView = ($nowStr >= $cq['end_time'] || (bool)$cq['results_released']);
                                $passScore = $cq['pass_score'] ?: 70;
                                $failed = ($cq['score'] < $passScore);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold" style="font-size:0.88rem;"><?= esc($cq['quiz_name']) ?>
                                        <?php if (($cq['retest_count'] ?? 0) > 0): ?>
                                            <span class="badge" style="background:#EFF6FF; color:#2563EB; font-size:0.6rem; padding:2px 6px; border-radius:4px;">Retake</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.72rem;"><?= esc($cq['topic_display']) ?></div>
                                </td>
                                <td>
                                    <span style="font-size:0.82rem; color:var(--dash-text-muted);">
                                        <?= date('M d, Y', strtotime($cq['completed_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($canView): ?>
                                        <span class="score-badge <?= $failed ? 'score-fail' : 'score-pass' ?>">
                                            <?= round($cq['score']) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem;">---</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($canView): ?>
                                        <a href="<?= base_url('results/' . $cq['id']) ?>" class="btn-action btn-action-primary" style="font-size:0.72rem; padding:6px 14px;">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="status-badge status-upcoming" style="font-size:0.68rem;">
                                            <i class="bi bi-lock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentQuizzes = <?= json_encode(array_merge($assigned_quizzes, $incomplete_quizzes)) ?>;

    // 1. Per-second Countdown Timers
    function updateTimers() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.assessment-item[data-start-time]').forEach(item => {
            const start = parseInt(item.dataset.startTime);
            const end = parseInt(item.dataset.endTime);
            const status = item.dataset.status;
            const timerContainer = item.querySelector('.timer-display-container');
            const actionContainer = item.querySelector('.action-button-container');

            if (now >= start && now <= end && status === 'ASSIGNED') {
                const startUrl = item.dataset.startUrl;
                if (actionContainer && !actionContainer.querySelector('a')) {
                    actionContainer.innerHTML = `<a href="${startUrl}" class="btn-action btn-action-primary">Start <i class="bi bi-chevron-right"></i></a>`;
                    item.dataset.status = 'STARTED_LOCALLY';
                }
                if (timerContainer) {
                    const diff = end - now;
                    const hrs = Math.floor(diff / 3600);
                    const mins = Math.floor((diff % 3600) / 60);
                    const secs = diff % 60;
                    const timeStr = hrs > 0 ? `${hrs}h ${mins}m` : `${mins}:${secs.toString().padStart(2, '0')}`;
                    timerContainer.innerHTML = `<span class="timer-pill" style="color:var(--dash-success);"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> <span class="fw-bold">${timeStr} remaining</span></span>`;
                }
            } else if (now >= start && now <= end && (status === 'STARTED' || status === 'STARTED_LOCALLY')) {
                if (timerContainer) {
                    const diff = end - now;
                    const hrs = Math.floor(diff / 3600);
                    const mins = Math.floor((diff % 3600) / 60);
                    const secs = diff % 60;
                    const timeStr = hrs > 0 ? `${hrs}h ${mins}m` : `${mins}:${secs.toString().padStart(2, '0')}`;
                    timerContainer.innerHTML = `<span class="timer-pill" style="color:var(--dash-success);"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> <span class="fw-bold">${timeStr} remaining</span></span>`;
                }
            } else if (now > end) {
                const hasRetest = (parseInt(item.dataset.retestCount || 0) >= 1);
                if (!hasRetest) {
                    item.style.opacity = '0.5';
                    item.style.pointerEvents = 'none';
                    if (timerContainer) {
                        timerContainer.innerHTML = `<span class="status-badge status-expired"><i class="bi bi-x-circle"></i> Expired</span>`;
                    }
                    if (actionContainer) {
                        actionContainer.innerHTML = `<span class="btn-action btn-action-disabled">Closed</span>`;
                    }
                } else {
                    if (timerContainer) {
                        timerContainer.innerHTML = `<span class="status-badge status-upcoming"><i class="bi bi-exclamation-circle"></i> Retest Available</span>`;
                    }
                }
            } else if (now < start) {
                const diff = start - now;
                if (diff < 3600) {
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    if (timerContainer) {
                        timerContainer.innerHTML = `<span class="timer-pill" style="color:var(--dash-warning);"><i class="bi bi-hourglass-split"></i> <span class="fw-bold">Starts in ${mins}:${secs.toString().padStart(2, '0')}</span></span>`;
                    }
                }
            }
        });
    }

    // 2. Poll for new assignments
    function pollAssignments() {
        fetch('<?= base_url('quiz/get-updates') ?>')
            .then(res => res.json())
            .then(data => {
                const newIdsArr = data.map(q => q.id);
                const currentIdsArr = currentQuizzes.map(q => q.id);
                const addedIds = newIdsArr.filter(id => !currentIdsArr.includes(id));
                const removedIds = currentIdsArr.filter(id => !newIdsArr.includes(id));

                // Add new items
                addedIds.forEach(id => {
                    fetch(`<?= base_url('quiz/get-card-html/') ?>/${id}`)
                        .then(r => r.text())
                        .then(html => {
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = html;
                            const newItem = wrapper.firstElementChild;
                            const itemData = data.find(q => q.id === id);
                            const category = itemData.category || 'active';
                            let container = (category === 'incomplete')
                                ? document.getElementById('incomplete-quizzes-list')
                                : document.getElementById('active-quizzes-list');
                            if (!container) { location.reload(); return; }
                            container.appendChild(newItem);
                        });
                });

                // Remove old items
                removedIds.forEach(id => {
                    const el = document.querySelector(`.quiz-card-container[data-quiz-id="${id}"]`);
                    if (el) el.remove();
                });

                // Update existing items
                data.forEach((newItem) => {
                    if (addedIds.includes(newItem.id)) return;
                    const itemEl = document.querySelector(`.quiz-card-container[data-quiz-id="${newItem.id}"]`);
                    if (itemEl) {
                        const parentListId = itemEl.parentElement.id;
                        const newCategory = newItem.category || 'active';
                        const expectedParentId = (newCategory === 'incomplete') ? 'incomplete-quizzes-list' : 'active-quizzes-list';

                        if (parentListId !== expectedParentId) { location.reload(); return; }

                        const oldRetestCount = parseInt(itemEl.dataset.retestCount || 0);
                        const oldRetestRejected = itemEl.dataset.retestRejected === '1';
                        const newRetestCount = parseInt(newItem.retest_count || 0);
                        const newRetestRejected = (newItem.retest_rejected == 1 || newItem.retest_rejected === '1' || newItem.retest_rejected === true);

                        const statusChanged = itemEl.dataset.status !== newItem.status &&
                            !(itemEl.dataset.status === 'STARTED_LOCALLY' && newItem.status === 'ASSIGNED');

                        let shouldUpdate = false;
                        if (parentListId === 'incomplete-quizzes-list') {
                            if (oldRetestCount !== newRetestCount || oldRetestRejected !== newRetestRejected) {
                                shouldUpdate = true;
                                if (newRetestCount > oldRetestCount) { location.reload(); return; }
                            }
                        } else {
                            if (statusChanged || oldRetestCount !== newRetestCount || oldRetestRejected !== newRetestRejected) {
                                shouldUpdate = true;
                            }
                        }

                        if (shouldUpdate) {
                            fetch(`<?= base_url('quiz/get-card-html/') ?>/${newItem.id}`)
                                .then(r => r.text())
                                .then(html => {
                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = html;
                                    itemEl.replaceWith(wrapper.firstElementChild);
                                });
                        }
                    }
                });

                currentQuizzes = data;
            })
            .catch(err => console.error('Poll failed:', err));
    }

    setInterval(updateTimers, 1000);
    setInterval(pollAssignments, 15000);
    updateTimers();
});
</script>
<?= $this->endSection() ?>
