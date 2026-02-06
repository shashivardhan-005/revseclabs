<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>My Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 justify-content-center">
    <!-- Left Column: Current Info & Account Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-primary py-5 text-center position-relative">
                <div class="position-relative z-1">
                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                    <h5 class="text-white mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <p class="text-white-50 small mb-0"><?= esc($user['email']) ?></p>
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background: url('<?= base_url('static/img/pattern.png') ?>');"></div>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Department</label>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building me-2 text-primary"></i>
                        <span class="fw-bold"><?= esc($user['department'] ?: 'General') ?></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Account Created</label>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>
                        <span class="fw-bold"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
                <div>
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Status</label>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Verified Member</span>
                </div>
            </div>
        </div>

        <!-- Security Quick-links & Instructions -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-primary"></i>Security Center</h6>
                <p class="small text-muted mb-4">Protect your account by ensuring your credentials are up to date.</p>
                
                <div class="bg-light p-3 rounded-4 mb-3">
                    <h6 class="small fw-bold mb-2">Changing your password?</h6>
                    <p class="x-small text-muted mb-3">Strong passwords contain at least 8 characters, including numbers and special signs.</p>
                    <a href="<?= base_url('password/change') ?>" class="btn btn-primary btn-sm w-100 rounded-3">
                        Secure New Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Profile Update & Activity -->
    <div class="col-lg-7">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Update Section -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-2">Account Management</h5>
            <p class="text-muted small mb-4">Request changes to your official profile details. All updates require administrative review for security purposes.</p>

            <?php if ($active_request): ?>
                <div class="bg-info bg-opacity-10 border border-info border-opacity-25 p-4 rounded-4">
                    <div class="d-flex">
                        <div class="bg-info bg-opacity-20 p-2 rounded-circle text-info me-3 h-100">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-info mb-1">Review in Progress</h6>
                            <p class="mb-3 small text-dark opacity-75">An administrator is currently validating your requested changes. You'll be notified via email once approved.</p>
                            <div class="bg-white p-3 rounded-3 small">
                                <div class="mb-2"><strong>Requested Name:</strong> <?= esc($active_request['new_full_name'] ?: 'No change') ?></div>
                                <div class="mb-0"><strong>Requested Dept:</strong> <?= esc($active_request['new_department'] ?: 'No change') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <form action="<?= base_url('profile/request') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="new_full_name" class="form-control border-light bg-light" placeholder="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Department</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-building text-muted"></i></span>
                                <input type="text" name="new_department" class="form-control border-light bg-light" placeholder="<?= esc($user['department'] ?: 'Your Department') ?>">
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3">
                                Submit for Review <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Role Guidance -->
        <div class="card border-0 bg-light p-4 rounded-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2 text-warning"></i>Why are updates reviewed?</h6>
            <p class="small text-muted mb-0">
                To ensure the integrity of your assessment records and certification, all identity changes must be verified against HR records.
                This prevents unauthorized profile modifications and protects your professional standing within the RevSecLabs platform.
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
