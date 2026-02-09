<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?><?= $quiz ? 'Edit' : 'Create' ?> Quiz<?= $this->endSection() ?>
<?php 
    $timeFormat = get_setting('time_format', '24h');
    $phpFormat = ($timeFormat === '12h') ? 'Y-m-d h:i A' : 'Y-m-d H:i';
    $flatFormat = ($timeFormat === '12h') ? 'Y-m-d h:i K' : 'Y-m-d H:i';
?>

<?= $this->section('content') ?>
<div class="content-card">
    <h3 class="card-title mb-4"><?= $quiz ? 'Edit Quiz' : 'Create New Quiz' ?></h3>
    
    <form action="<?= base_url('admin/quizzes/save') ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($quiz): ?>
            <input type="hidden" name="id" value="<?= $quiz['id'] ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="mb-3">General Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Quiz Name</label>
                            <input type="text" name="name" class="form-control" value="<?= $quiz ? esc($quiz['name']) : '' ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="text" name="start_time" class="form-control flatpickr" value="<?= (isset($quiz['start_time']) && $quiz['start_time']) ? date('Y-m-d H:i:s', strtotime($quiz['start_time'])) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Time</label>
                                <input type="text" name="end_time" class="form-control flatpickr" value="<?= (isset($quiz['end_time']) && $quiz['end_time']) ? date('Y-m-d H:i:s', strtotime($quiz['end_time'])) : '' ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="<?= $quiz ? esc($quiz['duration_minutes']) : 15 ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Questions to Show</label>
                                <input type="number" name="total_questions" class="form-control" value="<?= $quiz ? esc($quiz['total_questions']) : 10 ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Quiz Settings</h5>
                        <div class="mb-3">
                            <label class="form-label">Topic</label>
                            <select name="topic_id" class="form-select">
                                <?php foreach ($topics as $topic): ?>
                                    <option value="<?= $topic['id'] ?>" <?= ($quiz && isset($quiz['topic_id']) && $quiz['topic_id'] == $topic['id']) ? 'selected' : '' ?>><?= $topic['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Passing Score (%)</label>
                            <input type="number" name="pass_score" class="form-control" value="<?= $quiz ? esc($quiz['pass_score']) : get_setting('default_passing_score', 70) ?>" min="0" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="EASY" <?= ($quiz && isset($quiz['difficulty']) && $quiz['difficulty'] == 'EASY') ? 'selected' : '' ?>>Easy</option>
                                <option value="MEDIUM" <?= (!$quiz || (isset($quiz['difficulty']) && $quiz['difficulty'] == 'MEDIUM')) ? 'selected' : '' ?>>Medium</option>
                                <option value="HARD" <?= ($quiz && isset($quiz['difficulty']) && $quiz['difficulty'] == 'HARD') ? 'selected' : '' ?>>Hard</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="force_full_screen" <?= ($quiz && isset($quiz['force_full_screen']) && $quiz['force_full_screen']) ? 'checked' : '' ?>>
                            <label class="form-check-label">Force Full Screen</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="detect_tab_switch" <?= ($quiz && isset($quiz['detect_tab_switch']) && $quiz['detect_tab_switch']) ? 'checked' : '' ?>>
                            <label class="form-check-label">Detect Tab Switched</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="auto_submit_on_violation" <?= ($quiz && isset($quiz['auto_submit_on_violation']) && $quiz['auto_submit_on_violation']) ? 'checked' : '' ?>>
                            <label class="form-check-label">Auto-submit Violation</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="results_released" <?= ($quiz && isset($quiz['results_released']) && $quiz['results_released']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-primary">Release Results to Users</label>
                        </div>
                        <div class="mt-3">
                            <label class="form-label small">Violation Limit</label>
                            <input type="number" name="violation_limit" class="form-control form-control-sm" value="<?= $quiz ? esc($quiz['violation_limit']) : 3 ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= base_url('admin/quizzes') ?>" class="btn btn-light px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5">Save Quiz Configuration</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
    flatpickr(".flatpickr", {
        enableTime: true,
        altInput: true,
        altFormat: "<?= ($timeFormat === '12h') ? 'Y-m-d h:i K' : 'Y-m-d H:i' ?>",
        dateFormat: "Y-m-d H:i:S",
        time_24hr: <?= ($timeFormat === '24h') ? 'true' : 'false' ?>,
        allowInput: true
    });
</script>
<?= $this->endSection() ?>
