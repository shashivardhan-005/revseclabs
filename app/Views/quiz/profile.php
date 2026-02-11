<?= $this->extend('layout/dashboard') ?>

<?= $this->section('title') ?>My Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 justify-content-center">

    <!-- Header: Prominent Profile Info -->
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
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

    <!-- Quick Actions -->
    <div class="col-md-6">
        <a href="<?= base_url('profile/edit') ?>" class="card shadow-sm border-0 rounded-4 p-4 h-100 text-decoration-none" style="transition: all 0.2s ease;">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-person-gear text-primary fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Update Profile</h5>
                    <p class="text-muted small mb-0">Request changes to your name or department</p>
                </div>
            </div>
            <div class="text-primary small fw-bold">
                Go to Profile Update <i class="bi bi-chevron-right"></i>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?= base_url('password/change') ?>" class="card shadow-sm border-0 rounded-4 p-4 h-100 text-decoration-none" style="transition: all 0.2s ease;">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-key-fill text-warning fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Password Update</h5>
                    <p class="text-muted small mb-0">Change your account password</p>
                </div>
            </div>
            <div class="text-primary small fw-bold">
                Go to Password Update <i class="bi bi-chevron-right"></i>
            </div>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
