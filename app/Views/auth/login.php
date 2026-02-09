<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-stretch min-vh-75 mt-5">
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
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Password</label>
                    <a href="<?= base_url('forgot-password') ?>" class="small text-primary fw-semibold text-decoration-none">Forgot Password?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold">
                    Secure Login <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </form>

        <hr class="my-5 opacity-25">

        <div class="bg-light p-4 rounded-4 border border-dashed text-center">
            <h6 class="fw-bold text-dark mb-2">Trouble Logging In?</h6>
            <p class="small text-muted mb-0">
                To set or reset your password, use the <a href="<?= base_url('forgot-password') ?>" class="fw-bold">Forgot Password</a> link.
                We'll email you a secure link to create a new one instantly.
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
