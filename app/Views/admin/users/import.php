<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Import Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="content-card">
            <h3 class="card-title mb-4">Import Users from CSV</h3>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('admin/users/process-import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label">Select CSV File</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    <div class="form-text mt-2 text-muted">
                        Format: <span class="bg-light p-1 rounded">email, first_name, last_name, department</span>
                    </div>
                </div>

                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-2"></i> Password will be auto-generated and sent to users after successful import.
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3">
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-link text-decoration-none">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Import Now</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
