<?php
// Expects: $asm (array), $now (timestamp, optional)
$now = $now ?? time();
$start = strtotime($asm['start_time']);
$end = strtotime($asm['end_time']);
?>
<div class="col-md-6 quiz-card-container" data-quiz-id="<?= $asm['id'] ?>">
    <div class="card h-100 border-0 shadow-sm" 
            data-start-time="<?= $start ?>" 
            data-end-time="<?= $end ?>"
            data-start-url="<?= base_url('quiz/'.$asm['id'].'/start') ?>"
            data-end-time-str="<?= date('M d, H:i', $end) ?>"
            data-status="<?= $asm['status'] ?>"
            data-retest-count="<?= $asm['retest_count'] ?? 0 ?>"
            data-retest-rejected="<?= ($asm['retest_rejected'] ?? false) ? '1' : '0' ?>">
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
                        <?php elseif ($asm['retest_rejected'] ?? false): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" title="Retest request denied">
                                Retest Rejected
                            </span>
                        <?php elseif (($asm['retest_count'] ?? 0) < 1): ?>
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
                <?php elseif ($now > $end && ($asm['retest_count'] ?? 0) < 1): ?>
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
