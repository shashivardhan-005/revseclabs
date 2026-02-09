<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Analytics Reports<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($status_labels) ?>,
            datasets: [{
                data: <?= json_encode($status_values) ?>,
                backgroundColor: ['#e5e7eb', '#3b82f6', '#10b981'], // Grey, Blue, Green
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Dept Chart
    const ctxDept = document.getElementById('deptChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'bar',
        data: {
            labels: <?= json_encode($dept_labels) ?>,
            datasets: [{
                label: 'Average Score',
                data: <?= json_encode($dept_scores) ?>,
                backgroundColor: '#1e3a8a',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card mb-4">
            <form action="<?= base_url('admin/analytics') ?>" method="get" class="row align-items-end g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase">Filter Reports by Quiz</label>
                    <select name="quiz_id" class="form-select form-select-lg border-0 bg-light" onchange="this.form.submit()">
                        <option value="">-- All Quizzes (Global Summary) --</option>
                        <?php foreach ($quizzes as $q): ?>
                            <option value="<?= $q['id'] ?>" <?= ($selected_quiz_id == $q['id']) ? 'selected' : '' ?>>
                                <?= esc($q['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (!empty($selected_quiz_id)): ?>
                    <div class="col-md-2">
                        <a href="<?= base_url('admin/analytics') ?>" class="btn btn-outline-secondary w-100">Clear Filter</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
                <?php 
                    $selectedQuiz = null;
                    if ($selected_quiz_id) {
                        foreach ($quizzes as $q) {
                            if ($q['id'] == $selected_quiz_id) { $selectedQuiz = $q; break; }
                        }
                    }
                    $contextTitle = $selectedQuiz ? " for '" . esc($selectedQuiz['name']) . "'" : " (Global Summary)";
                ?>
                <div class="col-md-6">
                    <div class="content-card h-100" style="background: #f0f9ff; padding: 25px; border-radius: 12px; border: 1px solid #bae6fd;">
                        <h3 class="card-title text-dark mb-4">Completion Status<?= $contextTitle ?></h3>
                        <div style="height: 300px; position: relative;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="content-card h-100" style="background: #f0f9ff; padding: 25px; border-radius: 12px; border: 1px solid #bae6fd;">
                        <h3 class="card-title text-dark mb-4">Dept Performance<?= $contextTitle ?></h3>
                        <div style="height: 300px; position: relative;">
                            <canvas id="deptChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h3 class="card-title">Security & Cheat Violations<?= $contextTitle ?></h3>
    <div class="table-responsive mt-3">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Violation Description</th>
                    <th class="text-end">Event Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($violations as $v): ?>
                <tr>
                    <td><?= $v['details'] ?></td>
                    <td class="text-end fw-bold text-danger"><?= $v['count'] ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($violations)): ?>
                <tr>
                    <td colspan="2" class="text-center py-4 text-muted">No security violations recorded yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
