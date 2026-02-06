<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Topic Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-5">
        <div class="content-card">
            <h3 class="card-title mb-4">Add Topic</h3>
            <form action="<?= base_url('admin/topics/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Topic Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Email Security" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Topic</button>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="content-card">
            <h3 class="card-title mb-4">Existing Topics</h3>
            
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
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td><?= $topic['id'] ?></td>
                            <td class="fw-bold"><?= esc($topic['name']) ?></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-outline-danger btn-sm" onclick="confirmModal('<?= base_url('admin/topics/delete/'.$topic['id']) ?>', 'Are you sure you want to delete this topic?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
