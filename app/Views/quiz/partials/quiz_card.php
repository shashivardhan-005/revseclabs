<?php
// Expects: $asm (array), $now (timestamp, optional)
$now = $now ?? time();
$start = strtotime($asm['start_time']);
$end = strtotime($asm['end_time']);
?>
<div class="col-md-12 col-lg-6 col-xl-6 quiz-card-container" data-quiz-id="<?= $asm['id'] ?>">
    <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift transition-all" 
            style="background: #fff; border: 1px solid rgba(0,0,0,0.05);"
            data-start-time="<?= $start ?>" 
            data-end-time="<?= $end ?>"
            data-start-url="<?= base_url('quiz/'.$asm['id'].'/start') ?>"
            data-end-time-str="<?= date('M d, H:i', $end) ?>"
            data-status="<?= $asm['status'] ?>"
            data-retest-count="<?= $asm['retest_count'] ?? 0 ?>"
            data-retest-rejected="<?= ($asm['retest_rejected'] ?? false) ? '1' : '0' ?>">
        
        <div class="card-body p-4 d-flex flex-column">
            <!-- Header: Topic & Duration -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <?= esc($asm['topic_display']) ?>
                </span>
                <span class="text-muted fw-bold d-flex align-items-center" style="font-size: 0.85rem;">
                    <i class="bi bi-stopwatch me-1 text-secondary"></i> <?= $asm['duration_minutes'] ?>m
                </span>
            </div>
            
            <!-- Title -->
            <h5 class="fw-bolder mb-2 text-dark" style="font-size: 1.25rem; letter-spacing: -0.5px;"><?= esc($asm['quiz_name']) ?></h5>
            
            <?php if (($asm['retest_count'] ?? 0) >= 1): ?>
                <div class="mb-3">
                    <span class="badge bg-warning text-dark border-0 rounded-pill px-2">
                        <i class="bi bi-arrow-repeat me-1"></i> Retest Available
                    </span>
                </div>
            <?php else: ?>
                <div class="mb-2"></div> 
            <?php endif; ?>

            <!-- Date Grid -->
            <div class="row g-2 mb-4 mt-auto">
                <div class="col-6">
                    <div class="p-3 rounded-4 h-100" style="background: #f8faff; border: 1px dashed #cce5ff;">
                        <div class="text-primary small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Opens</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-primary opacity-50"><i class="bi bi-calendar-event"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="line-height: 1.2; font-size: 0.9rem;"><?= date('M d', $start) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= date('h:i A', $start) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4 h-100" style="background: #fff5f5; border: 1px dashed #f5c2c7;">
                        <div class="text-danger small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Closes</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-danger opacity-50"><i class="bi bi-calendar-x"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="line-height: 1.2; font-size: 0.9rem;"><?= date('M d', $end) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= date('h:i A', $end) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timer / Status -->
            <div class="mb-3 timer-display-container">
                 <!-- JS injects timer here -->
                 <div class="d-flex align-items-center justify-content-center py-2 px-3 rounded-pill bg-opacity-10 
                    <?= $now < $start ? 'bg-warning text-warning' : ($now > $end ? 'bg-secondary text-secondary' : 'bg-success text-success') ?>"
                    style="font-size: 0.9rem;">
                    <i class="bi <?= $now < $start ? 'bi-hourglass' : ($now > $end ? 'bi-x-circle' : 'bi-clock-history') ?> me-2 fs-5"></i>
                    <span class="fw-bold">
                        <?php if ($now < $start): ?>
                            Opens Soon
                        <?php elseif ($now > $end): ?>
                            Expired
                        <?php else: ?>
                            In Progress
                        <?php endif; ?>
                    </span>
                 </div>
            </div>

            <!-- Action Button -->
            <div class="d-grid">
                <?php if ($asm['status'] === 'COMPLETED'): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light border">
                        <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Submitted</span>
                        <?php if ($asm['retest_requested']): ?>
                            <span class="badge bg-warning text-dark">Pending Review</span>
                        <?php elseif ($asm['retest_rejected'] ?? false): ?>
                            <span class="badge bg-danger">Retest Denied</span>
                        <?php elseif (($asm['retest_count'] ?? 0) < 1): ?>
                            <form action="<?= base_url('quiz/retest/' . $asm['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted p-0 fw-bold" style="font-size: 0.8rem;">
                                    Request Retest
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php elseif ($now < $start): ?>
                    <button class="btn btn-light disabled text-muted fw-bold py-3 rounded-3 border-0" style="background: #e9ecef;">
                        <i class="bi bi-lock-fill me-2"></i> Locked
                    </button>
                <?php elseif ($now > $end && ($asm['retest_count'] ?? 0) < 1): ?>
                    <button class="btn btn-light disabled text-muted fw-bold py-3 rounded-3 border-0" style="background: #e9ecef;">
                         Assessment Closed
                    </button>
                <?php else: ?>
                    <a href="<?= base_url('quiz/'.$asm['id'].'/start') ?>" 
                       class="btn btn-primary fw-bold py-3 shadow-sm rounded-3 hover-scale d-flex align-items-center justify-content-center"
                       style="background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%); border: none;">
                        <span><?= ($asm['status'] === 'STARTED') ? 'Continue Assessment' : 'Start Assessment' ?></span>
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1)!important; }
.hover-scale { transition: transform 0.2s; }
.hover-scale:hover { transform: scale(1.02); }
</style>
