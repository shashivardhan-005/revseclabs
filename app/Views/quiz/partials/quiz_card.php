<?php
// Expects: $asm (array), $now (timestamp, optional)
$now = $now ?? time();
$start = strtotime($asm['start_time']);
$end = strtotime($asm['end_time']);

// Determine status
$isActive = ($now >= $start && $now <= $end && $asm['status'] !== 'COMPLETED');
$isUpcoming = ($now < $start);
$isExpired = ($now > $end && $asm['status'] !== 'COMPLETED');
$isStarted = ($asm['status'] === 'STARTED');
$isCompleted = ($asm['status'] === 'COMPLETED');
$retestAvailable = (($asm['retest_count'] ?? 0) >= 1);
?>
<div class="assessment-item quiz-card-container" data-quiz-id="<?= $asm['id'] ?>"
     data-start-time="<?= $start ?>"
     data-end-time="<?= $end ?>"
     data-start-url="<?= base_url('quiz/'.$asm['id'].'/start') ?>"
     data-end-time-str="<?= date('M d, H:i', $end) ?>"
     data-status="<?= $asm['status'] ?>"
     data-retest-count="<?= $asm['retest_count'] ?? 0 ?>"
     data-retest-rejected="<?= ($asm['retest_rejected'] ?? false) ? '1' : '0' ?>">

    <!-- Left: Info -->
    <div class="item-info">
        <div class="item-title">
            <?= esc($asm['quiz_name']) ?>
            <?php if ($retestAvailable): ?>
                <span class="badge bg-warning text-dark" style="font-size:0.65rem; padding:3px 8px; border-radius:6px;">
                    <i class="bi bi-arrow-repeat"></i> Retest
                </span>
            <?php endif; ?>
        </div>
        <div class="item-meta">
            <span><i class="bi bi-folder2 me-1"></i><?= esc($asm['topic_display']) ?></span>
            <span><i class="bi bi-calendar-event me-1"></i><?= date('M d, h:i A', $start) ?></span>
            <span><i class="bi bi-clock-history me-1"></i><?= date('M d, h:i A', $end) ?></span>
            <span><i class="bi bi-stopwatch me-1"></i><?= $asm['duration_minutes'] ?>m</span>
        </div>
    </div>

    <!-- Right: Status + Timer + Action -->
    <div class="item-status">
        <!-- Timer Container (JS will update this) -->
        <div class="timer-display-container">
            <?php if ($isActive): ?>
                <span class="timer-pill" style="color:var(--dash-success);">
                    <i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> 
                    <span class="fw-bold">Active</span>
                </span>
            <?php elseif ($isUpcoming): ?>
                <span class="status-badge status-upcoming">
                    <i class="bi bi-hourglass-split"></i> Upcoming
                </span>
            <?php elseif ($isCompleted): ?>
                <span class="status-badge status-active">
                    <i class="bi bi-check-circle-fill"></i> Submitted
                </span>
            <?php elseif ($isExpired): ?>
                <span class="status-badge status-expired">
                    <i class="bi bi-x-circle"></i> Expired
                </span>
            <?php endif; ?>
        </div>

        <!-- Action -->
        <div class="d-grid action-button-container">
            <?php if ($isCompleted): ?>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($asm['retest_requested']): ?>
                        <span class="status-badge status-upcoming"><i class="bi bi-hourglass"></i> Pending Review</span>
                    <?php elseif ($asm['retest_rejected'] ?? false): ?>
                        <span class="status-badge status-expired"><i class="bi bi-x-lg"></i> Retest Denied</span>
                    <?php elseif (($asm['retest_count'] ?? 0) < 1): ?>
                        <form action="<?= base_url('quiz/retest/' . $asm['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-action btn-action-primary" style="font-size:0.72rem; padding:6px 14px; background: var(--dash-navy-light);">
                                Request Retest
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php elseif ($isUpcoming): ?>
                <span class="btn-action btn-action-disabled"><i class="bi bi-lock-fill"></i> Locked</span>
            <?php elseif ($isExpired && !$retestAvailable): ?>
                <span class="btn-action btn-action-disabled">Closed</span>
            <?php else: ?>
                <a href="<?= base_url('quiz/'.$asm['id'].'/start') ?>"
                   class="btn-action btn-action-primary">
                    <?= $isStarted ? 'Continue' : 'Start' ?> <i class="bi bi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
