<?= $this->extend('layout/dashboard') ?>

<?= $this->section('title') ?>Start Quiz<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <!-- Start Header -->
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                <i class="bi bi-rocket-takeoff fs-2"></i>
            </div>
            <h2 class="fw-bold mb-2">Ready to Start?</h2>
            <p class="text-muted">Assessment Prepared: <strong><?= esc($quiz['name']) ?></strong></p>
        </div>

        <!-- Assessment Rules & Details -->
        <div class="row g-4 mb-5">
            <!-- Details Column -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-info-square me-2"></i>Details</h6>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted small">Duration</span>
                            <span class="fw-bold small"><?= $quiz['duration_minutes'] ?> Minutes</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted small">Passing Score</span>
                            <span class="fw-bold small text-success"><?= $quiz['pass_score'] ?>%</span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between">
                            <span class="text-muted small">Attempts</span>
                            <span class="fw-bold small">1 Allowed</span>
                        </div>

                        <!-- Availability Window -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold mb-3 text-muted small uppercase">Availability Window</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="bg-success bg-opacity-10 text-success p-1 rounded">
                                    <i class="bi bi-calendar-check small"></i>
                                </div>
                                <span class="small fw-bold text-dark">Starts: <?= date('M d, Y - H:i', strtotime($quiz['start_time'])) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-danger bg-opacity-10 text-danger p-1 rounded">
                                    <i class="bi bi-calendar-x small"></i>
                                </div>
                                <span class="small fw-bold text-dark">Ends: <?= date('M d, Y - H:i', strtotime($quiz['end_time'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rules Column -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-danger"><i class="bi bi-shield-exclamation me-2"></i>Critical Rules</h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-3 d-flex align-items-start gap-2">
                                <i class="bi bi-fullscreen text-danger mt-1"></i>
                                <span><strong>Full-Screen Required:</strong> The assessment stays in full-screen. Exiting will log a violation.</span>
                            </li>
                            <li class="mb-3 d-flex align-items-start gap-2">
                                <i class="bi bi-window-x text-danger mt-1"></i>
                                <span><strong>No Tab Switching:</strong> Navigating away from this window will auto-submit your response.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-cursor-text text-danger mt-1"></i>
                                <span><strong>Input Restricted:</strong> Copy, paste, and right-click have been disabled for security.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Checkbox & CTA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 text-center">
                <div class="mb-4">
                    <p class="small text-muted mb-0">Ensure you have a <strong>stable internet connection</strong> and <strong>no distractions</strong> for the next <?= $quiz['duration_minutes'] ?> minutes.</p>
                </div>
                
                <div class="d-grid gap-2 col-md-8 mx-auto">
                    <a href="<?= base_url('quiz/'.$assignment['id'].'/take') ?>" class="btn btn-primary btn-lg shadow rounded-3 py-3 fw-bold">
                        Initialize Assessment <i class="bi bi-chevron-right ms-2"></i>
                    </a>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-link text-muted text-decoration-none small">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <p class="text-center small text-black italic">
            By clicking "Initialize Assessment", you consent to real-time integrity monitoring.
        </p>
    </div>
</div>
<?= $this->endSection() ?>
