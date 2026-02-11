<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white">User Dashboard</h2>
        <hr class="text-white opacity-25">
    </div>
</div>
<div class="row g-4">
    <!-- Left Column: User Info & Progress -->
    <div class="col-lg-4">
        <!-- Welcome Banner -->
        <div class="card bg-primary text-white border-0 shadow-lg mb-4 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative z-1 text-white">
                <h4 class="fw-bold mb-1">Welcome, <?= session()->get('first_name') ?>!</h4>
                <p class="opacity-75 small mb-0">Assess your awareness today.</p>
            </div>
            <i class="bi bi-rocket-takeoff position-absolute end-0 bottom-0 display-1 opacity-10 me-n3 mb-n3"></i>
        </div>



        <div class="card shadow-sm h-auto mb-4 border-0">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Your Progress</h5>
            </div>
            <div class="card-body pt-0">
                <?php if (empty($progress_stats)): ?>
                    <p class="text-muted text-center py-4">No activity yet. Start a quiz to see your progress!</p>
                <?php else: ?>
                    <?php foreach ($progress_stats as $stat): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?= esc($stat['label']) ?></span>
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?= esc($stat['sublabel']) ?></span>
                        </div>
                        <div class="progress rounded-pill" style="height: 18px; background-color: #f1f5f9;">
                            <div class="progress-bar <?= $stat['color_class'] ?> rounded-pill shadow-sm d-flex align-items-center justify-content-center fw-bold" 
                                 role="progressbar" style="width: <?= $stat['percent'] ?>%; font-size: 0.7rem; color: #fff;" 
                                 aria-valuenow="<?= $stat['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $stat['display_percent'] ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0"><i class="bi bi-person-gear me-2 text-primary"></i>Security Center</h5>
            </div>
            <div class="card-body pt-0">
                <div class="p-3 bg-light rounded-4 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary me-2">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h6 class="mb-0 text-truncate small fw-bold"><?= session()->get('email') ?></h6>
                    </div>
                    <div class="small text-muted ps-2 border-start border-2 border-primary border-opacity-25 ms-3">
                        Dept: <?= session()->get('department') ?: 'Standard' ?>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('profile') ?>" class="btn btn-outline-primary btn-sm rounded-3">
                        <i class="bi bi-pencil-square"></i> Profile
                    </a>
                    <a href="<?= base_url('password/change') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="bi bi-shield-lock"></i> Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Quizzes -->
    <div class="col-lg-8">
        <h4 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-journal-check me-3 text-primary"></i>
            Active Assessments
        </h4>
        
        <?php if (empty($assigned_quizzes)): ?>
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-clipboard-x display-1 text-light mb-4"></i>
                <h5>No Quizzes Assigned</h5>
                <p class="text-muted">You're all caught up! New quizzes will appear here when assigned.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($assigned_quizzes as $asm): 
                    $now = time();
                    $start = strtotime($asm['start_time']);
                    $end = strtotime($asm['end_time']);
                ?>
                <div class="col-md-6 quiz-card-container" data-quiz-id="<?= $asm['id'] ?>">
                    <div class="card h-100 border-0 shadow-sm" 
                         data-start-time="<?= $start ?>" 
                         data-end-time="<?= $end ?>"
                         data-start-url="<?= base_url('quiz/'.$asm['id'].'/start') ?>"
                         data-end-time-str="<?= date('M d, H:i', $end) ?>"
                         data-status="<?= $asm['status'] ?>">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0"><?= esc($asm['topic_display']) ?></span>
                                <?php if (($asm['retest_count'] ?? 0) >= 1): ?>
                                    <span class="badge bg-info text-dark border-0 ms-1">Retest Opportunity</span>
                                <?php endif; ?>
                                <span class="small text-muted"><i class="bi bi-clock"></i> <?= $asm['duration_minutes'] ?>m</span>
                            </div>
                            
                            <h5 class="fw-bold mb-3"><?= esc($asm['quiz_name']) ?></h5>
                            
                             <div class="timeline-container">
                                <div class="timeline-line">
                                    <div class="timeline-progress" style="width: <?= ($now >= $start && $now <= $end) ? (($now - $start) / ($end - $start)) * 100 : ($now > $end ? 100 : 0) ?>%"></div>
                                    <div class="timeline-point active" style="left: 0;"></div>
                                    <div class="timeline-point <?= $now >= $end ? 'active' : '' ?>" style="right: 0;"></div>
                                </div>
                                <div class="timeline-label timeline-label-start"><?= date('M d, H:i', $start) ?></div>
                                <div class="timeline-label timeline-label-end"><?= date('M d, H:i', $end) ?></div>
                                
                                <div class="status-badge-floating <?= $now < $start ? 'bg-info' : ($now > $end ? 'bg-secondary' : 'bg-success') ?> text-white fw-bold">
                                    <span id="status-text-<?= $asm['id'] ?>">
                                        <?php if ($now < $start): ?>
                                            Upcoming
                                        <?php elseif ($now > $end): ?>
                                            Closed
                                        <?php else: ?>
                                            Active
                                        <?php endif; ?>
                                    </span>
                                </div>
                             </div>

                             <div class="mb-4 timer-display-container">
                                 <!-- Timer will be injected here by JS -->
                                 <div class="d-flex align-items-center text-muted small fw-bold" style="min-height: 20px;"></div>
                             </div>

                            <div class="d-grid gap-2">
                                <?php if ($asm['status'] === 'COMPLETED'): ?>
                                    <div class="alert alert-success border-0 small py-2 mb-0 d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-check-circle-fill me-2"></i> Submitted</span>
                                        <?php if ($asm['retest_requested']): ?>
                                            <span class="badge bg-warning text-dark">Retest Requested</span>
                                        <?php elseif (! ($asm['retest_rejected'] ?? false) && ($asm['retest_count'] ?? 0) < 1): ?>
                                            <form action="<?= base_url('quiz/retest/' . $asm['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-link btn-sm text-danger p-0 fw-bold" style="font-size: 0.75rem; text-decoration: none;">
                                                    <i class="bi bi-arrow-repeat"></i> Request Retest
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($now < $start): ?>
                                    <button class="btn btn-light disabled rounded-3">Upcoming</button>
                                <?php elseif ($now > $end): ?>
                                    <button class="btn btn-light disabled rounded-3">Expired</button>
                                <?php else: ?>
                                    <a href="<?= base_url('quiz/'.$asm['id'].'/start') ?>" class="btn btn-primary shadow-sm rounded-3">
                                        <?= ($asm['status'] === 'STARTED') ? 'Continue' : 'Enter Assessment' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h4 class="fw-bold mt-5 mb-4 d-flex align-items-center">
            <i class="bi bi-award me-3 text-success"></i>
            Assessment Results
        </h4>
        
        <?php if (empty($completed_quizzes)): ?>
            <div class="p-4 bg-white rounded-4 border-dashed border text-center text-muted">
                Completed history will appear here once results are released.
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Quiz Name</th>
                                <th>Score</th>
                                <th class="text-end pe-4">Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_quizzes as $cq): 
                                $nowStr = date('Y-m-d H:i:s');
                                // Results are released if the admin says so OR if the quiz time has naturally expired
                                $canView = ($nowStr >= $cq['end_time'] || (bool)$cq['results_released']);
                                $passScore = $cq['pass_score'] ?: 70;
                                $failed = ($cq['score'] < $passScore);
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= esc($cq['quiz_name']) ?></td>
                                <td>
                                    <?php if ($canView): ?>
                                        <span class="badge <?= !$failed ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= !$failed ? 'text-success' : 'text-danger' ?>">
                                            <?= round((float)$cq['score'], 1) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic"><i class="bi bi-hourglass-top me-1"></i> Processing</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2 align-items-center">
                                        <?php if ($cq['retest_rejected'] ?? false): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" title="Retest request denied">
                                                Retest Rejected
                                            </span>
                                        <?php elseif (($cq['retest_count'] ?? 0) >= 1): ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" title="Retest attempt used">
                                                Retest Used
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($canView): ?>
                                            <?php if (!$failed): ?>
                                                <a href="<?= base_url('quiz/certificate/'.$cq['id']) ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill" title="Download Certificate">
                                                    <i class="bi bi-award"></i> Certificate
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('results/'.$cq['id']) ?>" class="btn btn-primary btn-sm px-3 rounded-pill">
                                                View Report
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentQuizzes = <?= json_encode($assigned_quizzes) ?>;
    
    // 1. Per-second Countdown for Upcoming Quizzes
    function updateTimers() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.card[data-start-time]').forEach(card => {
            const start = parseInt(card.dataset.startTime);
            const end = parseInt(card.dataset.endTime);
            const status = card.dataset.status;
            const timerDisplay = card.querySelector('.timer-display-container div');
            const buttonContainer = card.querySelector('.d-grid');
            const progressBar = card.querySelector('.timeline-progress');
            const statusBadge = card.querySelector('.status-badge-floating');
            const quizId = card.closest('.quiz-card-container').dataset.quizId;

            // Update Progress Bar
            if (progressBar) {
                let progress = 0;
                if (now >= start && now <= end) {
                    progress = ((now - start) / (end - start)) * 100;
                } else if (now > end) {
                    progress = 100;
                }
                progressBar.style.width = `${progress}%`;
            }

            if (now >= start && now <= end && status === 'ASSIGNED') {
                // ... Swap logic ...
                const startUrl = card.dataset.startUrl;
                if (buttonContainer && !buttonContainer.querySelector('a')) {
                    buttonContainer.innerHTML = `<a href="${startUrl}" class="btn btn-primary shadow-sm rounded-3">Enter Assessment</a>`;
                    card.dataset.status = 'STARTED_LOCALLY';
                    if (statusBadge) {
                        statusBadge.className = 'status-badge-floating bg-success text-white fw-bold';
                        const stText = document.getElementById(`status-text-${quizId}`);
                        if (stText) stText.innerText = 'Active';
                    }
                }
                
                if (timerDisplay) {
                    const diff = end - now;
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    timerDisplay.innerHTML = `<i class="bi bi-unlock-fill me-2"></i> Active: Ends in ${mins}:${secs.toString().padStart(2, '0')}`;
                    timerDisplay.className = "d-flex align-items-center text-success small fw-bold";
                }
            } else if (now >= start && now <= end && (status === 'STARTED' || status === 'STARTED_LOCALLY')) {
                const diff = end - now;
                const mins = Math.floor(diff / 60);
                const secs = diff % 60;
                if (timerDisplay) {
                    timerDisplay.innerHTML = `<i class="bi bi-hourglass-split me-2"></i> Ends in: ${mins}:${secs.toString().padStart(2, '0')}`;
                    timerDisplay.className = "d-flex align-items-center text-success small fw-bold";
                }
                if (statusBadge && !statusBadge.classList.contains('bg-success')) {
                    statusBadge.className = 'status-badge-floating bg-success text-white fw-bold';
                    const stText = document.getElementById(`status-text-${quizId}`);
                    if (stText) stText.innerText = 'Active';
                }
            } else if (now > end) {
                const container = card.closest('.quiz-card-container');
                if (container && container.style.opacity !== '0.5') {
                    container.style.opacity = '0.5';
                    container.style.pointerEvents = 'none';
                    if (timerDisplay) {
                        timerDisplay.innerHTML = `<i class="bi bi-x-circle me-2"></i> Expired`;
                        timerDisplay.className = "d-flex align-items-center text-danger small fw-bold";
                    }
                    if (buttonContainer) {
                        buttonContainer.innerHTML = `<button class="btn btn-secondary disabled rounded-3">Expired</button>`;
                    }
                    if (statusBadge) {
                        statusBadge.className = 'status-badge-floating bg-secondary text-white fw-bold';
                        const stText = document.getElementById(`status-text-${quizId}`);
                        if (stText) stText.innerText = 'Closed';
                    }
                }
            } else if (now < start) {
                const diff = start - now;
                if (diff < 3600) {
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    if (timerDisplay) {
                        timerDisplay.innerHTML = `<i class="bi bi-clock-history me-2"></i> Starts in: ${mins}:${secs.toString().padStart(2, '0')}`;
                        timerDisplay.className = "d-flex align-items-center text-warning small fw-bold";
                    }
                }
            }
        });
    }

    // 2. Poll for new assignments every 15 seconds
    function pollAssignments() {
        fetch('<?= base_url('quiz/get-updates') ?>')
            .then(res => res.json())
            .then(data => {
                // If the count of items changed, we need to reload (new assignment or one finished)
                if (data.length !== currentQuizzes.length) {
                    location.reload();
                    return;
                }
                
                // If IDs don't match exactly, reload (assignment swapped)
                const currentIds = currentQuizzes.map(q => q.id).sort().join(',');
                const newIds = data.map(q => q.id).sort().join(',');
                if (currentIds !== newIds) {
                    location.reload();
                    return;
                }

                // Update status in place without reload
                data.forEach((newItem) => {
                    const cardContainer = document.querySelector(`.quiz-card-container[data-quiz-id="${newItem.id}"]`);
                    if (cardContainer) {
                        const card = cardContainer.querySelector('.card');
                        // Update status attribute so the timer loop picks it up
                        if (card.dataset.status !== newItem.status) {
                            card.dataset.status = newItem.status;
                        }
                    }
                });
                
                currentQuizzes = data;
            })
            .catch(err => console.error('Poll failed:', err));
    }

    setInterval(updateTimers, 1000);
    setInterval(pollAssignments, 15000); // 15s polling for instant feel
    updateTimers();
});
</script>
<?= $this->endSection() ?>
