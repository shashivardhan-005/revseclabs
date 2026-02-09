<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Quiz Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Manage Quizzes</h3>
        <div class="actions">
            <a href="<?= base_url('admin/quizzes/create') ?>" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i> Create New Quiz
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Quiz Name</th>
                    <th>Timeline</th>
                    <th>Duration</th>
                    <th>Questions</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td class="fw-bold"><?= esc($quiz['name']) ?></td>
                    <td class="small">
                        <?= date('M d, H:i', strtotime($quiz['start_time'])) ?> - <?= date('M d, H:i', strtotime($quiz['end_time'])) ?>
                    </td>
                    <td><?= esc($quiz['duration_minutes']) ?> min</td>
                    <td><?= esc($quiz['total_questions']) ?></td>
                    <td>
                        <?php 
                        $now = time();
                        $start = strtotime($quiz['start_time']);
                        $end = strtotime($quiz['end_time']);
                        if ($now < $start): ?>
                            <span class="badge bg-info text-dark">Upcoming</span>
                        <?php elseif ($now > $end): ?>
                            <span class="badge bg-secondary">Expired</span>
                        <?php else: ?>
                            <span class="badge bg-success">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="<?= base_url('admin/quizzes/edit/'.$quiz['id']) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('admin/quizzes/export/'.$quiz['id']) ?>" class="btn btn-outline-secondary btn-sm" title="Export Results">
                                <i class="bi bi-download"></i>
                            </a>
                            <a href="<?= base_url('admin/quizzes/toggle-release/'.$quiz['id']) ?>" class="btn <?= $quiz['results_released'] ? 'btn-success' : 'btn-outline-warning' ?> btn-sm" title="<?= $quiz['results_released'] ? 'Results Released (Click to Lock)' : 'Results Pending (Click to Release)' ?>">
                                <i class="bi <?= $quiz['results_released'] ? 'bi-envelope-check' : 'bi-envelope' ?>"></i>
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-sm" onclick="confirmModal('<?= base_url('admin/quizzes/delete/'.$quiz['id']) ?>', 'Are you sure you want to delete this quiz?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
