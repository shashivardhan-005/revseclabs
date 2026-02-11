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
            <div class="row g-4" id="active-quizzes-list">

                <?php foreach ($assigned_quizzes as $asm): 
                    // $now and $asm are passed to the view
                    echo view('quiz/partials/quiz_card', ['asm' => $asm, 'now' => time()]);
                endforeach; ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($incomplete_quizzes)): ?>
            <h4 class="fw-bold mt-5 mb-4 d-flex align-items-center">
                <i class="bi bi-exclamation-circle me-3 text-secondary"></i>
                Incomplete Assessments
            </h4>
            <div class="row g-4" id="incomplete-quizzes-list">
                <?php foreach ($incomplete_quizzes as $asm): 
                    echo view('quiz/partials/quiz_card', ['asm' => $asm, 'now' => time()]);
                endforeach; ?>
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
    let currentQuizzes = <?= json_encode(array_merge($assigned_quizzes, $incomplete_quizzes)) ?>;
    
    // 1. Per-second Countdown for Upcoming Quizzes
    // 1. Per-second Countdown for Upcoming Quizzes
    function updateTimers() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.card[data-start-time]').forEach(card => {
            const start = parseInt(card.dataset.startTime);
            const end = parseInt(card.dataset.endTime);
            const status = card.dataset.status;
            const timerDisplay = card.querySelector('.timer-display-container div');
            const buttonContainer = card.querySelector('.d-grid');
            
            // Base classes for the new pill badge design
            const baseClasses = "d-flex align-items-center justify-content-center py-2 px-3 rounded-pill bg-opacity-10 fw-bold";
            const fsStyle = "font-size: 0.9rem;";

            if (now >= start && now <= end && status === 'ASSIGNED') {
                const startUrl = card.dataset.startUrl;
                if (buttonContainer && !buttonContainer.querySelector('a')) {
                    buttonContainer.innerHTML = `<a href="${startUrl}" class="btn btn-primary fw-bold py-3 shadow-sm rounded-3 hover-scale d-flex align-items-center justify-content-center" style="background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%); border: none;"><span>Enter Assessment</span><i class="bi bi-arrow-right ms-2"></i></a>`;
                    card.dataset.status = 'STARTED_LOCALLY';
                }
                
                if (timerDisplay) {
                    const diff = end - now;
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    timerDisplay.innerHTML = `<i class="bi bi-unlock-fill me-2 fs-5"></i> <span class="fw-bold">Active: Ends in ${mins}:${secs.toString().padStart(2, '0')}</span>`;
                    timerDisplay.className = `${baseClasses} bg-success text-success`;
                    timerDisplay.style = fsStyle;
                }
            } else if (now >= start && now <= end && (status === 'STARTED' || status === 'STARTED_LOCALLY')) {
                const diff = end - now;
                const mins = Math.floor(diff / 60);
                const secs = diff % 60;
                if (timerDisplay) {
                    timerDisplay.innerHTML = `<i class="bi bi-hourglass-split me-2 fs-5"></i> <span class="fw-bold">Ends in: ${mins}:${secs.toString().padStart(2, '0')}</span>`;
                    timerDisplay.className = `${baseClasses} bg-success text-success`;
                    timerDisplay.style = fsStyle;
                }
            } else if (now > end) {
                const container = card.closest('.quiz-card-container');
                // Check if retest logic allows access despite expiry
                const hasRetest = (parseInt(card.dataset.retestCount || 0) >= 1);

                if (!hasRetest) {
                    if (container && container.style.opacity !== '0.6') { 
                        container.style.opacity = '0.6';
                        container.style.pointerEvents = 'none'; 
                    }
                    if (timerDisplay) {
                        timerDisplay.innerHTML = `<i class="bi bi-x-circle me-2 fs-5"></i> <span class="fw-bold">Expired</span>`;
                        timerDisplay.className = `${baseClasses} bg-secondary text-secondary`;
                        timerDisplay.style = fsStyle;
                    }
                    if (buttonContainer) {
                         // Only replace if it's not already showing "Expired" or "Closed"
                         if (!buttonContainer.innerHTML.includes('Locked') && !buttonContainer.innerHTML.includes('Closed')) {
                             buttonContainer.innerHTML = `<button class="btn btn-light disabled text-muted fw-bold py-3 rounded-3 border-0" style="background: #e9ecef;">Assessment Closed</button>`;
                         }
                    }
                } else {
                     // Has retest: Ensure it's active looking
                     if (timerDisplay) {
                        timerDisplay.innerHTML = `<i class="bi bi-exclamation-circle me-2 fs-5"></i> <span class="fw-bold">Expired (Retest Available)</span>`;
                        timerDisplay.className = `${baseClasses} bg-warning text-warning`;
                        timerDisplay.style = fsStyle;
                     }
                }

            } else if (now < start) {
                const diff = start - now;
                if (diff < 3600) {
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    if (timerDisplay) {
                        timerDisplay.innerHTML = `<i class="bi bi-clock-history me-2 fs-5"></i> <span class="fw-bold">Starts in: ${mins}:${secs.toString().padStart(2, '0')}</span>`;
                        timerDisplay.className = `${baseClasses} bg-warning text-warning`;
                        timerDisplay.style = fsStyle;
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
                // 3. Handle Additions (New Quizzes)
                const newIdsArr = data.map(q => q.id);
                const currentIdsArr = currentQuizzes.map(q => q.id);
                
                const addedIds = newIdsArr.filter(id => !currentIdsArr.includes(id));
                const removedIds = currentIdsArr.filter(id => !newIdsArr.includes(id));
                
                // Add new cards
                addedIds.forEach(id => {
                    fetch(`<?= base_url('quiz/get-card-html/') ?>/${id}`)
                        .then(r => r.text())
                        .then(html => {
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = html;
                            const newCard = wrapper.firstElementChild;
                            
                            // Determine where to put it
                            const itemData = data.find(q => q.id === id);
                            const category = itemData.category || 'active'; // Default to active if missing
                            
                            let container;
                            if (category === 'incomplete') {
                                container = document.getElementById('incomplete-quizzes-list');
                            } else {
                                container = document.getElementById('active-quizzes-list');
                            }

                            // If container is missing (e.g. empty state "No Quizzes" means active-list is missing), reload to fix structure
                            if (!container) {
                                location.reload();
                                return;
                            }
                            
                            container.appendChild(newCard);
                            
                            // If "No Quizzes" message exists, remove it
                            const noQuizMsg = document.querySelector('.col-lg-8 .card.p-5');
                            if (noQuizMsg) noQuizMsg.remove();
                        });
                });

                // Remove old cards
                removedIds.forEach(id => {
                    const card = document.querySelector(`.quiz-card-container[data-quiz-id="${id}"]`);
                    if (card) card.remove();
                });

                // Update existing cards (Status/Retest Changes)
                data.forEach((newItem) => {
                    if (addedIds.includes(newItem.id)) return; // Already handled

                    const cardContainer = document.querySelector(`.quiz-card-container[data-quiz-id="${newItem.id}"]`);
                    if (cardContainer) {
                        const card = cardContainer.querySelector('.card');
                        const parentListId = cardContainer.parentElement.id;
                        
                        // Check if it's in the correct section
                        const newItemCategory = newItem.category || 'active';
                        const expectedParentId = (newItemCategory === 'incomplete') ? 'incomplete-quizzes-list' : 'active-quizzes-list';
                        
                        if (parentListId !== expectedParentId) {
                            // Moved between Active <-> Incomplete
                            location.reload();
                            return;
                        }

                        const oldRetestCount = parseInt(card.dataset.retestCount || 0);
                        const oldRetestRejected = card.dataset.retestRejected === '1';
                        const newRetestCount = parseInt(newItem.retest_count || 0);
                        // Fix: explicitly check for truthy value that isn't "0" string
                        const newRetestRejected = (newItem.retest_rejected == 1 || newItem.retest_rejected === '1' || newItem.retest_rejected === true);

                        // Check for significant changes
                        // Ignore if local is STARTED_LOCALLY and remote is ASSIGNED
                        const statusChanged = card.dataset.status !== newItem.status && 
                                            !(card.dataset.status === 'STARTED_LOCALLY' && newItem.status === 'ASSIGNED');

                        let shouldUpdate = false;
                        
                        // Strict update rules for Incomplete section
                        if (parentListId === 'incomplete-quizzes-list') {
                            // Only update incomplete quizzes if Retest Status changes
                            if (oldRetestCount !== newRetestCount || oldRetestRejected !== newRetestRejected) {
                                shouldUpdate = true;
                                // If retest is granted, reload to move to Active list
                                if (newRetestCount > oldRetestCount) {
                                    location.reload();
                                    return;
                                }
                            }
                        } else {
                             // Active section standard rules
                             if (statusChanged || oldRetestCount !== newRetestCount || oldRetestRejected !== newRetestRejected) {
                                 shouldUpdate = true;
                             }
                        }

                        if (shouldUpdate) {
                                // Fetch new HTML and SWAP
                                fetch(`<?= base_url('quiz/get-card-html/') ?>/${newItem.id}`)
                                    .then(r => r.text())
                                    .then(html => {
                                        const wrapper = document.createElement('div');
                                        wrapper.innerHTML = html;
                                        cardContainer.replaceWith(wrapper.firstElementChild);
                                    });
                        }
                    }
                });
                
                currentQuizzes = data;
                
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
