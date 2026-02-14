<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-stretch mt-5">
    <!-- Login Form Panel -->
    <div class="col-lg-5 bg-white rounded-4 p-4 p-md-5 shadow-lg card border-0">
        <div class="text-center mb-5 d-lg-none">
            <img src="<?= base_url('static/images/revseclabs-logo.png') ?>" alt="RevSecLabs" height="60" class="mb-2">
            <h3 class="fw-bold mt-2">RevSecLabs</h3>
        </div>
        
        <h3 class="fw-bold mb-2">Welcome Back</h3>
        <p class="text-muted mb-4">Please enter your credentials to continue.</p>

        <form action="<?= base_url('login/attempt') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="yourname@company.com" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group position-relative">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" id="loginPassword" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted" onclick="togglePassword('loginPassword', this)" style="text-decoration: none; z-index: 10;">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
                <div class="mt-2 text-end">
                    <a href="<?= base_url('forgot-password') ?>" class="small text-primary fw-semibold text-decoration-none">Forgot Password?</a>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold">
                    Secure Login <i class="bi bi-arrow-right ms-2"></i>
                </button>
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
