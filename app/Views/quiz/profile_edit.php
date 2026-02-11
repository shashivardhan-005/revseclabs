<?= $this->extend('layout/dashboard') ?>

<?= $this->section('title') ?>Update Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Back Link -->
        <a href="<?= base_url('profile') ?>" class="d-inline-flex align-items-center text-muted text-decoration-none mb-3" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-2"></i> Back to Profile
        </a>

        <div class="card shadow-sm border-0 rounded-4 p-4">
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
                <!-- Current Info -->
                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Current Name</span>
                            <span class="fw-bold"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Current Department</span>
                            <span class="fw-bold"><?= esc($user['department'] ?: 'General') ?></span>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('profile/request') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">New Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="new_full_name" class="form-control border-light bg-light" placeholder="Enter new name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">New Department</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-building text-muted"></i></span>
                                <input type="text" name="new_department" class="form-control border-light bg-light" placeholder="Enter new department">
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">
                                Submit for Review <i class="bi bi-send ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
