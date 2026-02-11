<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Assignments<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">Quiz Assignments</h3>
        <div class="actions d-flex gap-2">
            <button type="button" class="btn btn-outline-primary d-flex align-items-center" onclick="downloadPDF()">
                <i class="bi bi-file-earmark-pdf me-2"></i> Download PDF
            </button>
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
                <option value="INCOMPLETE" <?= $filters['status'] == 'INCOMPLETE' ? 'selected' : '' ?>>Incomplete</option>
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
            <table class="table table-hover align-middle" id="assignmentsTable">
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
                            <?php
                                $badgeClass = 'bg-secondary';
                                if ($row['status'] == 'COMPLETED') $badgeClass = 'bg-success';
                                elseif ($row['status'] == 'STARTED') $badgeClass = 'bg-primary';
                                elseif ($row['status'] == 'INCOMPLETE') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>">
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

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<!-- Dependencies for PDF Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    if (!jsPDF) {
        alert('PDF library not loaded yet. Please try again in a moment.');
        return;
    }
    const doc = new jsPDF();

    // Get table headers (excluding checkbox and actions)
    const headers = [['User', 'Quiz', 'Status', 'Score', 'Assigned At']];
    
    // Get visible rows (respecting current filter)
    const rows = [];
    const tableRows = document.querySelectorAll('#assignmentsTable tbody tr');
    
    tableRows.forEach(tr => {
        // Skip empty message row
        if (tr.querySelector('td[colspan]')) return;
        
        const tds = tr.querySelectorAll('td');
        // Indexes: 1=Email, 2=Quiz, 3=Status (text), 4=Score, 5=Date
        if (tds.length >= 6) {
            const rowData = [
                tds[1].innerText.trim(),
                tds[2].innerText.trim(),
                tds[3].innerText.trim(),
                tds[4].innerText.trim(),
                tds[5].innerText.trim()
            ];
            rows.push(rowData);
        }
    });

    if (rows.length === 0) {
        alert('No data available to export.');
        return;
    }

    // Header
    doc.setFontSize(16);
    doc.text("Assignments Report", 14, 15);
    
    // Metadata
    doc.setFontSize(10);
    const now = new Date();
    doc.text(`Generated on: ${now.toLocaleString()}`, 14, 22);

    // Filter Info (Optional context)
    const statusFilter = document.querySelector('select[name="status"]').value;
    const quizFilter = document.querySelector('select[name="quiz_id"] option:checked').text;
    if (statusFilter || quizFilter !== "All Quizzes") {
         let filterText = `Filters: ${quizFilter !== "All Quizzes" ? "Quiz: " + quizFilter : ""} ${statusFilter ? "- Status: " + statusFilter : ""}`;
         doc.text(filterText, 14, 28);
    }

    doc.autoTable({
        head: headers,
        body: rows,
        startY: 35,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185] },
        styles: { fontSize: 9, cellPadding: 3 },
        alternateRowStyles: { fillColor: [245, 245, 245] }
    });

    // Timestamped filename
    const dateStr = now.toISOString().slice(0,19).replace(/[:T]/g, '-');
    doc.save(`Assignments_Report_${dateStr}.pdf`);
}
</script>
<?= $this->endSection() ?>
