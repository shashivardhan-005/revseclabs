<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Forgot Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center min-vh-75 mt-5">
    <div class="col-lg-5 col-md-8">
        <div class="card bg-white rounded-4 shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle text-primary">
                            <i class="bi bi-key-fill display-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark">Forgot Password?</h3>
                    <p class="text-muted">Enter your registered email and we'll send you a secure link to reset it.</p>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-10 text-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('forgot-password/send') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0 bg-light" required placeholder="name@company.com">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-3 fw-bold rounded-3 shadow-sm">
                            Send Reset Link <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top border-dashed">
                    <a href="<?= base_url('login') ?>" class="text-decoration-none fw-semibold text-primary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Back to Secure Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
