<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Question Bank<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Question Bank</h3>
        <div class="actions">
            <a href="<?= base_url('admin/topics') ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-tags me-1"></i> Topics
            </a>
            <a href="<?= base_url('admin/questions/create') ?>" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i> Add Question
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
                    <th style="max-width: 400px;">Question</th>
                    <th>Topic</th>
                    <th>Difficulty</th>
                    <th>Image</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): ?>
                <tr>
                    <td>
                        <div class="text-truncate" style="max-width: 400px;" title="<?= esc($q['text']) ?>">
                            <?= esc($q['text']) ?>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= esc($q['topic_name'] ?: 'General') ?></span></td>
                    <td>
                        <span class="badge <?= $q['difficulty'] == 'HARD' ? 'bg-danger' : ($q['difficulty'] == 'MEDIUM' ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= esc($q['difficulty']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($q['image_base64']): ?>
                            <i class="bi bi-image text-primary" title="Has Image"></i>
                        <?php else: ?>
                            <span class="text-muted small">None</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="<?= base_url('admin/questions/edit/'.$q['id']) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-sm" onclick="confirmModal('<?= base_url('admin/questions/delete/'.$q['id']) ?>', 'Are you sure you want to delete this question?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
