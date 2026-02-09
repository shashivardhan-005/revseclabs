<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Assignments<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Quiz Assignments</h3>
        <div class="actions">
            <button type="button" class="btn btn-outline-danger d-flex align-items-center" id="bulkDeleteBtn" disabled>
                <i class="bi bi-trash me-2"></i> Bulk Delete
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form action="<?= base_url('admin/assignments') ?>" method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="quiz_id" class="form-select">
                <option value="">All Quizzes</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= $quiz['id'] ?>" <?= $filters['quiz_id'] == $quiz['id'] ? 'selected' : '' ?>><?= esc($quiz['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="ASSIGNED" <?= $filters['status'] == 'ASSIGNED' ? 'selected' : '' ?>>Assigned</option>
                <option value="STARTED" <?= $filters['status'] == 'STARTED' ? 'selected' : '' ?>>Started</option>
                <option value="COMPLETED" <?= $filters['status'] == 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="retest" value="1" id="retestCheck" <?= $filters['retest'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="retestCheck">
                    Retest Only
                </label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
        <?php if (!empty($filters['status']) || !empty($filters['quiz_id']) || $filters['retest']): ?>
            <div class="col-md-1">
                <a href="<?= base_url('admin/assignments') ?>" class="btn btn-link text-decoration-none">Clear</a>
            </div>
        <?php endif; ?>
    </form>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form id="bulkActionForm" method="post">
        <?= csrf_field() ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>User</th>
                        <th>Quiz</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Assigned At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($assignments as $row): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="assignment_ids[]" value="<?= $row['id'] ?>" class="form-check-input assignment-checkbox">
                        </td>
                        <td><?= esc($row['email']) ?></td>
                        <td><?= esc($row['quiz_name']) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 'COMPLETED' ? 'bg-success' : ($row['status'] == 'STARTED' ? 'bg-primary' : 'bg-secondary') ?>">
                                <?= esc($row['status']) ?>
                            </span>
                        </td>
                        <td class="fw-bold">
                            <?= $row['score'] !== null ? $row['score'] . '%' : '-' ?>
                        </td>
                        <td class="small"><?= date('M d, Y H:i', strtotime($row['assigned_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if ($row['retest_requested']): ?>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="manageRetest('<?= $row['id'] ?>', '<?= esc($row['full_name'] ?? 'User') ?>', '<?= base_url('admin/assignments/approve-retest/'.$row['id']) ?>', '<?= base_url('admin/assignments/reject-retest/'.$row['id']) ?>')">
                                        Retest Request
                                    </button>
                                <?php endif; ?>

                                <a href="#" class="btn btn-outline-danger btn-sm ms-2" onclick="confirmModal('<?= base_url('admin/assignments/delete/'.$row['id']) ?>', 'Are you sure you want to delete this assignment?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links('default', 'bootstrap_full') ?>
    </div>
</div>

<!-- Retest Management Modal -->
<div class="modal fade" id="retestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Retest Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>User <strong><span id="retestUser"></span></strong> has requested a retest.</p>
                <p class="text-muted small">Approving will reset the quiz progress and score, allowing the user to start fresh. Rejecting will remove the request flag but keep current progress.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnRejectRetest" onclick="submitRetestAction(this.dataset.url)">Reject Request</button>
                <button type="button" class="btn btn-primary" id="btnApproveRetest" onclick="submitRetestAction(this.dataset.url)">Approve Retest</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkActionForm = document.getElementById('bulkActionForm');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.assignment-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    function updateButtons() {
        const checkedCount = document.querySelectorAll('.assignment-checkbox:checked').length;
        bulkDeleteBtn.disabled = checkedCount === 0;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateButtons();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButtons);
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            bulkActionForm.action = '<?= base_url('admin/assignments/bulk-delete') ?>';
            confirmFormSubmit(bulkActionForm, 'Are you sure you want to delete the selected assignments?');
        });
    }
});

function manageRetest(id, userName, approveUrl, rejectUrl) {
    document.getElementById('retestUser').textContent = userName;
    document.getElementById('btnApproveRetest').dataset.url = approveUrl;
    document.getElementById('btnRejectRetest').dataset.url = rejectUrl;
    
    new bootstrap.Modal(document.getElementById('retestModal')).show();
}

function submitRetestAction(url) {
    // Create a temporary form to submit properly via POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= csrf_token() ?>';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
<?= $this->endSection() ?>
