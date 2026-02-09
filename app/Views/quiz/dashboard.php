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
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small"><?= $stat['label'] ?></span>
                            <span class="badge bg-light text-dark border-0 small"><?= $stat['sublabel'] ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar <?= $stat['color_class'] ?>" role="progressbar" style="width: <?= $stat['percent'] ?>%" 
                                 aria-valuenow="<?= $stat['percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                <?php foreach ($assigned_quizzes as $asm): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0"><?= esc($asm['topic_name'] ?: 'General') ?></span>
                                <span class="small text-muted"><i class="bi bi-clock"></i> <?= $asm['duration_minutes'] ?>m</span>
                            </div>
                            
                            <h5 class="fw-bold mb-3"><?= esc($asm['quiz_name']) ?></h5>
                            
                            <div class="mb-4">
                                <?php
                                $now = time();
                                $start = strtotime($asm['start_time']);
                                $end = strtotime($asm['end_time']);

                                if ($now < $start): ?>
                                    <div class="d-flex align-items-center text-info small fw-bold">
                                        <i class="bi bi-calendar-event me-2"></i> Starts: <?= date('M d, H:i', $start) ?>
                                    </div>
                                <?php elseif ($now > $end): ?>
                                    <div class="d-flex align-items-center text-muted small fw-bold">
                                        <i class="bi bi-lock-fill me-2"></i> Closed: <?= date('M d, H:i', $end) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center text-success small fw-bold">
                                        <i class="bi bi-unlock-fill me-2"></i> Ends: <?= date('M d, H:i', $end) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <?php if ($now < $start): ?>
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
                                $end = strtotime($cq['end_time']);
                                $now = time();
                                $canView = ($now > $end && $cq['results_released']);
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= esc($cq['quiz_name']) ?></td>
                                <td>
                                    <?php if ($canView): ?>
                                        <span class="badge <?= $cq['score'] >= 70 ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= $cq['score'] >= 70 ? 'text-success' : 'text-danger' ?>">
                                            <?= round((float)$cq['score'], 1) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic"><i class="bi bi-hourglass-top me-1"></i> Processing</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($canView): 
                                        $passScore = $cq['pass_score'] ?: 70;
                                    ?>
                                        <div class="d-flex justify-content-end gap-2">
                                            <?php if ($cq['score'] >= $passScore): ?>
                                                <a href="<?= base_url('quiz/certificate/'.$cq['id']) ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill" title="Download Certificate">
                                                    <i class="bi bi-award"></i> Certificate
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('results/'.$cq['id']) ?>" class="btn btn-primary btn-sm px-3 rounded-pill">
                                                View Report
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($cq['retest_requested']): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Retest Requested</span>
                                        <?php else: ?>
                                            <form action="<?= base_url('quiz/retest/' . $cq['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Request Retest">
                                                    <i class="bi bi-arrow-repeat"></i> Request Retest
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
