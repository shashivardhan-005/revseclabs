<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?><?= $user ? 'Edit' : 'Create' ?> User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <h3 class="card-title mb-4"><?= $user ? 'Edit User' : 'Create New User' ?></h3>
    
    <form action="<?= base_url('admin/users/save') ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($user): ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= $user ? esc($user['first_name']) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= $user ? esc($user['last_name']) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= $user ? esc($user['email']) : '' ?>" required <?= $user ? 'readonly' : '' ?>>
                    <?php if ($user): ?>
                        <div class="form-text">Email address cannot be changed.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="<?= $user ? esc($user['department']) : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><?= $user ? 'New Password (leave blank to keep current)' : 'Password (leave blank for auto-generate)' ?></label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_staff" id="is_staff" <?= ($user && $user['is_staff']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_staff">Administrator Access</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-light px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5">Save User</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
