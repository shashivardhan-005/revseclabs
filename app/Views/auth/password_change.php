<?= $this->extend('layout/dashboard') ?>

<?= $this->section('title') ?>Change Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center py-3">
    <div class="col-lg-4 col-md-7">
        <div class="card bg-white rounded-5 shadow-lg border-0 overflow-hidden" style="border-radius: 2rem !important;">
            <div class="card-body p-0">
                <!-- Header with vibrant blue to match Reset Password -->
                <div class="py-2 px-4 text-center bg-primary">
                    <h6 class="mb-0 fw-bold text-white d-flex align-items-center justify-content-center">
                        <i class="bi bi-shield-lock-fill me-2 h5 mb-0"></i> CHANGE PASSWORD
                    </h6>
                </div>
                
                <div class="p-4 pt-4">
                    <div class="text-center mb-3">
                        <h2 class="fw-bold text-dark mb-2">Change Password</h2>
                        <p class="text-muted">You are required to set a new password before proceeding.</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show border-0 bg-warning bg-opacity-10 text-dark mb-4 rounded-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i><?= session()->getFlashdata('warning') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('password/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2">Current Password</label>
                            <input type="password" name="old_password" class="form-control border-0 bg-light rounded-3 py-2" required placeholder="Enter current password" style="background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2">New Password</label>
                            <div class="position-relative">
                                <input type="password" name="new_password" class="form-control border-0 bg-light rounded-3 py-2" required minlength="8" placeholder="Enter new password" style="background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">
                                <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                    <div class="bg-white rounded-circle p-1 shadow-sm border">
                                        <i class="bi bi-key-fill text-dark small"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control border-0 bg-light rounded-3 py-2" required minlength="8" placeholder="Confirm new password" style="background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                Update Password <i class="bi bi-check-circle-fill h5 mb-0"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
