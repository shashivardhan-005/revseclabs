<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Assessment Results<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 justify-content-center">
    <div class="col-lg-10 col-xl-9" id="report-content">
        <!-- Results Summary Header -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-center align-items-center p-5 text-center">
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Final Score</h6>
                        <div class="display-3 fw-bold mb-2"><?= round((float)$assignment['score'], 1) ?>%</div>
                        <span class="badge <?= $assignment['score'] >= 70 ? 'bg-success' : 'bg-danger' ?> px-4 py-2 rounded-pill shadow-sm">
                            <?= $assignment['score'] >= 70 ? 'CERTIFIED' : 'NOT CERTIFIED' ?>
                        </span>
                    </div>
                    <div class="col-md-8 p-5 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="fw-bold mb-1 text-dark"><?= esc($quiz['name']) ?></h2>
                                <p class="text-muted small mb-0"><i class="bi bi-calendar3 me-2"></i>Assessment finalized on <?= date('M d, Y H:i', strtotime($assignment['completed_at'])) ?></p>
                            </div>
                            <button id="download-pdf" class="btn btn-primary btn-sm rounded-pill px-4 d-none d-lg-block" data-html2canvas-ignore="true">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                            </button>
                        </div>
                        
                        <div class="row g-3">
                            <?php
                            $correct = 0; $total = count($results);
                            foreach($results as $r) if($r['is_correct']) $correct++;
                            ?>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <h6 class="text-muted small fw-bold mb-1">Total Questions</h6>
                                    <div class="h4 fw-bold mb-0"><?= $total ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-success-subtle rounded-4 text-center">
                                    <h6 class="text-success small fw-bold mb-1">Correct</h6>
                                    <div class="h4 fw-bold text-success mb-0"><?= $correct ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-danger-subtle rounded-4 text-center">
                                    <h6 class="text-danger small fw-bold mb-1">Incorrect</h6>
                                    <div class="h4 fw-bold text-danger mb-0"><?= $total - $correct ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <h4 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-journal-text me-3 text-primary"></i>
            Detailed Performance Breakdown
        </h4>

        <?php foreach ($results as $index => $res): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden result-item-card <?= $res['is_correct'] ? 'border-start border-4 border-success' : 'border-start border-4 border-danger' ?>">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="fw-bold text-dark d-flex gap-3">
                            <span class="text-primary">Q<?= $index + 1 ?>.</span>
                            <?= esc($res['question']['text']) ?>
                        </h6>
                        <span class="ms-3">
                            <?php if ($res['is_correct']): ?>
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if (!empty($res['question']['image_base64'])): ?>
                        <div class="mb-3">
                            <img src="<?= $res['question']['image_base64'] ?>" class="img-fluid rounded-3 border" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>

                    <div class="options-list d-grid gap-2 mb-4">
                        <?php foreach ($res['options'] as $opt): ?>
                            <?php 
                                $isUserSelection = ($opt['id'] == $res['selected_option_id']);
                                $isCorrect = (bool)$opt['is_correct'];
                                
                                $class = 'bg-light text-muted';
                                $icon = '';
                                
                                if ($isCorrect) {
                                    $class = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                    $icon = '<i class="bi bi-check-lg ms-2"></i>';
                                }
                                
                                if ($isUserSelection && !$isCorrect) {
                                    $class = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    $icon = '<i class="bi bi-x-lg ms-2"></i>';
                                }
                            ?>
                            <div class="p-3 rounded-3 small d-flex justify-content-between align-items-center <?= $class ?>">
                                <span><?= esc($opt['text']) ?></span>
                                <div>
                                    <?php if ($isUserSelection): ?>
                                        <span class="badge bg-primary text-white me-2" style="font-size: 0.65rem;">YOUR CHOICE</span>
                                    <?php endif; ?>
                                    <?= $icon ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($res['question']['explanation'])): ?>
                        <div class="bg-light p-4 rounded-4 border-dashed border-2">
                            <h6 class="fw-bold small mb-2"><i class="bi bi-lightbulb me-2 text-warning"></i>Educational Insight</h6>
                            <p class="small text-muted mb-0"><?= esc($res['question']['explanation']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-center mt-5 mb-5" data-html2canvas-ignore="true">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary rounded-pill px-5">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar, footer, .btn-secondary, .btn-outline-secondary { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .result-item-card { break-inside: avoid; }
    }
    .result-item-card {
        transition: transform 0.2s ease;
    }
    .result-item-card:hover {
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('download-pdf').addEventListener('click', function () {
        const element = document.getElementById('report-content');
        const opt = {
            margin:       0.5,
            filename:     'Assessment_Report.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    });
</script>
<?= $this->endSection() ?>
