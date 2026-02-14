<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Reset Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center mt-5">
    <div class="col-lg-5 col-md-8">
        <div class="card bg-white rounded-5 shadow-lg border-0 overflow-hidden" style="border-radius: 2rem !important;">
            <div class="card-body p-0">
                <!-- Header with vibrant blue -->
                <div class="py-3 px-5 text-center bg-primary">
                    <h5 class="mb-0 fw-bold text-white d-flex align-items-center justify-content-center">
                        <i class="bi bi-shield-lock-fill me-2 h4 mb-0"></i> RESET PASSWORD
                    </h5>
                </div>
                
                <div class="p-4">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-0">Please enter your new secure password below.</p>
                    </div>



                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('reset-password/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <div class="position-relative">
                                <input type="password" id="newPassword" name="new_password" class="form-control form-control-lg border-0 bg-light rounded-3 py-3 pe-5" required minlength="8" placeholder="Minimum 8 characters" style="background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 text-muted" onclick="togglePassword('newPassword', this)" style="text-decoration: none;">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <div class="position-relative">
                                <input type="password" id="confirmPassword" name="confirm_password" class="form-control form-control-lg border-0 bg-light rounded-3 py-3 pe-5" required minlength="8" placeholder="Repeat new password" style="background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 text-muted" onclick="togglePassword('confirmPassword', this)" style="text-decoration: none;">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                Reset Password <i class="bi bi-arrow-right-circle h5 mb-0"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
    }
}
</script>
<?= $this->endSection() ?>
