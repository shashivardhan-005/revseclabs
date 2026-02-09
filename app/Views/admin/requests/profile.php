<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Profile Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Pending Profile Updates</h3>
        <span class="badge bg-warning text-dark"><?= count($requests) ?> Pending</span>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Requested At</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td>
                        <div class="fw-bold"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></div>
                        <div class="text-muted small">ID: <?= $r['user_id'] ?></div>
                    </td>
                    <td>
                        <div class="text-muted"><?= date('M d, Y H:i', strtotime($r['requested_at'])) ?></div>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="viewRequestDetails(<?= htmlspecialchars(json_encode($r)) ?>)">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">No pending requests.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed View Modal -->
<div class="modal fade" id="requestDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Review Profile Change</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Full Name</label>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light border">
                        <div id="modal-name-current" class="text-muted">Current</div>
                        <i class="bi bi-arrow-right text-primary mx-3"></i>
                        <div id="modal-name-new" class="fw-bold text-dark">Requested</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Department</label>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light border">
                        <div id="modal-dept-current" class="text-muted">Current</div>
                        <i class="bi bi-arrow-right text-primary mx-3"></i>
                        <div id="modal-dept-new" class="fw-bold text-dark">Requested</div>
                    </div>
                </div>

                <div class="alert alert-info py-2 small mb-0">
                    <i class="bi bi-info-circle me-2"></i> Review the changes carefully before approving.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">Cancel</button>
                
                <form id="reject-form" action="" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-danger px-4" onclick="confirmFormSubmit(this.form, 'Are you sure you want to REJECT this profile update?');">Reject</button>
                </form>

                <form id="approve-form" action="" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-success px-4" onclick="confirmFormSubmit(this.form, 'Are you sure you want to APPROVE this profile update?');">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
function viewRequestDetails(request) {
    const modal = new bootstrap.Modal(document.getElementById('requestDetailModal'));
    
    // Set Names
    document.getElementById('modal-name-current').textContent = request.first_name + ' ' + request.last_name;
    document.getElementById('modal-name-new').textContent = request.new_full_name || (request.first_name + ' ' + request.last_name);
    
    // Highlight if name changed
    if (request.new_full_name) {
        document.getElementById('modal-name-new').classList.add('text-primary');
    } else {
        document.getElementById('modal-name-new').classList.remove('text-primary');
    }

    // Set Departments
    document.getElementById('modal-dept-current').textContent = request.current_dept || 'N/A';
    document.getElementById('modal-dept-new').textContent = request.new_department || (request.current_dept || 'N/A');
    
    // Highlight if dept changed
    if (request.new_department) {
        document.getElementById('modal-dept-new').classList.add('text-primary');
    } else {
        document.getElementById('modal-dept-new').classList.remove('text-primary');
    }

    // Set Form Actions
    document.getElementById('approve-form').action = '<?= base_url('admin/profile-requests/approve/') ?>/' + request.id;
    document.getElementById('reject-form').action = '<?= base_url('admin/profile-requests/reject/') ?>/' + request.id;

    modal.show();
}
</script>
<?= $this->endSection() ?>
