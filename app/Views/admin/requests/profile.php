<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Profile Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <h3 class="card-title mb-4">Pending Profile Updates</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Current Info</th>
                    <th>Requested Changes</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td>
                        <div class="fw-bold"><?= $r['first_name'] ?> <?= $r['last_name'] ?></div>
                        <div class="text-muted small">ID: <?= $r['user_id'] ?></div>
                    </td>
                    <td>
                        <div class="small">Dept: <?= $r['current_dept'] ?></div>
                    </td>
                    <td>
                        <?php if ($r['new_full_name']): ?>
                            <div class="small"><i class="bi bi-person me-1"></i> <?= $r['new_full_name'] ?></div>
                        <?php endif; ?>
                        <?php if ($r['new_department']): ?>
                            <div class="small"><i class="bi bi-building me-1"></i> <?= $r['new_department'] ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <form action="<?= base_url('admin/profile-requests/approve/'.$r['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-success btn-sm" onclick="confirmFormSubmit(this.form, 'Approve this profile change request?');">Approve</button>
                            </form>
                            <form action="<?= base_url('admin/profile-requests/reject/'.$r['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmFormSubmit(this.form, 'Reject this profile change request?');">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">No pending requests.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
