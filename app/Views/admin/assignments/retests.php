<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Retest Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <h3 class="card-title mb-4">Pending Retest Approvals</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success mt-3"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Quiz</th>
                    <th>Previous Score</th>
                    <th>Request Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                <tr>
                    <td><?= $a['email'] ?></td>
                    <td><?= $a['quiz_name'] ?></td>
                    <td><?= $a['score'] ? round($a['score']).'%' : '-' ?></td>
                    <td class="small"><?= date('M d, H:i', strtotime($a['assigned_at'])) ?></td>
                    <td class="text-end">
                        <form action="<?= base_url('admin/assignments/approve-retest/'.$a['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i> Approve Retest
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($assignments)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-patch-check h1 d-block mb-3"></i>
                            No pending retest requests at the moment.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
