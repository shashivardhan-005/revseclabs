<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Submission Successful<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">
        <div class="card shadow-lg p-5 border-0">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success display-1"></i>
            </div>
            <h2 class="mb-3">Quiz Submitted!</h2>
            <p class="text-muted lead mb-4">Your responses have been recorded successfully. Results will be released once the quiz window closes.</p>
            <div class="d-grid gap-2">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-lg">Return to Dashboard</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
