<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Active Users</h3>
        <div class="actions d-flex gap-2">
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-person-plus me-2"></i> Add User
            </a>
            <a href="<?= base_url('admin/users/import') ?>" class="btn btn-outline-primary d-flex align-items-center">
                <i class="bi bi-file-earmark-arrow-up me-2"></i> Import CSV
            </a>
            <button type="button" class="btn btn-outline-danger d-flex align-items-center" id="bulkDeleteBtn" disabled>
                <i class="bi bi-trash me-2"></i> Bulk Delete
            </button>
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#bulkAssignModal" id="bulkAssignBtn" disabled>
                <i class="bi bi-person-check me-2"></i> Bulk Assign Quiz
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form action="<?= base_url('admin/users') ?>" method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name or email..." value="<?= esc($filters['search']) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= esc($dept) ?>" <?= $filters['department'] == $dept ? 'selected' : '' ?>><?= esc($dept) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
        <?php if (!empty($filters['search']) || !empty($filters['department'])): ?>
            <div class="col-md-1">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-link text-decoration-none">Clear</a>
            </div>
        <?php endif; ?>
    </form>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
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
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users found matching your criteria.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>" class="form-check-input user-checkbox">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                    <?= strtoupper(substr($user['email'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                    <div class="text-muted small"><?= esc($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($user['department']) ?: '-' ?></td>
                        <td>
                            <span class="badge <?= $user['is_staff'] ? 'bg-info text-dark' : 'bg-secondary' ?>">
                                <?= $user['is_staff'] ? 'Staff' : 'User' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?= base_url('admin/users/edit/'.$user['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger btn-sm" onclick="confirmModal('<?= base_url('admin/users/delete/'.$user['id']) ?>', 'Are you sure you want to delete this user? This will also remove their quiz attempts.')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Bulk Assign Modal -->
        <div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Quiz to Selected Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="selectionCount" class="fw-bold text-primary mb-3">0 users selected</p>
                        <div class="mb-3">
                            <label class="form-label">Select Quiz</label>
                            <select name="quiz_id" class="form-select">
                                <option value="">-- Choose a Quiz --</option>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <option value="<?= $quiz['id'] ?>"><?= esc($quiz['name']) ?> (<?= date('M d', strtotime($quiz['start_time'])) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmAssignBtn">Confirm Assignment</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkActionForm = document.getElementById('bulkActionForm');
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectionCountText = document.getElementById('selectionCount');
    const confirmAssignBtn = document.getElementById('confirmAssignBtn');

    function updateBulkButtons() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        bulkAssignBtn.disabled = checkedCount === 0;
        bulkDeleteBtn.disabled = checkedCount === 0;
        selectionCountText.textContent = `${checkedCount} users selected`;
    }

    selectAll.addEventListener('change', function() {
        userCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkButtons();
    });

    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButtons);
    });

    // Handle Bulk Delete
    bulkDeleteBtn.addEventListener('click', function() {
        bulkActionForm.action = '<?= base_url('admin/users/bulk-delete') ?>';
        confirmFormSubmit(bulkActionForm, 'Are you sure you want to delete the selected users? This cannot be undone.');
    });

    // Handle Bulk Assign (within modal)
    confirmAssignBtn.addEventListener('click', function() {
        const quizId = bulkActionForm.querySelector('select[name="quiz_id"]').value;
        if (!quizId) {
            alert('Please select a quiz.');
            return;
        }
        bulkActionForm.action = '<?= base_url('admin/users/bulk-assign') ?>';
        bulkActionForm.submit();
    });
});
</script>
<?= $this->endSection() ?>
