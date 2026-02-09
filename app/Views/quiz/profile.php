<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>My Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white">My Profile</h2>
        <hr class="text-white opacity-25">
    </div>
</div>
<div class="row g-4 justify-content-center">
    <!-- Header: Prominent Profile Info -->
    <div class="col-lg-11">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-2">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-auto bg-primary py-5 px-4 text-center d-flex flex-column align-items-center justify-content-center" style="min-width: 250px;">
                        <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                        </div>
                        <h4 class="text-white mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                        <span class="badge bg-white text-primary rounded-pill px-3 fw-bold">Verified Member</span>
                    </div>
                    <div class="col-md p-4 d-flex align-items-center">
                        <div class="row w-100 g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Email Address</label>
                                <div class="d-flex align-items-center overflow-hidden">
                                    <i class="bi bi-envelope me-2 text-primary"></i>
                                    <span class="fw-bold text-nowrap"><?= esc($user['email']) ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Joined Since</label>
                                <div class="d-flex align-items-center text-nowrap">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                                    <span class="fw-bold"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Department</label>
                                <div class="d-flex align-items-center text-nowrap">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    <span class="fw-bold"><?= esc($user['department'] ?: 'General') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-11">
        <div class="row g-4">
            <!-- Left: Update Profile Section -->
            <div class="col-lg-8">
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

                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-person-gear text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Account Management</h5>
                            <p class="text-muted small mb-0">Request changes to your profile details</p>
                        </div>
                    </div>

                    <?php if ($active_request): ?>
                        <div class="bg-info bg-opacity-10 border border-info border-opacity-25 p-4 rounded-4">
                            <div class="d-flex">
                                <div class="bg-info bg-opacity-20 p-2 rounded-circle text-info me-3 h-100">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div class="w-100">
                                    <h6 class="fw-bold text-info mb-1">Review in Progress</h6>
                                    <p class="mb-3 small text-dark opacity-75">An administrator is currently validating your requested changes.</p>
                                    <div class="bg-white p-3 rounded-3 small shadow-sm">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Requested Name:</span>
                                            <span class="fw-bold"><?= esc($active_request['new_full_name'] ?: 'No change') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Requested Dept:</span>
                                            <span class="fw-bold"><?= esc($active_request['new_department'] ?: 'No change') ?></span>
                                        </div>
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
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 w-100 w-sm-auto">
                                        Submit for Review <i class="bi bi-send ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Security Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-shield-lock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Security</h5>
                            <p class="text-muted small mb-0">Credential settings</p>
                        </div>
                    </div>
                    
                    <div class="bg-light p-4 rounded-4 text-center">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-key-fill fs-1"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Change Password</h6>
                        <p class="small text-muted mb-4">Ensure your account is using a strong, unique password.</p>
                        <a href="<?= base_url('password/change') ?>" class="btn btn-outline-primary w-100 rounded-3">
                            Update Credentials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
