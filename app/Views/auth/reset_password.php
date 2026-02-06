<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Reset Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Reset Password</h2>
        <p class="text-muted">Please enter your new password below.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('reset-password/update') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required placeholder="Minimum 8 characters">
        </div>
        
        <div class="mb-4">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat new password">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Reset Password</button>
    </form>
</div>
<?= $this->endSection() ?>
