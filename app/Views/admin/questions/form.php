<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?><?= $question ? 'Edit' : 'Create' ?> Question<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <h3 class="card-title mb-4"><?= $question ? 'Edit Question' : 'Create New Question' ?></h3>
    
    <form action="<?= base_url('admin/questions/save') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if ($question): ?>
            <input type="hidden" name="id" value="<?= $question['id'] ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="mb-4">
                    <label class="form-label fw-bold">Question Text</label>
                    <textarea name="text" class="form-control" rows="3" required><?= $question ? esc($question['text']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Explanation (Optional)</label>
                    <textarea name="explanation" class="form-control" rows="2"><?= $question ? esc($question['explanation']) : '' ?></textarea>
                    <div class="form-text small">This is shown to users after submission.</div>
                </div>

                <!-- OPTIONS MANAGEMENT -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Answer Options</label>
                    <div id="options-container">
                        <?php if (empty($options)): ?>
                            <div class="option-row d-flex gap-2 mb-2 align-items-center">
                                <input type="radio" name="correct_option" value="0" checked>
                                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                            </div>
                            <div class="option-row d-flex gap-2 mb-2 align-items-center">
                                <input type="radio" name="correct_option" value="1">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
                            </div>
                        <?php else: ?>
                            <?php foreach ($options as $index => $opt): ?>
                                <div class="option-row d-flex gap-2 mb-2 align-items-center">
                                    <input type="radio" name="correct_option" value="<?= $index ?>" <?= $opt['is_correct'] ? 'checked' : '' ?>>
                                    <input type="text" name="options[]" class="form-control" value="<?= esc($opt['text']) ?>" required>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="add-option" class="btn btn-outline-secondary btn-sm mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Add Another Option
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Classification</h5>
                        <div class="mb-3">
                            <label class="form-label">Topic</label>
                            <select name="topic_id" class="form-select" required>
                                <?php foreach ($topics as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= ($question && isset($question['topic_id']) && $question['topic_id'] == $t['id']) ? 'selected' : '' ?>><?= $t['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="question_type" class="form-select">
                                <option value="MCQ" <?= ($question && isset($question['question_type']) && $question['question_type'] == 'MCQ') ? 'selected' : '' ?>>Multiple Choice</option>
                                <option value="TF" <?= ($question && isset($question['question_type']) && $question['question_type'] == 'TF') ? 'selected' : '' ?>>True/False</option>
                                <option value="SCENARIO" <?= ($question && isset($question['question_type']) && $question['question_type'] == 'SCENARIO') ? 'selected' : '' ?>>Scenario Based</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="EASY" <?= ($question && isset($question['difficulty']) && $question['difficulty'] == 'EASY') ? 'selected' : '' ?>>Easy</option>
                                <option value="MEDIUM" <?= (!$question || (isset($question['difficulty']) && $question['difficulty'] == 'MEDIUM')) ? 'selected' : '' ?>>Medium</option>
                                <option value="HARD" <?= ($question && isset($question['difficulty']) && $question['difficulty'] == 'HARD') ? 'selected' : '' ?>>Hard</option>
                            </select>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Attachment Image</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                            <?php if (isset($question['image_base64']) && $question['image_base64']): ?>
                                <div class="mt-2 text-center p-2 border rounded bg-white">
                                    <img src="<?= $question['image_base64'] ?>" class="img-fluid rounded" style="max-height: 100px;">
                                    <div class="small text-muted mt-1">Current Image</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-5">
            <a href="<?= base_url('admin/questions') ?>" class="btn btn-light px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5">Save Question</button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-option').addEventListener('click', function() {
    const container = document.getElementById('options-container');
    const count = container.children.length;
    const row = document.createElement('div');
    row.className = 'option-row d-flex gap-2 mb-2 align-items-center';
    row.innerHTML = `
        <input type="radio" name="correct_option" value="${count}">
        <input type="text" name="options[]" class="form-control" placeholder="Option ${count + 1}" required>
        <button type="button" class="btn btn-sm btn-outline-danger remove-opt"><i class="bi bi-x"></i></button>
    `;
    container.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-opt')) {
        e.target.closest('.option-row').remove();
        // Re-index radios
        document.querySelectorAll('input[name="correct_option"]').forEach((radio, index) => {
            radio.value = index;
        });
    }
});
</script>
<?= $this->endSection() ?>
