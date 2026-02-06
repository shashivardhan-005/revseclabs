<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Change Password<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header text-center py-4 bg-warning">
                <h3 class="font-weight-light my-1">Change Password</h3>
                <p class="mb-0 text-muted">Security Requirement</p>
            </div>
            <div class="card-body p-5">
                <form action="<?= base_url('password/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="newPassword" type="password" name="new_password" placeholder="New Password" required minlength="8" />
                        <label for="newPassword">New Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="confirmPassword" type="password" name="confirm_password" placeholder="Confirm Password" required minlength="8" />
                        <label for="confirmPassword">Confirm Password</label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                        <button type="submit" class="btn btn-warning btn-lg w-100">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
